$(function() {
    if($('#graph').length > 0) {
        subject_statistic();
    }

    if($('#pie-chart').length > 0) {
        statistic_score();
    }

    if($('#student-line-chart').length > 0) {
        subject_statistic_score();
    }
    select_grade();
    select_subject();
});

function grade_statistic(element) {
    var grade_id = element.value;
    if(!grade_id){
        subject_statistic();
        statistic_score();
    }
    subject_statistic(grade_id);
    statistic_score(grade_id);
}

function student_subject_statistic(element) {
    var subject_id = element.value;
    if(!subject_id){
        subject_statistic_score();
    }
    subject_statistic_score(subject_id);
}

function subject_statistic(grade_id=null) {
    $('#preload').removeClass('hidden');
    var url = "index.php?action=statistics";
    var grade = {
        grade_id: grade_id
    };
    if(!grade_id) {
        grade = {};
    }
    
    var success = function(result) {
        var data = $.parseJSON(result);
        var formStatusVar = [];
        var total = []; 

        for (var i in data) {
            formStatusVar.push(data[i].subject_detail);
            total.push(data[i].tested_time);
        }
        var options = {
            legend: {
                display: false
            },
            scales: {
                xAxes: [{
                    display: true
                }],
            yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            },
            plugins: {
                datalabels: {
                    formatter: (value, bar) => {
                        return '';
                    },
                    color: '#fff',
                }
            }
        };

        var myChart = {
            labels: formStatusVar,
            datasets: [
                {
                    label: 'Tổng số',
                    backgroundColor: '#17cbd1',
                    borderColor: '#46d5f1',
                    hoverBackgroundColor: '#0ec2b6',
                    hoverBorderColor: '#42f5ef',
                    data: total
                }
            ]
        };
        $("#graph").replaceWith('<canvas id="graph"></canvas>');
        $(".tested-time-chart .chartjs-size-monitor").remove();
        var bar = $("#graph");
        var barGraph = new Chart(bar, {
            type: 'bar',
            data: myChart,
            options: options
        });

        $('#preload').addClass('hidden');
    };
    $.post(url, grade, success);
}

function statistic_score(grade_id=null) {
    $('#preload').removeClass('hidden');
    var url = "index.php?action=statistics_score";
    var grade = {
        grade_id: grade_id
    };
    
    if(!grade_id) {
        grade = {};
    }
    var success = function(result) {
        var json_data = $.parseJSON(result);
        var map_arr = [parseInt(json_data[0].bad), parseInt(json_data[0].complete), parseInt(json_data[0].good), parseInt(json_data[0].excellent)];
        var sum_data = 0;
        map_arr.map(data => {
            sum_data += parseInt(data);
        });

        console.log(sum_data);
        var data = [{
            data: map_arr,
            labels: ["Yếu", "Trung bình", "Khá", "Giỏi"],
            backgroundColor: [
                "#4b77a9",
                "#5f255f",
                "#d21243",
                "#B27200"
            ],
            borderColor: "#fff"
        }];
        
        var options = {
            tooltips: {
                enabled: false
            },

            plugins: {
                datalabels: {
                    formatter: (value, ctx) => {
                        var sum = 0;
                        var dataArr = ctx.chart.data.datasets[0].data;
                        dataArr.map(data => {
                            sum += parseInt(data);
                        });

                        var percentage = (value*100 / sum).toFixed(2)+"%";
                        return percentage;
                    },
                    color: '#fff',
                }
            }
        };
    
        $("#pie-chart").replaceWith('<canvas id="pie-chart"></canvas>');
        $(".score-percent-chart .chartjs-size-monitor").remove();
        $(".score-percent-chart .empty-chart").remove();
        var ctx = document.getElementById("pie-chart").getContext('2d');
        if(sum_data > 0) {
            var myChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    datasets: data
                },
                options: options
            });
        } else if(grade_id){
            $('.score-percent-chart .chart-content').prepend('<div class="empty-chart">Khối ' + grade_id + ' chưa có bài thi nào hoàn thành</div>');
        } else {
            $('.score-percent-chart .chart-content').prepend('<div class="empty-chart">Chưa có bài thi nào hoàn thành</div>');
        }
        
    }
    $.post(url, grade, success);
}


function subject_statistic_score(subject_id=null) {
    $('#preload').removeClass('hidden');
    var url = "index.php?action=subject_statistics_score";
    var subject = {
        subject_id: subject_id
    };
    
    if(!subject_id) {
        grade = {};
    }
    var success = function(result) {
        var json_data = $.parseJSON(result);
        var scoreList = [];
        var dateList = []; 
        if(json_data.length > 0) {
            for (var i in json_data) {
                scoreList.push(json_data[i].score);
                var date = new Date(json_data[i].day);
                var dateformat = '';
                dateformat = date.getDate() + '/' + parseInt(date.getMonth() + 1) + '/' + date.getFullYear();
                dateList.push(dateformat);
            }
        }
        
        var options = {
            legend: {display: false},
            scales: {
                yAxes: [{ticks: {min: 0, max:10}}],
            },
            plugins: {
                datalabels: {
                    formatter: (value, line) => {
                        return '';
                    },
                    color: '#fff',
                }
            }
        };

        var data = {
            labels: dateList,
            datasets: [{
            fill: false,
            lineTension: 0,
            backgroundColor: "rgba(0,0,255,1.0)",
            borderColor: "rgba(0,0,255,0.1)",
            data: scoreList
            }]
        };

        $("#student-line-chart").replaceWith('<canvas id="student-line-chart"></canvas>');
        $(".score-percent-chart .chartjs-size-monitor").remove();
        $(".score-percent-chart .empty-chart").remove();
        var line = $("#student-line-chart");

        var myChart = new Chart(line, {
            type: "line",
            data: data,
            options: options
        });
    }
    $.post(url, subject, success);
}