# Author: Mathieu Hendey <mhendey01@qub.ac.uk>
# Source: https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
# Part of the AJ02 project supervised by Anna Jurek

"""Related to processing events from the Tweepy stream."""

from json import dumps
from logging import critical as log
from os import environ
from time import time
from typing import Union

from datetime import datetime
import pytz

from dataset import Table
from pika.channel import Channel
from tweepy import Status
from tweepy import StreamListener as Listener

from twitteranalyser import constants


class StreamListener(Listener):
    """Listens for events on the Tweepy stream.

    Provides several methods to handle events, such as errors and statuses.
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
        self.current_analyses_table = None  # type: Table
        self.channel = None  # type: Channel
        self._time_left_on_stream = 0
        self.streaming_hashtag = False
        self.search_term = ''
        self.current_analysis_id = None

    @property
    def time_left_on_stream(self) -> float:
        return self.max_exec_time - (time() - self.start)

    @property
    def max_exec_time(self) -> float:
        """Getter for max_exec_time"""
        return self._max_exec_time

    @max_exec_time.setter
    def max_exec_time(self, new_time: float) -> None:
        """Setter for max_exec_time.

        Limit query time to 10 minutes.
        """
        if new_time is None or new_time > 600:
            self._max_exec_time = 600
        else:
            self._max_exec_time = new_time

    @property
    def max_tweets(self) -> int:
        """Getter for max_tweets"""
        return self._max_tweets

    @max_tweets.setter
    def max_tweets(self, tweets_to_get: int) -> None:
        """Setter for max_exec_time.

        Limit number of Tweets to fetch to 10,000 Tweets.
        """
        if tweets_to_get is None or tweets_to_get > 50000:
            self._max_tweets = 50000
        else:
            self._max_tweets = tweets_to_get

    def keep_alive(self):
        if time() >= self.start + self.max_exec_time:
            log('Stream closed due to filter constraints being met.')
            self.current_analyses_table.delete(id=self.current_analysis_id)
            return False

    def on_status(self, status: Status) -> Union[bool, None]:
        """Handler called every time a new status appears on the stream.

        Here, we get information about the Tweet and store it in the database.

        This is also possibly where the call to the analyser will go, although
        Twitter mandates a certain processing speed so analysis may have to be
        done by a cron job.

        If False is returned, the stream is closed. If True is returned, the
        stream remains open and the current Tweet is skipped.
        """
        # Check that we haven't met the specified constraints.
        # Returning false from the handler will close the stream.
        if time() >= self.start + self.max_exec_time:
            log('Stream closed due to filter constraints being met.')
            self.current_analyses_table.delete(id=self.current_analysis_id)
            return False

        # We don't care about retweets.
        if getattr(
                status,
                'retweeted_status',
                False) or 'RT @' in getattr(
                    status,
                    'text',
                    None):
            return True

        # If we're streaming a user, we only care about replies to that user.
        if self.analysis_key_name == constants.TWEET_USER_TABLE_KEY_NAME:
            if getattr(status, 'in_reply_to_user_id_str', None) is None:
                return True

        if self.streaming_hashtag:
            hashtags = []
            for hashtag in getattr(status, 'entities', [])['hashtags']:
                hashtags.append(hashtag['text'])
            if self.search_term not in hashtags:
                return True

        # Create the dictionary that will be inserted into the database.
        status_dict = {
            'author_screen_name': getattr(getattr(status, 'user', None), 'screen_name', None),
            'author_id': getattr(getattr(status, 'user', None), 'id_str', None),
            'in_reply_to_user_id': getattr(status, 'in_reply_to_user_id_str', None),
            'in_reply_to_screen_name': getattr(status, 'in_reply_to_screen_name', None),
            'in_reply_to_status_id': getattr(status, 'in_reply_to_status_id_str', None),
            'tweet_id': getattr(status, 'id_str', None),
            'tweet_text': getattr(status, 'text', None),
            self.analysis_key_name: self.analysis_key_value,
            'created_on': datetime.now(pytz.timezone('Europe/London')),
        }
        table_id = self.tweet_table.insert_ignore(status_dict, ['tweet_id'])
        if table_id:
            status_dict['table_id'] = table_id
            self.channel.basic_publish(
                exchange='',
                routing_key=environ.get(
                    'RABBIT_QUEUE',
                    'classifier_queue'),
                body=dumps(
                    {
                        'tweet_text': status_dict['tweet_text'],
                        'table_id': status_dict['table_id']}))
            self.num_tweets += 1

    def on_error(self, status_code: int) -> bool:
        """If we get an error back from Twitter, log it out.

        420 means that we have been rate limited, but there are various other
        codes that Twitter can return.

        Rate limited is the only one we're likely to encounter.

        Returning False from this method closes the stream.
        """
        log(status_code)
        if status_code == 420:
            log('Rate limited, closing stream.')
        self.current_analyses_table.delete(id=self.current_analysis_id)
        return False
