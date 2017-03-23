CREATE TABLE IF NOT EXISTS analysis_topic
(
    id BIGINT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    term VARCHAR(300) NOT NULL,
    is_hashtag BOOLEAN NOT NULL,
    UNIQUE KEY term_is_hashtag (term, is_hashtag),
    created_on TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_on TIMESTAMP NOT NULL DEFAULT NOW() ON UPDATE now()
) CHARACTER SET utf8mb4;

CREATE TABLE IF NOT EXISTS analysis_user
(
    id BIGINT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    twitter_id BIGINT NOT NULL UNIQUE,
    author_screen_name VARCHAR(300) NOT NULL UNIQUE,
    created_on TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_on TIMESTAMP NOT NULL DEFAULT NOW() ON UPDATE now()
) CHARACTER SET utf8mb4;

CREATE TABLE IF NOT EXISTS current_analyses
(
    id BIGINT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    analysis_topic_id BIGINT,
    analysis_user_id BIGINT,
    is_hashtag BOOLEAN NOT NULL,
    FOREIGN KEY (analysis_topic_id)
      REFERENCES analysis_topic(id),
    FOREIGN KEY (analysis_user_id)
      REFERENCES analysis_user(id)
) CHARACTER SET utf8mb4;

DELETE FROM current_analyses;

CREATE TABLE IF NOT EXISTS tweet
(
    id BIGINT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    analysis_topic_id BIGINT,
    analysis_user_id BIGINT,
    author_screen_name VARCHAR(15) NOT NULL,
    author_id VARCHAR(300) NOT NULL,
    in_reply_to_user_id VARCHAR(300),
    in_reply_to_screen_name VARCHAR(15),
    in_reply_to_status_id VARCHAR(300),
    tweet_id VARCHAR(300) UNIQUE NOT NULL,
    tweet_text VARCHAR(300),
    sentiment VARCHAR(10),
    created_on DATETIME,
    FOREIGN KEY (analysis_topic_id)
      REFERENCES analysis_topic(id),
    FOREIGN KEY (analysis_user_id)
      REFERENCES analysis_user(id)
) CHARACTER SET utf8mb4;
