"""
This module contains code related to extracting features and classifying Tweets
as 'positive' or 'negative'.
"""

import string
import csv
from typing import Union
from math import floor

from re import sub, search
from tweepy import Status

from nltk.corpus import stopwords
from nltk import FreqDist


class TweetPreprocessor(object):
    """
    This class reformats Tweets into a format that makes them easier to deal
    with.

    Specifically, it:
        Lower cases the Tweet
        Strips URLs
        Replaces "#word" with "word"
        Removes punctuation
        Removes usernames
        Cleans whitespace between words and trims at both sides of the Tweet
        Removes stopwords
    """

    def __init__(self):
        self.stopwords = stopwords.words("english")

    def preprocess_tweet(self, tweet: Union[Status, str]) -> str:
        """
        Convert tweet into a format that makes it easier to analyse.

        This involves removing extraneous words, cleaning up whitespace etc.

        This is so that the words match the feature vector, which will
        consist of lowercase words and their associated sentiment.
        """

        if not isinstance(tweet, str):
            tweet = self.reformat_tweet(getattr(tweet, 'status'))
        else:
            tweet = self.reformat_tweet(tweet)

        word_list = tweet.split()
        processed_word_list = []

        for word in word_list:
            word = self.replace_letter_repetitions(word)
            starts_with_alpha = self.is_word_alpha(word)
            if word in self.stopwords or starts_with_alpha:
                continue
            else:
                processed_word_list.append(word)

        tweet = ' '.join(processed_word_list)

        return tweet

    def reformat_tweet(self, tweet: str) -> str:
        """
        Lower case all characters, strip URLs, strip @usernames, replace
        remove octothorpes from hashtags, clean up whitespace, remove
        punctuation.
        """

        tweet = tweet.lower()
        tweet = self.remove_urls(tweet)
        tweet = self.remove_usernames(tweet)
        tweet = self.remove_hash_tags(tweet)
        tweet = self.remove_punctuation(tweet)
        tweet = self.fix_whitespace(tweet)

        return tweet

    def remove_stopwords(self, tweet: str) -> str:
        """
        Remove stopwords (words that do not affect the sentiment of the tweet,
        such as prepositions, articles etc.)
        """

        return ' '.join([word for word in tweet.split() if word not in self.stopwords])

    @staticmethod
    def remove_urls(tweet: str) -> str:
        """
        Remove URLs from the Tweet.
        """

        return sub('((www\.[^\s]+)|(https?://[^\s]+))', '', tweet)

    @staticmethod
    def remove_usernames(tweet: str) -> str:
        """
        Remove words beginning with "@", as usernames have no sentiment value.
        """

        return sub(r'(^|[^@\w])@(\w{1,15})\b', '', tweet)

    @staticmethod
    def remove_hash_tags(tweet: str) -> str:
        """Replace '#word' with '#word'"""

        return sub(r'#([^\s]+)', r'\1', tweet)

    @staticmethod
    def remove_punctuation(tweet: str) -> str:
        """
        Remove all punctuation marks.
        """

        no_punc_translator = str.maketrans("", "", string.punctuation)
        return tweet.translate(no_punc_translator)

    @staticmethod
    def fix_whitespace(tweet: str) -> str:
        """
        Cleans whitespace between words and trims at both sides of the Tweet.
        """

        return sub('\s+', ' ', tweet).strip()

    @staticmethod
    def replace_letter_repetitions(word: str) -> str:
        """
        Replace repitions of the same letter are replaced by one of that
        letter. E.g., 'greeaaat' should become 'great.
        """

        return sub(r'([a-z])\1+', r'\1', word)

    @staticmethod
    def is_word_alpha(word: str) -> bool:
        """
        Check that the passed word begins with an alphabetic character.

        Words that start with numbers are very unlikely to add any value to
        the feature vector.
        """

        result = search(r"^[A-Za-z]*$", word)
        if result is None:
            return False
        return True


class Classifier(object):
    """
    Extracts features from tweets and classifies them based on their
    sentiment as positve, neutral or negative.
    """

    def __init__(self):
        self.labelled_tweets = []  # Pre-labelled tweets from a corpus
        self.training_set = []  # ~80% of the labelled tweets
        self.test_set = []  # ~20% of the labelled tweets
        self.word_features = []  # All feature words ordered by frequency

    # TODO: remove this, used only for testing
    def train(self):
        self.initialise_tweet_sets()
        self.initialise_word_features()

    def initialise_tweet_sets(self):
        """Read labelled data from CSV file."""
        raw_labelled_tweets = csv.reader(open('data/full-corpus.csv'), delimiter=',')
        labelled_tweets = []
        for tweet in raw_labelled_tweets:
            sentiment = tweet[1]
            vector = TweetPreprocessor().preprocess_tweet(tweet[4])
            labelled_tweets.append((vector, sentiment))
        self.labelled_tweets = labelled_tweets
        self.split_training_and_test_sets()

    def initialise_word_features(self):
        """Initialise instance variable containing all words in vector"""
        self.word_features = self.get_word_features(self.get_all_words_in_labelled_tweets(self.training_set))

    def split_training_and_test_sets(self):
        """Split the labelled Tweet set into 80% training and 20% testing."""

        labelled_tweets_count = len(self.labelled_tweets)
        training_entries_number = floor(labelled_tweets_count * 0.8)
        self.training_set = self.labelled_tweets[:training_entries_number]
        self.test_set = self.labelled_tweets[training_entries_number:]

    @staticmethod
    def get_all_words_in_labelled_tweets(tweets: list) -> list:
        """Get every word from the data in the training set."""

        all_words = []
        for (words, sentiment) in tweets:
            all_words.extend(words.split())
        return all_words

    @staticmethod
    def get_word_features(word_list: list):
        """Order feature words by frequency."""

        word_list = FreqDist(word_list)
        return word_list.keys()

    def extract_features(self, tweet):
        tweet_words = tweet.split()
        features = {}
        for word in self.word_features:
            features['contains(%s' % word] = (word in tweet_words)
        return features
