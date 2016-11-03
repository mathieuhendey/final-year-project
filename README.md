# Final Year Project AJ02

## Description
This is a project aimed at analysing the popularity of Twitter users,
using various sources of data such as replies to their tweets, likes of
their tweets, and retweets. It may also be used to analyse the reaction
to a trending topic.

It is written in PHP 7, using Symfony. It uses Docker to easily set up
local development environments and make it easy to deploy on EC2.

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

After `docker-compose up --build` has completed, you should be able to
see the three containers listed when you run `docker ps`.

You will then need to use Composer to install dependencies. Composer is
the most-widely used package manager in the PHP community. You can find
instructions for installing Composer
[here](https://getcomposer.org/download/). After you have Composer set
up, enter the `final-year-project/symfony` directory and run `composer install`.
You may be asked for some values: just enter the defaults for all
fields. The reason you are asked for these fields is that I haven't set
up the connection to the database yet.

Then, simply head to http://localhost/app_dev.php to access the
application.

Clicking the "Authorise on Twitter" link will take you through the
normal Twitter authorisation flow, redirecting you to Twitter and asking
if you are happy with the permissions I'm requesting.

Currently, all that will happen after you authorise is you'll see
information about your profile dumped to the homepage.

Any tokens are just stored in the PHP session, as of now they are not
persisted to the database as I haven't decided whether to keep access
tokens locally or just get the user to re-authenticate each time the
use the application.
