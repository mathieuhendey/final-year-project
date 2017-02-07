"""
This module contains code related to extracting features and classifying Tweets
as 'positive' or 'negative'.
"""
from csv import reader
from re import search
from re import sub
from pathlib import Path
import pickle
from string import punctuation
from typing import Iterable
from typing import Union

from nltk import FreqDist
from nltk import NaiveBayesClassifier
from nltk.classify import apply_features
from nltk.corpus import stopwords
from tweepy import Status


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
        # Initialise these in __init__ so they're only instantiated once, with
        # this class, rather than every time a Tweet is to be classified.
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
            if word in self.stopwords or not starts_with_alpha:
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

        no_punc_translator = str.maketrans("", "", punctuation)
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
        Replace repitions of the same letter are replaced by two of that
        letter. E.g., 'greeaaat' should become 'great.
        """

        return sub(r'([a-z])\1+', r'\1\1', word)

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
    sentiment as positve or negative.
    """

    def __init__(self):
        self.labelled_tweets = []  # Pre-labelled tweets from a corpus
        self.training_set = []  # ~80% of the labelled tweets
        self.test_set = []  # ~20% of the labelled tweets
        self.word_features = []  # All feature words ordered by frequency
        self._classifier = None  # type: NaiveBayesClassifier
        self.initialise_tweet_sets()
        self.initialise_word_features()

    @property
    def classifier(self):
        """
        Classifier getter.

        If there is no pickled classifier, train a new one and pickle it.
        Otherwise, load the pickled classifier and set self._classifier.
        :return:
        """
        if self._classifier is None:
            if Path("data/classifier.p").is_file():
                self.classifier = pickle.load(open('data/classifier.p', 'rb'))  # type: NaiveBayesClassifier
            else:
                self.classifier = self.train()
        return self._classifier

    @classifier.setter
    def classifier(self, classifier: NaiveBayesClassifier) -> None:
        """Classifier setter."""
        self._classifier = classifier

    def train(self) -> NaiveBayesClassifier:
        """
        Train the classifier.

        This takes a very long time, so after training, pickle it and save it
        to disk.
        """
        training_set = apply_features(self.extract_features_from_tweet, self.training_set)
        classifier = NaiveBayesClassifier.train(training_set)
        pickle.dump(classifier, open('data/classifier.p', 'wb'))
        return classifier

    def initialise_tweet_sets(self) -> None:
        """
        Read labelled data from CSV file.

        A row from the CSV looks like the following:
        |<sentiment>|,|<tweet_text>|
        """
        csv_file = open('data/corpus.csv', encoding='utf-8', errors='ignore')
        raw_labelled_tweets = reader(csv_file, delimiter=',', quotechar='|')
        labelled_tweets = []

        for tweet in raw_labelled_tweets:
            sentiment = tweet[0]
            vector = TweetPreprocessor().preprocess_tweet(tweet[1])
            labelled_tweets.append((vector, sentiment))

        for (words, sentiment) in labelled_tweets:
            words_filtered = [e.lower() for e in words.split() if len(e) >= 3]
            self.labelled_tweets.append((words_filtered, sentiment))

        self.split_training_and_test_sets()

    def initialise_word_features(self) -> None:
        """Initialise instance variable containing all words in vector"""
        self.word_features = self.get_word_features(self.get_all_words_from_tweets(self.training_set))

    def split_training_and_test_sets(self) -> None:
        """Split the labelled Tweet set into 80% training and 20% testing."""

        # TODO: split sets
        self.training_set = self.labelled_tweets

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

    def extract_features_from_tweet(self, tweet: Union[list, str]) -> dict:
        """
        Extract feature words from Tweet and put them into a dictionary that
        can be used by NLTK's classifiers.
        """
        if isinstance(tweet, str):
            tweet = tweet.split()
        tweet_words = set(tweet)
        features = {}
        for word in self.word_features:
            features['contains(%s)' % word] = (word in tweet_words)
        return features

    def classify(self, tweet: str):
        """
        Classify the given Tweet.
        """
        return self.classifier.classify(self.extract_features_from_tweet(tweet))
