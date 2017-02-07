# Author: Mathieu Hendey <mhendey01@qub.ac.uk>
# Source: https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
# Part of the AJ02 project supervised by Anna Jurek

"""
Related to processing events from the Tweepy stream.
"""
import logging
import time

import tweepy

import constants


class StreamListener(tweepy.StreamListener):
    """
    The listener which listens for events on the Tweet stream and provides a
    method to process them.
    """

    def __init__(self):
        super(StreamListener, self).__init__()
        self._max_exec_time = 0
        self.num_tweets = 0
        self._max_tweets = 0
        self.start = None
        self.analysis_key_name = None
        self.analysis_key_value = None
        self.tweet_table = None

    @property
    def max_exec_time(self):
        """Getter for max_exec_time"""
        return self._max_exec_time

    @max_exec_time.setter
    def max_exec_time(self, new_time: float):
        """
        Setter for max_exec_time.

        Limit query time to 10 minutes.
        """
        if new_time is None or new_time > 600:
            self._max_exec_time = 600
        else:
            self._max_exec_time = new_time

    @property
    def max_tweets(self):
        """Getter for max_tweets"""
        return self._max_tweets

    @max_tweets.setter
    def max_tweets(self, tweets_to_get: int):
        """
        Setter for max_exec_time.

        Limit number of Tweets to fetch to 10,000 Tweets.
        """
        if tweets_to_get is None or tweets_to_get > 10000:
            self._max_tweets = 10000
        else:
            self._max_tweets = tweets_to_get

    def on_status(self, status):
        """
        Handler called every time a new status appears on the stream.

        Here, we get information about the Tweet and store it in the database.

        This is also possibly where the call to the analyser will go, although
        Twitter mandates a certain processing speed so analysis may have to be
        done by a cron job.
        """

        # Check that we haven't met the specified constraints.
        # Returning false from the handler will close the stream.
        if (self.num_tweets > self.max_tweets or
                time.time() > self.start + self.max_exec_time):
            logging.critical('Stream closed due to filter constraints being met.')
            return False

        # If we're streaming a user, we only care abouut replies to that user.
        if self.analysis_key_name == constants.TWEET_USER_TABLE_KEY_NAME:
            if getattr(status, 'in_reply_to_user_id_str', None) is None:
                return True

        # Create the dictionary that will be inserted into the database.
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

    def on_error(self, status_code: int):
        """
        If we get an error back from Twitter, log it out.

        420 means that we have been rate limited, but there are various other
        codes that Twitter can return.

        Rate limited is the only one we're likely to encounter.
        """
        logging.critical(status_code)
        if status_code == 420:
            logging.critical('Rate limited!')
