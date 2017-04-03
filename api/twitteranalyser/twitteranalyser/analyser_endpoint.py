# Author: Mathieu Hendey <mhendey01@qub.ac.uk>
# Source: https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
# Part of the AJ02 project supervised by Anna Jurek

"""
Simply create the API and register our Tweets resource under the '/tweets'
endpoint.

If we wanted to make the analysis part of the project triggered by a cron job
we could simply add another endpoint like '/analyse'.
"""

import falcon

from twitteranalyser.tweetresource import Tweet
from twitteranalyser.currentanalysesresource import CurrentAnalyses


ENDPOINT = application = falcon.API()
TWEETS = Tweet()
ENDPOINT.add_route('/tweets', TWEETS)

CURRENT_ANALYSES = CurrentAnalyses()
ENDPOINT.add_route('/current_analyses', CURRENT_ANALYSES)
