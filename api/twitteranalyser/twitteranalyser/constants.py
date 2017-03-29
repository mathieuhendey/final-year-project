# Author: Mathieu Hendey <mhendey01@qub.ac.uk>
# Source: https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
# Part of the AJ02 project supervised by Anna Jurek

"""This module contains constants related to the functionality of the app."""

from os.path import join, dirname
from os import environ
from dotenv import load_dotenv

DOTENV_PATH = join(dirname(__file__), '.env')
load_dotenv(DOTENV_PATH)

TWITTER_APP_KEY = environ.get("TWITTER_APP_KEY")
TWITTER_APP_SECRET = environ.get("TWITTER_APP_SECRET")
TWITTER_KEY = environ.get("TWITTER_KEY")
TWITTER_SECRET = environ.get("TWITTER_SECRET")

DB_URL = 'mysql+pymysql://root:root@db/twitter_analyser?charset=utf8mb4'
TWEET_TOPIC_TABLE = 'analysis_topic'
TWEET_USER_TABLE = 'analysis_user'
CURRENT_ANALYSES_TABLE = 'current_analyses'
TWEET_TABLE = 'tweet'
TWEET_TOPIC_TABLE_KEY_NAME = 'analysis_topic_id'
TWEET_USER_TABLE_KEY_NAME = 'analysis_user_id'

REQUEST_TYPE_PARAM = 'type'
REQUEST_TERM_PARAM = 'term'
REQUEST_EXEC_TIME_PARAM = 'exec_time'
REQUEST_EXEC_NUMBER_PARAM = 'exec_number'
REQUEST_SHOULD_REANALYSE_PARAM = 'should_reanalyse'
CURRENT_ANALYSIS_PARAM = 'current_analysis'

FILTER_TYPE_USER = 'user'
FILTER_TYPE_TOPIC = 'topic'
