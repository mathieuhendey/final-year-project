# Author: Mathieu Hendey <mhendey01@qub.ac.uk>
# Source: https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
# Part of the AJ02 project supervised by Anna Jurek

"""
This module contains code related to extracting features and classifying Tweets
as 'positive' or 'negative'.
"""

import logging
from csv import reader
from math import floor
from pathlib import Path
from pickle import dump
from pickle import load
from pickle import HIGHEST_PROTOCOL
from re import search
from re import sub
from string import punctuation
from typing import Iterable
from typing import Union
from random import shuffle

from nltk import DecisionTreeClassifier
from nltk import FreqDist
from nltk import NaiveBayesClassifier
from nltk.classify import apply_features
from nltk.classify.api import ClassifierI
from nltk.corpus import stopwords
from tweepy import Status


class TweetPreprocessor(object):
    """Reformats Tweets.

    Specifically, it:
        - Lower cases the Tweet
        - Strips URLs
        - Replaces "#word" with "word"
        - Removes punctuation
        - Removes usernames
        - Cleans whitespace between words and trims at both sides of the Tweet
        - Removes stopwords
    """

    def __init__(self):
        # Initialise these in __init__ so they're only instantiated once, with
        # this class, rather than every time a Tweet is to be classified.
        self.stopwords = stopwords.words('english')

    def preprocess_tweet(self, tweet: Union[Status, str]) -> str:
        """Convert a Tweet into a format that makes it easier to analyse."""

        # If tweet is an instance of tweepy.Status, get the 'text' attribute
        # from it.
        if not isinstance(tweet, str):
            tweet = self.reformat_tweet(getattr(tweet, 'text'))
        else:
            tweet = self.reformat_tweet(tweet)

        word_list = tweet.split()
        processed_word_list = []

        for word in word_list:
            word = self.fix_character_repetitions(word)
            starts_with_alpha = self.is_word_alpha(word)
            if word in self.stopwords or not starts_with_alpha:
                continue
            else:
                processed_word_list.append(word)

        tweet = ' '.join(processed_word_list)

        return tweet

    def reformat_tweet(self, tweet: str) -> str:
        """Put Tweet in an easier format to handle.

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
        tweet = self.remove_stopwords(tweet)

        return tweet

    def remove_stopwords(self, tweet: str) -> str:
        """Remove stopwords from a Tweet.

        Stopwords are words that do not affect the sentiment of the Tweet,
        such as prepositions, articles etc.
        """

        return ' '.join([word for word in tweet.split() if word not in self.stopwords])

    @staticmethod
    def remove_urls(tweet: str) -> str:
        """Remove URLs from the Tweet.

        We want to analyse Tweets, not linked articles.
        """

        return sub('((www\.[^\s]+)|(https?://[^\s]+))', '', tweet)

    @staticmethod
    def remove_usernames(tweet: str) -> str:
        """Remove words beginning with "@".

        Usernames have no sentiment value and we already have the Tweet's full
        text, including @usernames, saved in the database.
        """

        return sub(r'(^|[^@\w])@(\w{1,15})\b', 'USER', tweet)

    @staticmethod
    def remove_hash_tags(tweet: str) -> str:
        """Replace '#word' with 'word'"""

        return sub(r'#([^\s]+)', r'\1', tweet)

    @staticmethod
    def remove_punctuation(tweet: str) -> str:
        """Remove all punctuation marks."""

        no_punc_translator = str.maketrans('', '', punctuation)
        return tweet.translate(no_punc_translator)

    @staticmethod
    def fix_whitespace(tweet: str) -> str:
        """Fix up any problems with whitespace in a Tweet.

        Make sure there's only one space between words, and strip whitespace
        from the start and end of the Tweet.
        """

        return sub('\s+', ' ', tweet).strip()

    @staticmethod
    def fix_character_repetitions(word: str) -> str:
        """Fix up character repetitions.

        Two or more identical characters directly after each other will be
        replaced by just two of those characters.

        For example, 'haaaappppppyyy' becomes 'happy'.
        """

        return sub(r'([a-z])\1+', r'\1\1', word)

    @staticmethod
    def is_word_alpha(word: str) -> bool:
        """Check that a word begins with an alphabetic character."""

        result = search(r'^[A-Za-z]*$', word)
        if result is None:
            return False
        return True


class Classifier(object):
    """Classify Tweets as 'positive' or 'negative'.

    Extracts features from tweets and classifies them based on their
    sentiment as positive or negative.

    The default classifier is Naive Bayes.
    """

    NAIVE_BAYES = 'naive_bayes'
    DECISION_TREE = 'decision_tree'

    def __init__(self, classifier_name=NAIVE_BAYES):
        self.labelled_tweets = []  # Pre-labelled tweets from a corpus
        self.training_set = []  # ~80% of the labelled tweets
        self.testing_set = []  # ~20% of the labelled tweets
        self.word_features = []  # All feature words ordered by frequency
        self.classifier_name = classifier_name
        self._classifier = None
        self.initialise_tweet_sets()
        self.initialise_word_features()
        if self.classifier is None:
            self.train()

    @property
    def classifier(self):
        """Classifier getter.

        If there is no pickled classifier, train a new one and pickle it.
        Otherwise, load the pickled classifier and set self._classifier.
        """
        if self._classifier is None:
            if Path('data/%s.p' % self.classifier_name).is_file():
                logging.info('Loaded classifier from disk.')
                self.classifier = load(open('data/%s.p' % self.classifier_name, 'rb'))
            else:
                logging.warning('No classifier found, training new one. This will take a long time.')
                if not self.labelled_tweets:
                    self.initialise_tweet_sets()
                    self.initialise_word_features()
                self.classifier = self.train()
        return self._classifier

    @classifier.setter
    def classifier(self, classifier: ClassifierI) -> None:
        """Classifier setter."""
        self._classifier = classifier

    def train(self) -> ClassifierI:
        """Train the classifier.

        This takes a very long time, so after training, pickle it and save it
        to disk.
        """
        training_set = apply_features(self.extract_features_from_tweet, self.training_set)
        if self.classifier_name == self.NAIVE_BAYES:
            classifier = NaiveBayesClassifier.train(training_set)
        elif self.classifier_name == self.DECISION_TREE:
            classifier = DecisionTreeClassifier.train(training_set)
        else:
            raise ValueError("Couldn't create classifier")
        dump(classifier, open('data/%s.p' % self.classifier_name, 'wb'), HIGHEST_PROTOCOL)
        return classifier

    def initialise_tweet_sets(self) -> None:
        """Read labelled data from CSV file.

        A row from the CSV looks like the following:
        |<sentiment>|,|<tweet_text>|
        """
        csv_file = open('data/corpus.csv', encoding='utf-8', errors='ignore')
        raw_labelled_tweets = reader(csv_file, delimiter=',', quotechar='|')
        labelled_tweets = []

        for tweet in raw_labelled_tweets:
            if len(list(tweet[0])) <= 5:
                continue
            sentiment = tweet[0]
            vector = TweetPreprocessor().preprocess_tweet(tweet[1])
            labelled_tweets.append((vector, sentiment))

        for (words, sentiment) in labelled_tweets:
            words_filtered = [w for w in words.split()]
            self.labelled_tweets.append((words_filtered, sentiment))

        shuffle(self.labelled_tweets)
        feature_sets = [(self.extract_features_from_tweet(t), s) for (t, s) in self.labelled_tweets]

        self.split_training_and_test_sets(feature_sets)

    def initialise_word_features(self) -> None:
        """Initialise instance variable containing all words in vector"""
        self.word_features = self.get_word_features(self.get_all_words_from_tweets(self.labelled_tweets))

    def split_training_and_test_sets(self, feature_sets) -> None:
        """Split the labelled Tweet set into 80% training and 20% testing."""

        total_labelled_tweets = len(feature_sets)
        training_slice = floor(total_labelled_tweets * 0.8)
        self.training_set = feature_sets[:training_slice]
        self.testing_set = feature_sets[training_slice:]

    @staticmethod
    def get_all_words_from_tweets(tweets: list) -> list:
        """Get every word from the data in the training set."""

        all_words = []
        for (words, _) in tweets:
            all_words.extend(words)
        return all_words

    @staticmethod
    def get_word_features(word_list: list) -> Iterable:
        """Order feature words by frequency."""

        word_list = FreqDist(word_list)
        return word_list.keys()

    def extract_features_from_tweet(self, tweet: list) -> dict:
        """
        Extract feature words from Tweet and put them into a dictionary that
        can be used by NLTK's classifiers.
        """
        tweet_words = set(tweet)
        features = {}
        for word in self.word_features:
            features['contains(%s)' % word] = (word in tweet_words)
        return features

    def classify(self, tweet: str):
        """Classify the given Tweet."""
        tweet = TweetPreprocessor().preprocess_tweet(tweet)
        distribution = self.classifier.prob_classify(self.extract_features_from_tweet(tweet.split()))
        for label in distribution.samples():
            print("%s: %f" % (label, distribution.prob(label)))
