var intervalInMs = 5000;

var tweetList = $("#tweet_list");

var termType = tweetList.data('term-type');
var termId = tweetList.data('term-id');

setInterval(function() {
    var latest_tweet_in_list = 1;
    if (tweetList.children().last().data("id")) {
       latest_tweet_in_list = tweetList.children().last().data("id");
    }

    var query_params = {
        term_type: termType,
        term_id: termId,
        latest_tweet_in_list: latest_tweet_in_list
    };

    $.ajax({
        url: url,
        data: query_params,
        type: "GET",
        dataType: "html"
    })
        .done(function (newTweets) {
            console.log(query_params);
            tweetList.append(newTweets)
        })
}, intervalInMs);