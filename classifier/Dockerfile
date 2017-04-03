FROM python:latest

LABEL maintainer="Mathieu Hendey" \
      name="Sentiment analyser for Tweets" \
      version="0.10"

COPY ./twitteranalyser/requirements.txt /twitteranalyser/requirements.txt
WORKDIR /twitteranalyser

RUN pip install -r requirements.txt

CMD python3 -m twitteranalyser.app
