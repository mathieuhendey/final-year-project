import pika
from os import environ
from json import loads
from dataset import connect
from twitteranalyser.analyser import Classifier
from twitteranalyser import constants
import logging

logging.basicConfig(level=logging.ERROR)

classifier = Classifier()

database = connect(constants.DB_URL)
tweet_table = database[constants.TWEET_TABLE]

creds = pika.PlainCredentials(username=environ.get('RABBIT_USER', 'rabbit'),
                              password=environ.get('RABBIT_PASS', 'rabbit'))

connection_params = pika.ConnectionParameters(
    host=environ.get(
        'RABBIT_HOST', 'rabbit'), port=int(
            environ.get(
                'RABBIT_PORT', 5672)), credentials=creds)

connection = pika.BlockingConnection(connection_params)
channel = connection.channel()
channel.queue_declare(environ.get('RABBIT_QUEUE', 'classifier_queue'))


def callback(channel, method, properties, body):
    status_dict = loads(body)
    status_text = status_dict['tweet_text']
    status_table_id = status_dict['table_id']

    sentiment = classifier.classify(status_text)

    update_data = dict(id=status_table_id, sentiment=sentiment)

    tweet_table.update(update_data, ['id'])


channel.basic_consume(callback,
                      queue=environ.get('RABBIT_QUEUE', 'classifier_queue'),
                      exclusive=True,
                      no_ack=True)

channel.start_consuming()
