# Author: Mathieu Hendey <mhendey01@qub.ac.uk>
# Source: https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
# Part of the AJ02 project supervised by Anna Jurek

"""
Test module for the analyser module.
"""
import unittest

import analyser


class TestTweetPreprocessor(unittest.TestCase):

    def setUp(self):
        self.test_preprocessor = analyser.TweetPreprocessor

    def test_remove_urls(self):
        test_tweet = "http://foo.bar test"

        self.assertEqual('test', self.test_preprocessor.remove_urls(test_tweet).strip())

    def test_remove_usernames(self):
        test_tweet = "test @mathieu_hendey"
        self.assertEqual('test', self.test_preprocessor.remove_usernames(test_tweet))

    def test_replace_letter_repetitions(self):
        test_tweet = 'aaaddddccc'
        processed_tweets = self.test_preprocessor.fix_character_repetitions(test_tweet)
        self.assertEqual('aaddcc', processed_tweets)

    def test_remove_hashtags(self):
        test_tweet = '#test'
        self.assertEqual('test', self.test_preprocessor.remove_hash_tags(test_tweet))

    def test_remove_punctuation(self):
        test_tweet = 'test!?!'
        self.assertEqual('test', self.test_preprocessor.remove_punctuation(test_tweet))

    def test_fix_whitespace(self):
        test_tweet = '   test  this is   a   test   '
        self.assertEqual('test this is a test', self.test_preprocessor.fix_whitespace(test_tweet))

    def test_alphabetic(self):
        bad_tweet = '12asd'
        good_tweet = 'Test'

        bad_result = self.test_preprocessor.is_word_alpha(bad_tweet)
        good_result = self.test_preprocessor.is_word_alpha(good_tweet)

        self.assertFalse(bad_result)
        self.assertTrue(good_result)
