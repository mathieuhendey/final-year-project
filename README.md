[![Build Status](https://travis-ci.com/mathieuhendey/final-year-project.svg?token=moUsqfQSbWDYrgRb2xXP&branch=master)](https://travis-ci.com/mathieuhendey/final-year-project)

# Final Year Project AJ02

## Description
This is a project aimed at analysing the popularity of Twitter users,
using various sources of data such as replies to their tweets, likes of
their tweets, and retweets. It may also be used to analyse the reaction
to a trending topic.

The front end is written in PHP 7, using Symfony. The analyser itself is
written in Python, using Tweepy to stream Tweets. It uses Docker to
easily set up local development environments and make it easy to deploy.

### Getting started
To run the app locally, you must first download Docker from [their
website](https://www.docker.com/products/docker). I have only tested it
under macOS using the native Docker for Mac, but it should also work on
Linux. I have no idea about Windows.

Once you have Docker downloaded, just clone the repository somewhere,
switch to the directory containing the repository and run
`docker-compose up --build`. This may take a while the first time it's
run as it will need to download the base containers from the internet,
as well as download any dependencies I have specified. Once complete,
the following containers will have been spun up:
* **PHP FPM** – This is a very fast CGI. Its main benefits are seen in
  high-load applications, which this project is unlikely to end up
being, but I went with it as it is used by almost all PHP projects.
* **MySQL** – I have decided to use a MySQL database for persistence.
  MySQL is supported by the ORM I plan on using, I have more experience
with it than other databases, and, being the default database for PHP,
it's likely that I will be able to find much more documentation for it
than if I had decided to use something like Mongo or Cassandra.
* **NGINX** – NGINX is being used as the web server for this project.
  Although Apache is more commonly used in PHP projects, I've always
found NGINX to be something you can set up once and never have to look
at again.
* **Python** - This is the analyser itself.

After `docker-compose up --build` has completed, you should be able to
see the three containers listed when you run `docker ps`.

