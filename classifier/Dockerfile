FROM python:latest

MAINTAINER Mathieu Hendey

COPY ./twitteranalyser/requirements.txt /twitteranalyser/requirements.txt
WORKDIR /twitteranalyser

RUN pip install -r requirements.txt

CMD python3 -m twitteranalyser.app
