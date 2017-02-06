"""
Simply create the API and register our Tweets resource under the '/tweets'
endpoint.

If we wanted to make the analysis part of the project triggered by a cron job
we could simply add another endpoint like '/analyse'.
"""

import falcon

from tweetresource import Tweet


ENDPOINT = application = falcon.API()
TWEETS = Tweet()
ENDPOINT.add_route('/tweets', TWEETS)
