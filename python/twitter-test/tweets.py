import falcon


TWITTER_APP_KEY = '8QIz3eR8jPgzGbyl6kzw'
TWITTER_APP_SECRET = 'uDaycgKRCHRIilZFdkvZznJCWGZqcvZ6t9aZeo1GiI'
TWITTER_KEY = '69321956-8hNBZHTC48vrXgcfyJkE81MgpUrv46r2GdevAMsF4'
TWITTER_SECRET = 'mbINiYnlWxaxxoghhIwjGib2baZ9u21qiODXJxUUCwy0K'


class Tweet(object):

    def __init__(self):
        self.auth = tweepy.OAuthHandler(TWITTER_APP_KEY, TWITTER_APP_SECRET)
        self.auth.set_access_token(TWITTER_KEY, TWITTER_SECRET)
        self.api = tweepy.API(self.auth)
        self.stream_listener = StreamListener()
        self.stream = tweepy.Stream(auth=self.api.auth, listener=self.stream_listener)

    def on_get(self, req, resp):
        if not self.stream.running:
            self.stream_listener.num_tweets = 0
            self.stream.filter(track=["trump", "clinton", "hillary clinton", "donald trump"])
            resp.status = falcon.HTTP_200
            resp.body = "Processing..."
        else:
            resp.status = falcon.HTTP_429
