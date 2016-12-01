FROM python:latest

ADD ./twitter-test/requirements.txt /twitter-test/requirements.txt
WORKDIR /twitter-test
RUN pip install -r requirements.txt

ADD ./twitter-test /twitter-test

CMD gunicorn -b 0.0.0.0:80 test
