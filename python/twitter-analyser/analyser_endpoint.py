import logging
from urllib import parse
import time
import json

import falcon
import tweepy
import dataset


TWITTER_APP_KEY = '8QIz3eR8jPgzGbyl6kzw'
TWITTER_APP_SECRET = 'uDaycgKRCHRIilZFdkvZznJCWGZqcvZ6t9aZeo1GiI'
TWITTER_KEY = '69321956-8hNBZHTC48vrXgcfyJkE81MgpUrv46r2GdevAMsF4'
TWITTER_SECRET = 'mbINiYnlWxaxxoghhIwjGib2baZ9u21qiODXJxUUCwy0K'

DB_URL = 'mysql+pymysql://root:root@database/twitter_analyser?charset=utf8&use_unicode=1'
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

endpoint = application = falcon.API()


class StreamListener(tweepy.StreamListener):
    max_exec_time = 0
    num_tweets = 0
    max_tweets = 0
    start = None
    analysis_key_name = None
    analysis_key_value = None
    tweet_table = None

    def __init__(self):
        super(StreamListener, self).__init__()

    def on_status(self, status):
        if self.num_tweets > self.max_tweets or time.time() > self.start + self.max_exec_time:
            logging.info('Stream closed due to filter constraints being met')
            return False

        status_dict = {
            'author_screen_name': status.user.screen_name.encode('latin-1', 'ignore'),
            'author_id': status.user.id_str,
            'in_reply_to_user_id': getattr(status, 'in_reply_to_user_id_str', None),
            'in_reply_to_screen_name': getattr(status, 'in_reply_to_screen_name', None).encode('latin-1', 'ignore'),
            'in_reply_to_status_id': getattr(status, 'in_reply_to_status_id_str', None),
            'tweet_id': status.id_str,
            'tweet_text': status.text.encode('latin-1', 'ignore'),
            self.analysis_key_name: self.analysis_key_value,
        }

        self.tweet_table.insert(status_dict)
        self.num_tweets += 1

    def on_error(self, status_code):
        logging.critical(status_code)
        if status_code == 420:
            logging.critical('Rate limited!')


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
    logging.info('Ready!')

    def on_get(self, req, resp):
        if not self.stream.running:
            logging.info('Starting stream...')
            filter_type = req.get_param(REQUEST_TYPE_PARAM)
            filter_term = parse.unquote(req.get_param(REQUEST_TERM_PARAM))
            filter_exec_time = req.get_param(REQUEST_EXEC_TIME_PARAM)
            filter_number = req.get_param(REQUEST_EXEC_NUMBER_PARAM)

            self.stream.listener.start = time.time()
            self.stream.listener.num_tweets = 0
            self.stream_listener.exec_time = filter_exec_time
            self.stream_listener.max_tweets = filter_number

            if filter_type == FILTER_TYPE_USER:
                analysis_user_id = (self.tweet_user_table.find_one(term = filter_term)['id']
                         or self.tweet_user_table.insert({'term': filter_term}))
                self.stream_listener.analysis_key_name = TWEET_USER_TABLE_KEY_NAME
                self.stream_listener.analysis_key_value = analysis_user_id
                self.stream.filter(follow=[filter_term], async=True)
                resp.body = json.dumps({'user_id': analysis_user_id})

            elif filter_type == FILTER_TYPE_TOPIC:
                analysis_topic_id = (self.tweet_topic_table.find_one(term = filter_term)['id']
                         or self.tweet_topic_table.insert({'term': filter_term}))
                self.stream_listener.analysis_key_name = TWEET_TOPIC_TABLE_KEY_NAME
                self.stream_listener.analysis_key_value = analysis_topic_id
                self.stream.filter(track=[filter_term], async=True)
                resp.body = json.dumps({'topic_id': analysis_topic_id})

            resp.status = falcon.HTTP_OK
        else:
            logging.info('Stream already running')
            resp.status = falcon.HTTP_CONFLICT


tweets = Tweet()
endpoint.add_route('/tweets', tweets)