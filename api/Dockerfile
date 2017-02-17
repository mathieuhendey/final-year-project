FROM python:latest

MAINTAINER Mathieu Hendey

COPY ./twitteranalyser/requirements.txt /twitteranalyser/requirements.txt
WORKDIR /twitteranalyser
RUN pip install -r requirements.txt
RUN python -m nltk.downloader stopwords


CMD gunicorn -b 0.0.0.0:80 twitteranalyser.analyser_endpoint

EXPOSE 80
