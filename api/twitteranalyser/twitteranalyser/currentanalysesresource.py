from json import dumps

import falcon
from dataset import connect

from twitteranalyser import constants


class CurrentAnalyses(object):

    @staticmethod
    def on_get(req: falcon.Request, resp: falcon.Response):

        database = connect(constants.DB_URL)
        current_analyses_table = database[constants.CURRENT_ANALYSES_TABLE]

        if req.get_param(constants.TWEET_TOPIC_TABLE_KEY_NAME):
            if current_analyses_table.find_one(
                    analysis_topic_id=req.get_param_as_int(
                        constants.TWEET_TOPIC_TABLE_KEY_NAME)):
                resp.status = falcon.HTTP_OK
                resp.body = dumps({'currently_analysing': True})
            else:
                resp.status = falcon.HTTP_OK
                resp.body = dumps({'currently_analysing': False})

        elif req.get_param(constants.TWEET_USER_TABLE_KEY_NAME):
            if current_analyses_table.find_one(
                    analysis_user_id=req.get_param_as_int(
                        constants.TWEET_USER_TABLE_KEY_NAME)):
                resp.status = falcon.HTTP_OK
                resp.body = dumps({'currently_analysing': True})
            else:
                resp.status = falcon.HTTP_OK
                resp.body = dumps({'currently_analysing': False})

        elif req.get_param('all'):
            data = {'current_analyses': []}

            current_analyses = current_analyses_table.all()
            for current_analysis in current_analyses:
                if current_analysis[constants.TWEET_TOPIC_TABLE_KEY_NAME]:
                    if current_analysis['is_hashtag']:
                        data['current_analyses'].append({'type': 'hashtag',
                                                         'analysis_topic_id': current_analysis[
                                                             constants.TWEET_TOPIC_TABLE_KEY_NAME]})
                    else:
                        data['current_analyses'].append({'type': 'topic',
                                                         'analysis_topic_id': current_analysis[
                                                             constants.TWEET_TOPIC_TABLE_KEY_NAME]})
                elif current_analysis[constants.TWEET_USER_TABLE_KEY_NAME]:
                    data['current_analyses'].append({'type': 'user',
                                                     'analysis_user_id': current_analysis[
                                                         constants.TWEET_USER_TABLE_KEY_NAME]})
            resp.status = falcon.HTTP_OK
            resp.body = dumps(data)
        else:
            resp.status = falcon.HTTP_NOT_FOUND
