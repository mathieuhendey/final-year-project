FROM python:3.6.0

LABEL maintainer="Mathieu Hendey" \
      name="API exposing functionality related to classifying Tweets" \
      version="0.10"

COPY ./twitteranalyser/requirements.txt /twitteranalyser/requirements.txt
WORKDIR /twitteranalyser
RUN pip install -r requirements.txt

CMD gunicorn --reload -b 0.0.0.0:80 twitteranalyser.analyser_endpoint
