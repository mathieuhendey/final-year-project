import falcon

from tweetresource import Tweet

endpoint = application = falcon.API()
tweets = Tweet()
endpoint.add_route('/tweets', tweets)
