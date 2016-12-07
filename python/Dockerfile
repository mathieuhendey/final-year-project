FROM python:latest

MAINTAINER Mathieu Hendey

COPY ./twitteranalyser/requirements.txt /twitteranalyser/requirements.txt
WORKDIR /twitteranalyser
RUN pip install -r requirements.txt

COPY ./twitteranalyser /twitteranalyser

CMD gunicorn -b 0.0.0.0:80 analyser_endpoint
