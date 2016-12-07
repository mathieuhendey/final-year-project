FROM python:latest

MAINTAINER Mathieu Hendey

COPY ./twitter-analyser/requirements.txt /twitter-analyser/requirements.txt
WORKDIR /twitter-analyser
RUN pip install -r requirements.txt

COPY ./twitteranalyser /twitteranalyser

CMD gunicorn -b 0.0.0.0:80 analyser_endpoint
