# Author: Mathieu Hendey <mhendey01@qub.ac.uk>
# Source: https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
# Part of the AJ02 project supervised by Anna Jurek

"""Represents the '/tweets' endpoint of the API."""

from json import dumps
from logging import critical as log
from os import environ
from time import time
from urllib.parse import unquote
from datetime import datetime
import pytz

import falcon
import pika
import tweepy
from dataset import connect
from pika.channel import Channel
from sqlalchemy.exc import IntegrityError

from twitteranalyser import constants
from twitteranalyser.twitteranalyserstreamlistener import StreamListener


class Tweet(object):
    """Provides methods that are fired depending on HTTP method."""

    def __init__(self):
        self.auth = tweepy.OAuthHandler(
            constants.TWITTER_APP_KEY,
            constants.TWITTER_APP_SECRET
        )

        self.auth.set_access_token(
            constants.TWITTER_KEY,
            constants.TWITTER_SECRET
        )

        self.api = tweepy.API(self.auth)
        self.database = connect(constants.DB_URL)
        self.tweet_topic_table = self.database[constants.TWEET_TOPIC_TABLE]
        self.tweet_user_table = self.database[constants.TWEET_USER_TABLE]
        self.current_analyses_table = self.database[constants.CURRENT_ANALYSES_TABLE]
        self.stream_listener = StreamListener()
        self.stream_listener.tweet_table = self.database[constants.TWEET_TABLE]
        self.stream_listener.current_analyses_table = self.current_analyses_table
        self.stream = tweepy.Stream(
            auth=self.api.auth,
            listener=self.stream_listener
        )
        log('Ready to handle requests.')

    def on_get(self, req: falcon.Request, resp: falcon.Response):
        """Handles GET requests.

        Uses query parameters from the request to initialise a Tweepy stream
        asynchronously, so that returning the response to the client is not
        blocked while the stream is running.
        """
        # Get query parameters from request.
        filter_term = req.get_param(constants.REQUEST_TERM_PARAM)
        filter_type = req.get_param(constants.REQUEST_TYPE_PARAM)
        filter_exec_time = req.get_param_as_int(
            constants.REQUEST_EXEC_TIME_PARAM)
        filter_number = req.get_param_as_int(
            constants.REQUEST_EXEC_NUMBER_PARAM)
        should_reanalyse = req.get_param_as_bool(
            constants.REQUEST_SHOULD_REANALYSE_PARAM)

        # Convert %20s into actual spaces (' ').
        filter_term = unquote(filter_term)

        if not self.stream.running:
            # Initialise stream constraint counters.
            self.stream.listener.start = time()
            self.stream.listener.num_tweets = 0

            # Set stream constraints.
            self.stream_listener.max_exec_time = filter_exec_time
            self.stream_listener.max_tweets = filter_number

            # Pass Rabbit channel.
            self.stream_listener.channel = self.get_rabbit_channel()

            self.stream_listener.streaming_hashtag = False

        # If the query is for a user...
        if filter_type == constants.FILTER_TYPE_USER:
            # Check if analysis on the requested user has already been
            # performed. If it has, get the ID of the already existing
            # query, if not, insert a new row into the analysis_user
            # table and get its ID.
            user = self.api.get_user(screen_name=filter_term)
            analysis_user_id = 0
            already_analysed = False
            if not user:
                resp.status = falcon.HTTP_NOT_FOUND
            analysis_user = self.tweet_user_table.find_one(
                twitter_id=user.id_str)
            if not analysis_user:
                if not self.stream.running:
                    data = {
                        'author_screen_name': user.screen_name,
                        'twitter_id': user.id
                    }
                    try:
                        analysis_user_id = self.tweet_user_table.insert(data)
                    except IntegrityError:
                        log('Already saved')
                else:
                    log(self.stream_listener.max_exec_time)
                    resp.body = dumps({'time_left_on_stream': int(
                        self.stream_listener.time_left_on_stream), 'rate_limited': True})
                    resp.status = falcon.HTTP_CONFLICT
            else:
                analysis_user_id = analysis_user['id']
                already_analysed = True

            if not self.stream.running:
                # Set the type of query we are performing and the id of the
                # query so the listener knows which field should be populated
                # and what its value should be.
                self.stream_listener.analysis_key_name = constants.TWEET_USER_TABLE_KEY_NAME
                self.stream_listener.analysis_key_value = analysis_user_id

            if already_analysed:
                if should_reanalyse:
                    if not self.stream.running:
                        log('Starting stream.')
                        data = {
                            'analysis_user_id': analysis_user_id
                        }
                        current_analysis_id = self.current_analyses_table.insert(
                            data)
                        update_data = dict(id=analysis_user_id, updated_on=datetime.now(pytz.timezone('Europe/London')))
                        self.tweet_user_table.update(update_data, ['id'])
                        self.stream_listener.current_analysis_id = current_analysis_id
                        self.stream.filter(follow=[user.id_str], async=True)
                        resp.body = dumps({'user_id': analysis_user_id,
                                           'already_analysed': already_analysed,
                                           'currently_analysing': True})
                        resp.status = falcon.HTTP_OK
                    else:
                        log(self.stream_listener.max_exec_time)
                        resp.body = dumps({'time_left_on_stream': int(
                            self.stream_listener.time_left_on_stream), 'rate_limited': True})
                        resp.status = falcon.HTTP_CONFLICT
                else:
                    resp.body = dumps({'user_id': analysis_user_id,
                                       'already_analysed': already_analysed,
                                       'currently_analysing': False})
                    resp.status = falcon.HTTP_OK
            else:
                if not self.stream.running:
                    log('Starting stream.')
                    data = {
                        'analysis_topic_id': analysis_user_id
                    }
                    current_analysis_id = self.current_analyses_table.insert(
                        data)
                    self.stream_listener.current_analysis_id = current_analysis_id
                    self.stream.filter(follow=[user.id_str], async=True)
                    resp.body = dumps({'user_id': analysis_user_id,
                                       'already_analysed': False, 'currently_analysing': True})
                    resp.status = falcon.HTTP_OK
                else:
                    log(self.stream_listener.max_exec_time)
                    resp.body = dumps({'time_left_on_stream': int(
                        self.stream_listener.time_left_on_stream), 'rate_limited': True})
                    resp.status = falcon.HTTP_CONFLICT

        # If the query is for a topic...
        elif filter_type == constants.FILTER_TYPE_TOPIC:

            # Check if analysis on the requested topic has already been
            # performed. If it has, get the ID of the already existing
            # query, if not, insert a new row into the analysis_topic
            # table and get its ID.
            analysis_topic_id = 0
            already_analysed = False
            is_hashtag = False
            if filter_term[0] == '#':
                is_hashtag = True
                analysis_topic = self.tweet_topic_table.find_one(
                    term=filter_term[1:], is_hashtag=1)
            else:
                analysis_topic = self.tweet_topic_table.find_one(
                    term=filter_term, is_hashtag=0)

            if analysis_topic is None:
                if filter_term[0] == '#':
                    is_hashtag = True
                    filter_term = filter_term[1:]
                    if not self.stream.running:
                        self.stream_listener.streaming_hashtag = True
                        self.stream_listener.search_term = filter_term
                data = {
                    'term': filter_term,
                    'is_hashtag': is_hashtag
                }
                try:
                    analysis_topic_id = self.tweet_topic_table.insert(data)
                except IntegrityError:
                    log('Already saved')
            else:
                analysis_topic_id = analysis_topic['id']
                already_analysed = True

            if not self.stream.running:
                # Set the type of query we are performing and the id of the
                # query so the listener knows which field should be populated
                # and what its value should be.
                self.stream_listener.analysis_key_name = constants.TWEET_TOPIC_TABLE_KEY_NAME
                self.stream_listener.analysis_key_value = analysis_topic_id

            if already_analysed:
                if should_reanalyse:
                    if not self.stream.running:
                        log('Starting stream.')
                        data = {
                            'analysis_topic_id': analysis_topic_id,
                            'is_hashtag': is_hashtag
                        }
                        update_data = dict(id=analysis_topic_id, updated_on=datetime.now(pytz.timezone('Europe/London')))
                        self.tweet_topic_table.update(update_data, ['id'])
                        current_analysis_id = self.current_analyses_table.insert(
                            data)
                        self.stream_listener.current_analysis_id = current_analysis_id
                        self.stream.filter(
                            track=[filter_term], languages=['en'], async=True)
                        resp.body = dumps({'topic_id': analysis_topic_id,
                                           'is_hashtag': is_hashtag,
                                           'already_analysed': already_analysed,
                                           'currently_analysing': True})
                        resp.status = falcon.HTTP_OK
                    else:
                        log(self.stream_listener.max_exec_time)
                        resp.body = dumps({'time_left_on_stream': int(
                            self.stream_listener.time_left_on_stream), 'rate_limited': True})
                        resp.status = falcon.HTTP_CONFLICT
                else:
                    resp.body = dumps({'topic_id': analysis_topic_id,
                                       'is_hashtag': is_hashtag,
                                       'already_analysed': already_analysed,
                                       'currently_analysing': False})
                    resp.status = falcon.HTTP_OK
            else:
                if not self.stream.running:
                    log('Starting stream.')
                    data = {
                        'analysis_topic_id': analysis_topic_id,
                        'is_hashtag': is_hashtag
                    }
                    current_analysis_id = self.current_analyses_table.insert(
                        data)
                    self.stream_listener.current_analysis_id = current_analysis_id
                    self.stream.filter(
                        track=[filter_term], languages=['en'], async=True)
                    resp.body = dumps({'topic_id': analysis_topic_id,
                                       'is_hashtag': is_hashtag,
                                       'already_analysed': False,
                                       'currently_analysing': True})
                    resp.status = falcon.HTTP_OK
                else:
                    log(self.stream_listener.max_exec_time)
                    resp.body = dumps({'time_left_on_stream': int(
                        self.stream_listener.time_left_on_stream), 'rate_limited': True})
                    resp.status = falcon.HTTP_CONFLICT

    @staticmethod
    def get_rabbit_channel() -> Channel:
        credentials = pika.PlainCredentials(
            username=environ.get(
                'RABBIT_USER', 'rabbit'), password=environ.get(
                    'RABBIT_PASS', 'rabbit'))
        connection = pika.BlockingConnection(
            pika.ConnectionParameters(
                host=environ.get(
                    'RABBIT_HOST', 'rabbit'), port=int(
                        environ.get(
                            'RABBIT_PORT', 5672)), credentials=credentials))
        channel = connection.channel()
        channel.queue_declare(environ.get('RABBIT_QUEUE', 'classifier_queue'))
        return channel
