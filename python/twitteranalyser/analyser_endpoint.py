import falcon

from tweetresource import Tweet

"""
Simply create the API and register our Tweets resource under the '/tweets'
endpoint.

If we wanted to make the analysis part of the project triggered by a cron job
we could simply add another endpoint like '/analyse'.
"""

endpoint = application = falcon.API()
tweets = Tweet()
endpoint.add_route('/tweets', tweets)
