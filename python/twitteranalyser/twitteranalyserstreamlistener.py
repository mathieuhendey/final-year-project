import logging
import time

import tweepy


class StreamListener(tweepy.StreamListener):
    max_exec_time = 0
    num_tweets = 1
    max_tweets = 0
    start = None
    analysis_key_name = None
    analysis_key_value = None
    tweet_table = None

    def __init__(self):
        super(StreamListener, self).__init__()

    def on_status(self, status):
        if self.num_tweets > self.max_tweets or time.time() > self.start + self.max_exec_time:
            logging.critical('Stream closed due to filter constraints being met')
            return False

        status_dict = {
            'author_screen_name': status.user.screen_name,
            'author_id': status.user.id_str,
            'in_reply_to_user_id': getattr(status, 'in_reply_to_user_id_str', None),
            'in_reply_to_screen_name': getattr(status, 'in_reply_to_screen_name', None),
            'in_reply_to_status_id': getattr(status, 'in_reply_to_status_id_str', None),
            'tweet_id': status.id_str,
            'tweet_text': status.text,
            self.analysis_key_name: self.analysis_key_value,
        }

        self.tweet_table.insert(status_dict)
        self.num_tweets += 1

    def on_error(self, status_code):
        logging.critical(status_code)
        if status_code == 420:
            logging.critical('Rate limited!')
