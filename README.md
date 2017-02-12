[![Build Status](https://travis-ci.com/mathieuhendey/final-year-project.svg?token=moUsqfQSbWDYrgRb2xXP&branch=master)](https://travis-ci.com/mathieuhendey/final-year-project)

# Final Year Project AJ02

## Description
This is a project aimed at analysing the popularity of Twitter users and
topics.

The front end is written in PHP 7, using Symfony. The analyser itself is
written in Python, using Tweepy to stream Tweets. It uses Docker to
easily set up local development environments and make it easy to deploy.

### Getting started
To run the app locally, you must first download Docker from [their
website](https://www.docker.com/products/docker).

Once you have Docker downloaded, just clone the repository somewhere,
switch to the directory containing the repository and run
`docker-compose up --build`. This may take a while the first time it's
run as it will need to download the base containers from the internet,
as well as download any dependencies I have specified. Once complete,
the following containers will have been spun up:
* **frontend** – This is a PHP-FPM-based container. PHP-FPM is a CGI for PHP
applications.
* **database** – This is a MySQL-based database used for storing Tweets, Users,
Topics etc.
* **nginx** – Serves the PHP frontend to the Internet.
* **api** - This is the Python application that streams Tweets and classifies
them.

After `docker-compose up --build` has completed, you should be able to
see the four containers listed when you run `docker ps`.
