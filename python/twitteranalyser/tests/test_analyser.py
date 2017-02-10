# Author: Mathieu Hendey <mhendey01@qub.ac.uk>
# Source: https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
# Part of the AJ02 project supervised by Anna Jurek

"""
Test module for the analyser module.
"""

import pytest

from twitteranalyser import analyser


class TestTweetPreprocessor(object):

    preprocessor = analyser.TweetPreprocessor()

    def test_remove_urls(self):
        test_tweet = "http://foo.bar test"
        assert 'test' == self.preprocessor.remove_urls(test_tweet).strip()

    def test_remove_usernames(self):
        test_tweet = "test @mathieu_hendey"
        assert 'test USER' == self.preprocessor.remove_usernames(test_tweet)

    def test_replace_letter_repetitions(self):
        test_tweet = 'aaaddddccc'
        processed_tweets = self.preprocessor.fix_character_repetitions(test_tweet)
        assert 'aaddcc' == processed_tweets

    def test_remove_hashtags(self):
        test_tweet = '#test'
        assert 'test' == self.preprocessor.remove_hash_tags(test_tweet)

    def test_remove_punctuation(self):
        test_tweet = 'test!?!'
        assert 'test' == self.preprocessor.remove_punctuation(test_tweet)

    def test_fix_whitespace(self):
        test_tweet = '   test  this is   a   test   '
        assert 'test this is a test' == self.preprocessor.fix_whitespace(test_tweet)

    @pytest.fixture(params=[['12asd', False], ['Test', True]])
    def alphabetic_fixture(self, request):
        yield request.param

    def test_alphabetic(self, alphabetic_fixture):
        assert self.preprocessor.is_word_alpha(alphabetic_fixture[0]) is alphabetic_fixture[1]
