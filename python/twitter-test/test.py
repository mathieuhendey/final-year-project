import falcon
import tweepy
import time
import dataset
import logging

TWITTER_APP_KEY = '8QIz3eR8jPgzGbyl6kzw'
TWITTER_APP_SECRET = 'uDaycgKRCHRIilZFdkvZznJCWGZqcvZ6t9aZeo1GiI'
TWITTER_KEY = '69321956-8hNBZHTC48vrXgcfyJkE81MgpUrv46r2GdevAMsF4'
TWITTER_SECRET = 'mbINiYnlWxaxxoghhIwjGib2baZ9u21qiODXJxUUCwy0K'

endpoint = application = falcon.API()


class StreamListener(tweepy.StreamListener):
    MAX_EXEC_TIME = 300
    num_tweets = 0
    start = time.time()

    def __init__(self):
        super(StreamListener, self).__init__()
        self.db = dataset.connect('mysql+pymysql://root:root@database/twitter_analyser?charset=utf8&use_unicode=1')
        self.table = self.db['tweets']

    def on_status(self, status):
        if self.num_tweets > 200 or time.time() > self.start + self.MAX_EXEC_TIME:
            return False
        status_dict = {
            'tweet_id': status.id_str,
            'author': status.user.name.encode('latin-1', 'ignore'),
            'author_id': status.user.id_str,
            'text': status.text.encode('latin-1', 'ignore'),
            'in_reply_to_status_id': getattr(status, 'in_reply_to_status_id_str', None),
            'in_reply_to_user_id': getattr(status, 'in_reply_to_user_id_str', None)
        }
        self.table.insert(status_dict)
        self.num_tweets += 1

    def on_error(self, status_code):
        logging.critical(status_code)
        if status_code == 420:
            logging.critical('Rate limited!')


class Tweet(object):
    auth = tweepy.OAuthHandler(TWITTER_APP_KEY, TWITTER_APP_SECRET)
    auth.set_access_token(TWITTER_KEY, TWITTER_SECRET)
    api = tweepy.API(auth)
    stream_listener = StreamListener()
    stream = tweepy.Stream(auth=api.auth, listener=stream_listener)

    def on_get(self, req, resp):
        if not self.stream.running:
            self.stream.listener.start = time.time()
            self.stream.listener.num_tweets = 0
            self.stream.filter(track=['and'], async=True)
            resp.status = falcon.HTTP_200
        else:
            resp.status = falcon.HTTP_409

tweets = Tweet()
endpoint.add_route('/tweets', tweets)