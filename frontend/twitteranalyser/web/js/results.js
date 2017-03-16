var intervalInMs = 5000;

var tweetList = $("#tweet_list");

var positiveTweets = tweetList.data('positive-tweets');
var negativeTweets = tweetList.data('negative-tweets');

if (!positiveTweets) {
    positiveTweets = 0;
}
if (!negativeTweets) {
    negativeTweets = 0;
}
var termType = tweetList.data('term-type');
var termId = tweetList.data('term-id');

var ctx = $("#myChart");
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Positive', 'Negative'],
        datasets: [{
            label: 'Tweet sentiment',
            data: [positiveTweets, negativeTweets],
            backgroundColor: [
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 99, 132, 0.2)'
            ],
            borderColor: [
                'rgba(54, 162, 235, 1)',
                'rgba(255,99,132,1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    }
});

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
        dataType: "json"
    })
        .done(function (newTweets) {
            myChart.data.datasets[0].data[0] = newTweets.positiveTweets;
            myChart.data.datasets[0].data[1] = newTweets.negativeTweets;
            myChart.update();
            tweetList.append(newTweets.view)
        })
}, intervalInMs);