import json
from urllib import parse
import logging
import time

import falcon
import tweepy
import dataset

from twitteranalyserstreamlistener import StreamListener

TWITTER_APP_KEY = 'GpFmKlYeKJ2WoAuPydGqXDnZW'
TWITTER_APP_SECRET = 'mLste69X6FOqNwcAw8b6tcI8m2avhnl8QYpjUXlsd5mncW6CGE'
TWITTER_KEY = '69321956-eExmX9kaFmtutwXrrn5nfuu8p2n1bxZtXjhScUV82'
TWITTER_SECRET = 'a832WpLH7ERGpAqhtt6K4Nj07hI83LDXaOZZxp7QhjMaM'

DB_URL = 'mysql+pymysql://root:root@database/twitter_analyser?charset=utf8mb4'
TWEET_TOPIC_TABLE = 'analysis_topic'
TWEET_USER_TABLE = 'analysis_user'
TWEET_TABLE = 'tweet'
TWEET_TOPIC_TABLE_KEY_NAME = 'analysis_topic_id'
TWEET_USER_TABLE_KEY_NAME = 'analysis_user_id'

REQUEST_TYPE_PARAM = 'type'
REQUEST_TERM_PARAM = 'term'
REQUEST_EXEC_TIME_PARAM = 'exec_time'
REQUEST_EXEC_NUMBER_PARAM = 'exec_number'

FILTER_TYPE_USER = 'user'
FILTER_TYPE_TOPIC = 'topic'


class Tweet(object):
    auth = tweepy.OAuthHandler(TWITTER_APP_KEY, TWITTER_APP_SECRET)
    auth.set_access_token(TWITTER_KEY, TWITTER_SECRET)
    api = tweepy.API(auth)
    db = dataset.connect(DB_URL)
    tweet_topic_table = db[TWEET_TOPIC_TABLE]
    tweet_user_table = db[TWEET_USER_TABLE]
    tweet_table = db[TWEET_TABLE]
    stream_listener = StreamListener()
    stream_listener.tweet_table = tweet_table
    stream = tweepy.Stream(auth=api.auth, listener=stream_listener)
    logging.critical('Ready!')

    def on_get(self, req, resp):
        if not self.stream.running:
            logging.critical('Starting stream...')
            filter_term = req.get_param(REQUEST_TERM_PARAM)
            filter_type = req.get_param(REQUEST_TYPE_PARAM)
            filter_term = parse.unquote(filter_term)
            filter_exec_time = req.get_param_as_int(REQUEST_EXEC_TIME_PARAM)
            filter_number = req.get_param_as_int(REQUEST_EXEC_NUMBER_PARAM)

            self.stream.listener.start = time.time()
            self.stream.listener.num_tweets = 0
            self.stream_listener.max_exec_time = filter_exec_time
            self.stream_listener.max_tweets = filter_number

            if filter_type == FILTER_TYPE_USER:
                analysis_user = self.tweet_user_table.find_one(term=filter_term)
                if analysis_user is None:
                    analysis_user_id = self.tweet_user_table.insert({'term': filter_term})
                else:
                    analysis_user_id = analysis_user['id']
                self.stream_listener.analysis_key_name = TWEET_USER_TABLE_KEY_NAME
                self.stream_listener.analysis_key_value = analysis_user_id
                user = self.api.get_user(filter_term)
                if user is not None:
                    self.stream.filter(follow=[user.id_str], async=True)
                    resp.body = json.dumps({'user_id': analysis_user_id})
                    resp.status = falcon.HTTP_OK
                else:
                    resp.status = falcon.HTTP_NOT_FOUND

            elif filter_type == FILTER_TYPE_TOPIC:
                analysis_topic = self.tweet_topic_table.find_one(term=filter_term)
                if analysis_topic is None:
                    analysis_topic_id = self.tweet_topic_table.insert({'term': filter_term})
                else:
                    analysis_topic_id = analysis_topic['id']
                self.stream_listener.analysis_key_name = TWEET_TOPIC_TABLE_KEY_NAME
                self.stream_listener.analysis_key_value = analysis_topic_id
                self.stream.filter(track=[filter_term], languages=["en"], async=True)
                resp.body = json.dumps({'topic_id': analysis_topic_id})
                resp.status = falcon.HTTP_OK

        else:
            logging.critical('Stream already running')
            resp.status = falcon.HTTP_CONFLICT
