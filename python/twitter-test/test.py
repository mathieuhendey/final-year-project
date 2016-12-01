import falcon
from os import environ
from celery import Celery
import tweepy
import time
import dataset

TWITTER_APP_KEY = '8QIz3eR8jPgzGbyl6kzw'
TWITTER_APP_SECRET = 'uDaycgKRCHRIilZFdkvZznJCWGZqcvZ6t9aZeo1GiI'
TWITTER_KEY = '69321956-8hNBZHTC48vrXgcfyJkE81MgpUrv46r2GdevAMsF4'
TWITTER_SECRET = 'mbINiYnlWxaxxoghhIwjGib2baZ9u21qiODXJxUUCwy0K'

endpoint = application = falcon.API()
celery = Celery('twitter_sentiment', broker=environ.get('AMPQ_ADDRESS'))


class StreamListener(tweepy.StreamListener):
    MAX_EXEC_TIME = 300

    def __init__(self):
        super(StreamListener, self).__init__()
        self.num_tweets = 0
        self.start = time.time()
        self.db = dataset.connect('mysql+pymysql://root:root@database/twitter_analyser?charset=utf8&use_unicode=1')
        self.table = self.db['tweets']

    def on_status(self, status):
        if self.num_tweets > 200 or time.time() > self.start + self.MAX_EXEC_TIME:
            return False
        self.table.insert(dict(status_text=status.text.encode('latin-1', 'ignore')))
        print(status.text)
        self.num_tweets += 1

    def on_error(self, status_code):
        if status_code == 420:
            print('Rate limited!')


class Tweet(object):

    def __init__(self):
        self.auth = tweepy.OAuthHandler(TWITTER_APP_KEY, TWITTER_APP_SECRET)
        self.auth.set_access_token(TWITTER_KEY, TWITTER_SECRET)
        self.api = tweepy.API(self.auth)
        self.stream_listener = StreamListener()
        self.stream = tweepy.Stream(auth=self.api.auth, listener=self.stream_listener)

    def on_get(self, req, resp):
        if not self.stream.running:
            self.stream_listener.num_tweets = 0
            self.stream.filter(track=["trump", "clinton", "hillary clinton", "donald trump"])
            resp.status = falcon.HTTP_200
            resp.body = "Processing..."
        else:
            resp.status = falcon.HTTP_429

tweets = Tweet()
endpoint.add_route('/tweets', tweets)
