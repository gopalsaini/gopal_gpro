@extends('layouts/master')

@section('title',__('Dashboard'))

@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('admin-assets/css/animate.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('admin-assets/css/chartist.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('admin-assets/css/owlcarousel.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('admin-assets/css/prism.css') }}">

<style>
.mainboxs {
    border: 1px solid #bdadad;
}

.mainboxs h2 {
    font-size: 12px;
    background: #dedada;
    padding: 11px;
    margin: 0;
}

.mainboxs .counternumber {
    text-align: center;
    padding: 10px;
}

.brlr {
    border-left: 1px solid #bdadad;
    border-right: 1px solid #bdadad;
}

.headding {
    border-top: 2px solid #9e9494;
    border-bottom: 2px solid #9e9494;
}

.box-inner {
    border-right: 1px solid;
}

.box-inner h2 {
    text-align: center;
    line-height: 20px;
    background: #eaeaea;
    /* text-transform: uppercase; */
}

.box-inner h4 {
    text-align: center;
    background: #fff;
    padding: 11px;

}

.brl {
    border-right: 1px solid #bdadad;
}
</style>

@endpush

@section('content')

<div class="container-fluid">
    <div class="row mainboxs">
        
        <div class="col-md-12 p-0 headding">
            <h2>Attendees Stages</h2>
        </div>
        
        <div class="col-2 p-0 box-inner">
            <a href="{{url('admin/user/attendees/stage/all')}}">
                <h2>STAGE ALL </h2>
                <h4> {{ \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2]])->count() }} </h4>
            </a>
        </div>
        <div class="col-1 p-0 box-inner">
            <a href="{{url('admin/user/attendees/stage/zero')}}">
                <h2>STAGE 0 </h2>
                <h4> {{ \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['stage', '=', '0']])->count() }} </h4>
            </a>
        </div>

        <div class="col-4  box-inner" id="stageOneDiv">
            
            <h2>STAGE 1 </h2>
            <h4> {{ \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['stage', '=', '1']])->where(function ($query) {
                              $query->where('added_as',null)
                                ->orWhere('added_as', '=', 'Group');
                            })->count() }} </h4>

            <div class="" style="display:none" id="stageOneDivShow">
                <div >
                    <a href="{{url('admin/user/attendees/stage/one')}}" class="row" >
                        <div class="col-4 p-0 box-inner">
                            <h2>Pending </h2>
                            <h4> {{ \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['stage', '=', '1'], ['profile_status', 'Review']])->where(function ($query) {
                              $query->where('added_as',null)
                                ->orWhere('added_as', '=', 'Group');
                            })->count() }} </h4>
                        </div>
                        <div class="col-4 p-0 box-inner">
                            <h2>Waiting </h2>
                            <h4> {{ \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['stage', '=', '1'], ['profile_status', 'Waiting']])->where(function ($query) {
                              $query->where('added_as',null)
                                ->orWhere('added_as', '=', 'Group');
                            })->count() }} </h4>
                        </div>
                        <div class="col-4 p-0 box-inner">
                            <h2>Declined </h2>
                            <h4> {{ \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['stage', '=', '1'], ['profile_status', 'Rejected']])->where(function ($query) {
                              $query->where('added_as',null)
                                ->orWhere('added_as', '=', 'Group');
                            })->count() }} </h4>
                        </div>
                    </a>
                </div>
            </div>
        </div>


        <div class="col-1 p-0 box-inner">
            <a href="{{url('admin/user/attendees/stage/two')}}">
                <h2>STAGE 2 </h2>
                <h4> {{ \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['stage', '=', '2']])
                        ->where(function ($query) {
							$query->where('added_as',null)
								->orWhere('added_as', '=', 'Group');
						})->count() }} </h4>
            
            </a>
        </div>

        <div class="col-1 p-0 box-inner">
            <a href="{{url('admin/user/attendees/stage/three')}}">
                <h2>STAGE 3 </h2>
                <h4> {{ \App\Models\User::with('TravelInfo')->where([['user_type', '!=', '1'], ['designation_id', 2], ['stage', '=', '3']])->where(function ($query) {
							$query->where('added_as',null)
								->orWhere('added_as', '=', 'Group');
						})->count() }} </h4>
            </a>
        </div>


        <div class="col-1 p-0 box-inner">
            <a href="{{url('admin/user/attendees/stage/four')}}">
                <h2>STAGE 4 </h2>
                <h4> {{ \App\Models\User::with('SessionInfo')->where([['user_type', '!=', '1'], ['designation_id', 2], ['stage', '=', '4']])->count() }} </h4>
            </a>
        </div>


        <div class="col-2 p-0 box-inner">
            <a href="{{url('admin/user/attendees/stage/five')}}">
                <h2>STAGE 5 </h2>
                <h4> {{ \App\Models\User::with('TravelInfo')->with('SessionInfo')->where([['user_type', '!=', '1'], ['designation_id', 2], ['stage', '=', '5']])->count() }} </h4>
            </a>
        </div>


        <div class="col-md-4 p-0 brl">
            <h2>Group Registration /Non Group Registration</h2>
            <div class="circle-chart">

                <div id="pieChartGroup" style="width: 100%; height: 310px;"></div>

            </div>
        </div>

        <div class="col-md-4 p-0 brl">
            <h2>Single/married without spouse (Single/TwinSharing)</h2>
            <div class="circle-chart">

                <div id="getSingleMarriedWOSChartAjax" style="width: 100%; height: 300px;"></div>

            </div>
        </div>

        <div class="col-md-4 p-0 ">
            <h2>Stage</h2>
            <div class="circle-chart">
                <div id="stageChart" style="width: 100%; height: 310px;"></div>


            </div>
        </div>

        
        <div class="col-md-6 p-0 brl">
            <h2>Married with spouse -> Both trainers/One of them is a trainer/Both are non trainers </h2>
            <div class="circle-chart">

                <div id="getSingleMarriedWSChartAjax" style="width: 100%; height: 300px;"></div>

            </div>
        </div>

        <div class="col-md-6 p-0 ">
            <h2>Pastoral Trainers - Yes/No</h2>
            <div class="circle-chart">
                <div id="PastoralTrainersChart" style="width: 100%; height: 310px;"></div>


            </div>
        </div>


        <div class="col-md-3 p-0">
            <h2>Amount in Process</h2>
            <h3 class="counternumber"> {{ \App\Models\Transaction::where([['status', '=', Null]])->sum('amount') }} </h3>
        </div>

        <div class="col-md-3 p-0 brlr">
            <h2>Accepted Amount</h2>
            <h3 class="counternumber"> {{ \App\Models\Wallet::where([['type', '=', 'Cr'], ['status', '=', 'Success']])->sum('amount') }} </h3>
        </div>

        <div class="col-md-3 p-0">
            <h2>Declined Amount</h2>
            <h3 class="counternumber"> {{ \App\Models\Wallet::where([['type', '=', 'Cr'], ['status', '=', 'Failed']])->sum('amount') }} </h3>
        </div>

        <div class="col-md-3 p-0">
            <h2>Pending Amount</h2>
            <h3 class="counternumber"> {{ \App\Models\Wallet::where([['type', '=', 'Cr'], ['status', '=', 'Pending']])->sum('amount') }} </h3>
        </div>


        <div class="col-md-6 p-0 brl">
            <h2>Payments</h2>
            <div class="circle-chart">

                <div id="PaymentChartData" style="width: 100%; height: 500px;"></div>

            </div>
        </div>

        <div class="col-md-6 p-0 ">
            <h2>Payment Mode</h2>
            <div class="circle-chart">
                <div id="PaymentTypeChartData" style="width: 100%; height: 500px;"></div>


            </div>
        </div>


        <div class="col-md-12 p-0 headding">
            <h2>Countries (Total Attendees: {{ \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2]])->count() }} Form {{\App\Models\User::select('users.*','countries.name as cname','countries.id as cId')->where([['user_type', '!=', '1'], ['designation_id', 2]])->join('countries','users.citizenship','=','countries.id')->orderBy('countries.name','ASC')->groupBy('countries.id')->count()}} Countries)</h2>
            <div class="circle-chart">
                <div id="countries_chart" style="width: 100%; height: 500px;"></div>
            </div>
        </div>

        

        <div class="col-md-12 p-0 headding">
            <h2>Countries and Continents</h2>
            <div class="circle-chart">
                <div id="continents_chart" style="width: 100%; height: 500px;"></div>
            </div>
        </div>
        

        <div class="col-md-12 p-0 headding">
            <h2>User Ages according Graph</h2>
            <div class="circle-chart">
                <div id="user_age_chart" style="width: 100%; height: 500px;"></div>
            </div>
        </div>
    </div>
</div>
@endsection
<?php
 

$dataPoints = array( 
	array("label"=>"Chrome", "y"=>64.02),
	array("label"=>"Firefox", "y"=>12.55),
	array("label"=>"IE", "y"=>8.47),
	array("label"=>"Safari", "y"=>6.08),
	array("label"=>"Edge", "y"=>4.29),
	array("label"=>"Others", "y"=>4.59)
)
 
?>
@push('custom_js')
<script src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
google.charts.load('current', {
    'packages': ['corechart', 'bar']
});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {

    $.getJSON(baseUrl+"/admin/get-payments", function(result){

        var arr = [['Task', 'Hours per Day']]
        $.each(Object.entries(result), function(i, field){
            arr.push(field);
        });

        var data = google.visualization.arrayToDataTable(arr);
        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, {});
    });

}

google.charts.setOnLoadCallback(drawChart1);

function drawChart1() {

    var data = google.visualization.arrayToDataTable([
        ['Task', 'Hours per Day'],
        ['Credit/Debit Card', 11],
        ['PayPal', 2],
        ['Western Union', 2],
        ['Money Gram', 2],
        ['RAI', 2],
        ['Bank Wire Transfer', 7]
    ]);

    var chart = new google.visualization.PieChart(document.getElementById('piechart1'));

    chart.draw(data, {});
}

google.charts.setOnLoadCallback(languageBar);

function languageBar() {
    var data = new google.visualization.arrayToDataTable([
        ['Opening Move', 'Percentage'],
        ["English (en)", 44],
        ["Spanish (sp)", 31],
        ["Portuguese (pt)", 31]
    ]);

    var options = {
        title: 'Chess opening moves',
        legend: {
            position: 'none'
        },
        chart: {
            // title: 'Chess opening moves',
            // subtitle: 'popularity by percentage'
        },
        bars: 'vertical', // Required for Material Bar Charts.
        axes: {
            x: {
                0: {
                    side: 'top',
                    label: 'Percentage'
                } // Top x-axis.
            }
        },
        bar: {
            groupWidth: "90%"
        }
    };

    var chart = new google.charts.Bar(document.getElementById('top_y_div_language'));
    chart.draw(data, options);
};

google.charts.setOnLoadCallback(packagesChart);

function packagesChart() {

    var data = google.visualization.arrayToDataTable([
        ['Task', 'Hours per Day'],
        ['Twin Sharing', 11],
        ['Daypass', 2],
        ['Single occupancy', 2]
    ]);

    var chart = new google.visualization.PieChart(document.getElementById('packages_chart'));

    chart.draw(data, {});
}


google.charts.setOnLoadCallback(postoral_trainers);

function postoral_trainers() {
    var data = new google.visualization.arrayToDataTable([
        ['Opening Move', 'Percentage'],
        ["Yes", 44],
        ["No", 31]
    ]);

    var options = {
        title: 'Chess opening moves',
        legend: {
            position: 'none'
        },
        chart: {
            // title: 'Chess opening moves',
            // subtitle: 'popularity by percentage'
        },
        bars: 'vertical', // Required for Material Bar Charts.
        axes: {
            x: {
                0: {
                    side: 'top',
                    label: 'Percentage'
                } // Top x-axis.
            }
        },
        bar: {
            groupWidth: "90%"
        }
    };

    var chart = new google.charts.Bar(document.getElementById('postoral_trainers'));
    chart.draw(data, options);
};



google.charts.setOnLoadCallback(graduates);

function graduates() {

    var data = google.visualization.arrayToDataTable([
        ['Task', 'Hours per Day'],
        ['DGPA', 11],
        ['NGPA', 2],
        ['None', 2]
    ]);

    var chart = new google.visualization.PieChart(document.getElementById('graduates'));

    chart.draw(data, {});
}



google.charts.setOnLoadCallback(countries_chart);

function countries_chart() {

    $.getJSON(baseUrl+"/admin/get-users-by-country", function(result){

        var arr = [['Country', 'Percentage']]
        $.each(Object.entries(result), function(i, field){
            arr.push(field);
        });

        var options = {
            title: 'Chess opening moves',
            legend: {
                position: 'none'
            },
            chart: {
                // title: 'Chess opening moves',
                // subtitle: 'popularity by percentage'
            },
            bars: 'horizontal', // Required for Material Bar Charts.
            axes: {
                x: {
                    0: {
                        side: 'top',
                        label: 'Percentage'
                    } // Top x-axis.
                }
            },
            bar: {
                groupWidth: "90%"
            }
        };

        var data = google.visualization.arrayToDataTable(arr);
        var chart = new google.charts.Bar(document.getElementById('countries_chart'));

        chart.draw(data, options);

    });

};


google.charts.setOnLoadCallback(continents_chart);

function continents_chart() {

    $.getJSON(baseUrl+"/admin/get-users-by-continents", function(result){

        var arr = [['Country', 'Percentage']]
        $.each(Object.entries(result), function(i, field){
            arr.push(field);
        });

        var options = {
            pieSliceText: 'value',
            sliceVisibilityThreshold: 0,
        };

        var data = google.visualization.arrayToDataTable(arr);
        var chart = new google.charts.Bar(document.getElementById('continents_chart'));

        chart.draw(data, options);

    });

};



google.charts.setOnLoadCallback(continents_chart);

function continents_chart() {

    $.getJSON(baseUrl+"/admin/get-users-by-continents", function(result){

        var arr = [['Country', 'Percentage']]
        $.each(Object.entries(result), function(i, field){
            arr.push(field);
        });

        var options = {
            pieSliceText: 'value',
            sliceVisibilityThreshold: 0,
        };

        var data = google.visualization.arrayToDataTable(arr);
        var chart = new google.charts.Bar(document.getElementById('continents_chart'));

        chart.draw(data, options);

    });

};



google.charts.setOnLoadCallback(user_age_chart);

function user_age_chart() {

    $.getJSON(baseUrl+"/admin/get-users-by-user-age", function(result){

        var arr = [['User Age', 'Value']]
        $.each(Object.entries(result), function(i, field){
            arr.push(field);
        });

        var options = {
            pieSliceText: 'value',
            sliceVisibilityThreshold: 0,
        };

        var data = google.visualization.arrayToDataTable(arr);
        var chart = new google.charts.Bar(document.getElementById('user_age_chart'));

        chart.draw(data, options);

    });

};



google.charts.setOnLoadCallback(stageChart);

function stageChart() {

    $.getJSON(baseUrl+"/admin/get-users-stage-ajax", function(result){

        var arr = [['Task', 'Stage']];
        $.each(Object.entries(result), function(i, field){
            arr.push(field);
        });
        
        var options = {
            pieSliceText: 'value',
            sliceVisibilityThreshold: 0,
        };
        var data = google.visualization.arrayToDataTable(arr);
        var chart = new google.visualization.PieChart(document.getElementById('stageChart'));

        chart.draw(data, options);

        
        var chart = new google.charts.PieChart(document.getElementById('stageChart'));
        

    });
    
}

google.charts.setOnLoadCallback(pieChartGroup);

function pieChartGroup() {

    $.getJSON(baseUrl+"/admin/get-group-registered-chart-ajax", function(result){

        var arr = [['Task', 'Group Registration /Non Group Registration']];
        $.each(Object.entries(result), function(i, field){
            arr.push(field);
        });
        
        var options = {
            pieSliceText: 'value',
            sliceVisibilityThreshold: 0,
        };
        var data = google.visualization.arrayToDataTable(arr);
        var chart = new google.visualization.PieChart(document.getElementById('pieChartGroup'));

        chart.draw(data, options);


    });
    
}


google.charts.setOnLoadCallback(getSingleMarriedWOSChartAjax);

function getSingleMarriedWOSChartAjax() {

    $.getJSON(baseUrl+"/admin/get-single-married-ws-chart-ajax", function(result){

        var arr = [['Task', 'Single/married without spouse (Single/TwinSharing)']];
        $.each(Object.entries(result), function(i, field){
            arr.push(field);
        });
        
        var options = {
            pieSliceText: 'value',
            sliceVisibilityThreshold: 0,
        };
        var data = google.visualization.arrayToDataTable(arr);
        var chart = new google.visualization.PieChart(document.getElementById('getSingleMarriedWOSChartAjax'));

        chart.draw(data, options);


    });
    
}


google.charts.setOnLoadCallback(getSingleMarriedWSChartAjax);

function getSingleMarriedWSChartAjax() {

    $.getJSON(baseUrl+"/admin/get-married-ws-chart-ajax", function(result){

        var arr = [['Task', 'Married with spouse (Both trainers/One of them is a trainer/Both are non trainers)']];
        $.each(Object.entries(result), function(i, field){
            arr.push(field);
        });
        
        var options = {
            pieSliceText: 'value',
            sliceVisibilityThreshold: 0,
        };
        var data = google.visualization.arrayToDataTable(arr);
        var chart = new google.visualization.PieChart(document.getElementById('getSingleMarriedWSChartAjax'));

        chart.draw(data, options);


    });
    
}


google.charts.setOnLoadCallback(PastoralTrainersChart);

function PastoralTrainersChart() {

    $.getJSON(baseUrl+"/admin/get-pastoral-trainers-chart-ajax", function(result){

        var arr = [['Task', 'Pastoral TrainersChart']];
        $.each(Object.entries(result), function(i, field){
            arr.push(field);
        });
        
        var options = {
            pieSliceText: 'value',
            sliceVisibilityThreshold: 0,
        };
        var data = google.visualization.arrayToDataTable(arr);
        var chart = new google.visualization.PieChart(document.getElementById('PastoralTrainersChart'));

        chart.draw(data, options);


    });
    
}

google.charts.setOnLoadCallback(PaymentChartData);

function PaymentChartData() {

    $.getJSON(baseUrl+"/admin/get-payment-chart-ajax", function(result){

        var arr = [['Task', 'Payment ']];
        $.each(Object.entries(result), function(i, field){
            arr.push(field);
        });
        console.log(arr);
        var options = {
            pieSliceText: 'value',
            sliceVisibilityThreshold: 0,
        };
        var data = google.visualization.arrayToDataTable(arr);
        var chart = new google.visualization.PieChart(document.getElementById('PaymentChartData'));

        chart.draw(data, options);


    });
    
}


google.charts.setOnLoadCallback(PaymentTypeChartData);

function PaymentTypeChartData() {

    $.getJSON(baseUrl+"/admin/get-payment-type-chart-ajax", function(result){

        var arr = [['Task', 'Payment ']];
        $.each(Object.entries(result), function(i, field){
            arr.push(field);
        });
        console.log(arr);
        var options = {
            pieSliceText: 'value',
            sliceVisibilityThreshold: 0,
        };
        var data = google.visualization.arrayToDataTable(arr);
        var chart = new google.visualization.PieChart(document.getElementById('PaymentTypeChartData'));

        chart.draw(data, options);


    });
    
}



$(document).ready( function() {

    $("#stageOneDiv").click( function() {
        $('#stageOneDivShow').toggle();
    });

});
</script>



@endpush