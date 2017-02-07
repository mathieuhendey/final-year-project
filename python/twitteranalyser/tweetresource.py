# Author: Mathieu Hendey <mhendey01@qub.ac.uk>
# Source: https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
# Part of the AJ02 project supervised by Anna Jurek

"""Represents the '/tweets' endpoint of the API."""

from json import dumps
from logging import critical as log
from time import time
from urllib.parse import unquote

from dataset import connect
import falcon
import tweepy

import constants
from twitteranalyserstreamlistener import StreamListener


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
        self.stream_listener = StreamListener()
        self.stream_listener.tweet_table = self.database[constants.TWEET_TABLE]
        self.stream = tweepy.Stream(
            auth=self.api.auth,
            listener=self.stream_listener
        )
        log('Ready to handle requests.')

    def on_get(self, req: falcon.Request, resp: falcon.Response):
        """Handles GET requests.

        Uses query parameters from the request to initialise a Tweepy stream
        asynchronously, so that returning the response to the client is not
        blocked while the stream is running.Ø
        """

        # Twitter allows only one stream to be running at once.
        if not self.stream.running:
            log('Starting stream.')

            # Get query parameters from request.
            filter_term = req.get_param(constants.REQUEST_TERM_PARAM)
            filter_type = req.get_param(constants.REQUEST_TYPE_PARAM)
            filter_exec_time = req.get_param_as_int(constants.REQUEST_EXEC_TIME_PARAM)
            filter_number = req.get_param_as_int(constants.REQUEST_EXEC_NUMBER_PARAM)

            # Convert %20s into actual spaces (' ').
            filter_term = unquote(filter_term)

            # Initialise stream constraint counters.
            self.stream.listener.start = time()
            self.stream.listener.num_tweets = 0

            # Set stream constraints.
            self.stream_listener.max_exec_time = filter_exec_time
            self.stream_listener.max_tweets = filter_number

            # If the query is for a user...
            if filter_type == constants.FILTER_TYPE_USER:
                # Check if analysis on the requested user has already been
                # performed. If it has, get the ID of the already existing
                # query, if not, insert a new row into the analysis_user
                # table and get its ID.
                analysis_user = self.tweet_user_table.find_one(term=filter_term)
                if analysis_user is None:
                    analysis_user_id = self.tweet_user_table.insert({'term': filter_term})
                else:
                    analysis_user_id = analysis_user['id']

                # Set the type of query we are performing and the id of the
                # query so the listener knows which field should be populated
                # and what its value should be.
                self.stream_listener.analysis_key_name = constants.TWEET_USER_TABLE_KEY_NAME
                self.stream_listener.analysis_key_value = analysis_user_id

                # Twitter requires that you get a user by their ID, not by
                # their screen name. Therefore we need an extra call to
                # convert the screen name into an ID.
                user = self.api.get_user(filter_term)

                # Check if the requested user actually exists. If not, return
                # 404 not found. If it is a valid user, begin streaming
                # asynchronously and immediately return a 200 OK to the
                # client, with the ID of the user in the body. This is used by
                # the client to find the Tweets associated with the user once
                # they've been stored in our database.
                if user is not None:
                    self.stream.filter(follow=[user.id_str], async=True)
                    resp.body = dumps({'user_id': analysis_user_id})
                    resp.status = falcon.HTTP_OK
                else:
                    resp.status = falcon.HTTP_NOT_FOUND

            # If the query is for a topic...
            elif filter_type == constants.FILTER_TYPE_TOPIC:

                # Check if analysis on the requested topic has already been
                # performed. If it has, get the ID of the already existing
                # query, if not, insert a new row into the analysis_topic
                # table and get its ID.
                analysis_topic = self.tweet_topic_table.find_one(term=filter_term)
                if analysis_topic is None:
                    analysis_topic_id = self.tweet_topic_table.insert({'term': filter_term})
                else:
                    analysis_topic_id = analysis_topic['id']

                # Set the type of query we are performing and the id of the
                # query so the listener knows which field should be populated
                # and what its value should be.
                self.stream_listener.analysis_key_name = constants.TWEET_TOPIC_TABLE_KEY_NAME
                self.stream_listener.analysis_key_value = analysis_topic_id

                # Begin streaming. We only care about Tweets in English so we
                # specify that we only want to receive Tweets in English. Once
                # the stream has been started, return 200 OK with the ID of
                # the topic in the body. This is used by the client to find
                # the Tweets associated with the topic once they've been
                # stored in our database.
                self.stream.filter(track=[filter_term], languages=['en'], async=True)
                resp.body = dumps({'topic_id': analysis_topic_id})
                resp.status = falcon.HTTP_OK

        # Only one stream can be running at once, so if another request comes
        # in before the previous one has been completed, return 409 conflict,
        # letting the client know that their request conflicts with an ongoing
        # one.
        else:
            log('Stream already running.')
            resp.status = falcon.HTTP_CONFLICT
