[![Build Status](https://travis-ci.com/mathieuhendey/final-year-project.svg?token=moUsqfQSbWDYrgRb2xXP&branch=master)](https://travis-ci.com/mathieuhendey/final-year-project)

# Final Year Project AJ02

## Description
This is a project aimed at analysing the popularity of Twitter users and
topics. Users can enter a search term (@user, #hashtag, 'set of words') and
view the sentiment of the responses to the search term. 'Sentiment' here means
'positive' or 'negative'.

The front end is written in PHP 7.1, using Symfony 3. N.B. this is my first
project using Symfony so please forgive any un-followed standards.

The analyser itself is written in Python, using Tweepy to stream Tweets. There
are three parts to the anaylyser:
- The service which streams data from Twitter, saves it in the database and adds
  messages to a RabbitMQ queue;
- The RabbitMQ queue;
- The service which reads the queue and classifies the Tweets.

Twitter mandates that clients of their streaming API keep up with the stream,
so the Tweets are then put in a RabbitMQ queue to be classified (classifying in
real time led to being kicked off the stream). Once classified the sentiment of
each Tweet is stored in the database.

The front end then displays this data in a (hopefully eventually) variety of
ways.

This project uses Docker to manage any required services.

### Getting started
To run the app locally, you must first download Docker from [their
website](https://www.docker.com/products/docker).

Once you have Docker downloaded, just clone the repository somewhere,
switch to the directory containing the repository and run
`docker-compose up --build`. This may take a while the first time it's
run as it will need to download the base containers from the internet,
as well as download any dependencies I have specified. Once complete,
the following containers will have been spun up:
* **frontend** – This is a Debian Jessie container with PHP-FPM. PHP-FPM is a
CGI for PHP.
* **db** – This is a MySQL-based database.
* **nginx** – Serves the PHP front end to the Internet.
* **api** - This is the Python application that streams Tweets and provides
and endpoint which triggers streaming.
* **classifier** This is the Python application that does the actual
classification of Tweets.
* **rabbit** This is a RabbitMQ service that allows `api` to offload
classification of Tweets to `classifier`.

After `docker-compose up --build` has completed, you should be able to
see the six containers listed when you run `docker ps`.

You will then need to install the PHP dependencies by changing to
`frontend/twitteranalyser` and executing `composer install`.

Once Composer has installed the dependencies you will be able to see the front
end by navigation to localhost:80 in a browser.
