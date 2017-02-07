FROM python:latest

MAINTAINER Mathieu Hendey

COPY ./twitteranalyser/requirements.txt /twitteranalyser/requirements.txt
WORKDIR /twitteranalyser
RUN pip install -r requirements.txt
RUN python -m nltk.downloader stopwords

COPY ./twitteranalyser /twitteranalyser

CMD gunicorn -b 0.0.0.0:80 twitter_analyser.analyser_endpoint
