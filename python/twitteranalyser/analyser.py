import re

import nltk


class Analyser(object):
    """
    This class determines the sentiment of a tweet. It performs some
    preprocessing of the tweet to convert it to a more useful format, then
    extracts the feature words (i.e. words that have some effect on the
    sentiment of the tweet) and then uses a Naive Bayes Classifier to classify
    the tweet as either positive, negative or neutral.
    """

    stopwords = nltk.corpus.stopwords("english")

    def pre_process_tweet(self, tweet) -> str:
        """
        Convert tweet into a format that makes it easier to analyse.

        This involves removing extraneous words, cleaning up whitespace etc.

        This is so that the words match the feature vector, which will
        consist of lowercase words and their associated sentiment.
        """

        # Get text from status object
        status_text = tweet.text

        # Lower case, strip URLs and #, clean whitespace, remove punctuation
        status_text = self.reformat_tweet(status_text)

        status_text = self.remove_stopwords(tweet)

        return status_text

    @staticmethod
    def reformat_tweet(tweet: str) -> str:
        """
        Lower case all characters, strip URLs, strip @usernames, replace
        remove octothorpes from hashtags, clean up whitespace, remove
        punctuation.
        """

        # Lower case text
        tweet = tweet.lower()

        # Remove any tweetURLs
        tweet = re.sub('((www\.[^\s]+)|(https?://[^\s]+))', '', tweet)

        # Remove any @usernames
        tweet = re.sub('(?<=^|(?<=[^a-zA-Z0-9-_\.]))@([A-Za-z]+[A-Za-z0-9]+)', '', tweet)

        # Replace '#hashtag' with 'hashtag'
        tweet = re.sub(r'#([^\s]+)', r'\1', tweet)

        # Remove all punctuation. TODO: possibly use ! to intensify words?
        tweet.join(c for c in tweet if c not in ('!', '.', ':'))

        # Trim whitespace from both ends of string and replace multiple
        # whitespae characters with one
        tweet = re.sub('\s+', ' ', tweet).strip()

        return tweet

    def remove_stopwords(self, tweet: str) -> str:
        """
        Remove stopwords (words that do not affect the sentiment of the tweet,
        such as prepositions, articles etc.)
        """
        return ' '.join([word for word in tweet.split() if word not in self.stopwords])
