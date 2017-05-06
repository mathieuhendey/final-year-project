$(document).ready(function() {
    function randomColorFactor() {
        return Math.round(Math.random() * 255);
    }
    function randomColor(opacity) {
        return 'rgba(' + randomColorFactor() + ',' + randomColorFactor() + ',' + randomColorFactor() + ',' + (opacity || '.3') + ')';
    }
    // var now = moment();
    var ctx = $("#myChart");
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [
                moment().format("ddd, hA"),
                moment().subtract(1, 'hours').format("ddd, hA"),
                moment().subtract(2, 'hours').format("ddd, hA"),
                moment().subtract(3, 'hours').format("ddd, hA"),
                moment().subtract(4, 'hours').format("ddd, hA"),
                moment().subtract(5, 'hours').format("ddd, hA"),
                moment().subtract(7, 'hours').format("ddd, hA"),
                moment().subtract(8, 'hours').format("ddd, hA"),
                moment().subtract(9, 'hours').format("ddd, hA"),
                moment().subtract(10, 'hours').format("ddd, hA"),
                moment().subtract(11, 'hours').format("ddd, hA"),
                moment().subtract(12, 'hours').format("ddd, hA")
            ]
        },
        options: {
            responsive: true,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:true
                    }
                }],
                xAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Date'
                    }
                }]
            }
        }
    });

    var request = $.ajax({
        url: url,
        data: {
            term_ids: term_ids,
            term_type: term_type
        },
        type: "GET",
        dataType: "json"
    });
    request.done(function (data) {
        console.log(data);
        console.log(moment().format("ddd, hA"));
        myChart.data.datasets = data.datasets;
        $.each(myChart.data.datasets, function (i, dataset) {
            dataset.borderColor = randomColor(0.4);
            dataset.backgroundColor = randomColor(0.5);
            dataset.pointBorderColor = randomColor(0.7);
            dataset.pointBackgroundColor = randomColor(0.5);
            dataset.pointBorderWidth = 1;
        });
        myChart.update();
    });
    request.fail(function(jqXHR, textStatus) {
        console.log( "Request failed: " + textStatus );
    });
});


