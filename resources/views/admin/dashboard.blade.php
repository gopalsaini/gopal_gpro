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
            <h2>Summery</h2>
        </div>
        
        <div class="col-2 p-0 box-inner">
            <a href="#">
                <h2>Total candidates paid(Including Spouses) </h2>

                @php 
            
                $userPaid = [];
                $candidatesWSPaid = 0;
                $candidatesPartiallyPaid = 0;
                $candidatesWSPartiallyPaid = 0;
                $candidatesNotPaid = 0;
                $totalUser = 0;

                $results = \App\Models\User::where([
                        ['user_type', '!=', '1'],
                        ['designation_id', 2],
                    ])
                    ->where('profile_status', 'Approved')
                    ->where(function ($query) {
                        $query->where('added_as', null)
                            ->orWhere('added_as', '=', 'Group')
                            ->orWhere('parent_spouse_stage', '>=', '2');
                    })
                    ->get();

                if ($results) {
                    foreach ($results as $val) {
                        $totalPendingAmount = \App\Helpers\commonHelper::getTotalPendingAmount($val->id);
                        
                        if ($totalPendingAmount <= 0) {
                            $candidatesWSPaid++;
                        } elseif ($totalPendingAmount < $val->amount) {
                            $candidatesPartiallyPaid++;
                            $candidatesWSPartiallyPaid++;
                        } else {
                            $candidatesNotPaid++;
                        }

                        if (!in_array($val->id, $userPaid)) {
                            $userPaid[] = $val->id;
                        }

                        $spouse = \App\Models\User::where([
                            ['parent_id', '=', $val->id],
                            ['added_as', '=', 'Spouse'],
                        ])->first();

                        if ($spouse && !in_array($spouse->id, $userPaid)) {
                            $userPaid[] = $spouse->id;
                            if ($totalPendingAmount <= 0) {
                                $candidatesWSPaid++;
                            } elseif ($totalPendingAmount < $val->amount) {
                                $candidatesWSPartiallyPaid++;
                            } else {
                                $candidatesNotPaid++;
                            }
                        }
                    }
                }
               
                
                @endphp
                <h4> {{ $candidatesWSPaid }} </h4>
            </a>
        </div>
        <div class="col-2 p-0 box-inner">
            <a href="#">
                <h2>Total Candidates Approved but Partially Paid </h2>
                <h4> {{ $candidatesPartiallyPaid }} </h4>
            </a>
        </div>

        <div class="col-4 p-0 box-inner">
            <a href="#">
                <h2>Total Candidates Approved but Partially Paid(Including Spouses)</h2>
                <h4> {{ $candidatesWSPartiallyPaid}} </h4>
            
            </a>
        </div>

        <div class="col-2 p-0 box-inner">
            <a href="#">
                <h2>Total Candidates Approved but have not Paid yet</h2>
                <h4> {{ $candidatesNotPaid }} </h4>
            
            </a>
        </div>

        <div class="col-2 p-0 box-inner">
            <a href="#">
                <h2>Total Candidates Registered(including Spouses)</h2>
                <h4> {{ \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2]])->count() }} </h4>
            </a>
        </div>


        <div class="col-md-12 p-0 headding">
            <h2>Attendees Stages</h2>
        </div>
        
        <div class="col-1 p-0 box-inner">
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
                        <div class="col-2 p-0 box-inner">
                            <h2>Pending </h2>
                            <h4> {{ \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['stage', '=', '1'], ['profile_status', 'Review']])->where(function ($query) {
                              $query->where('added_as',null)
                                ->orWhere('added_as', '=', 'Group');
                            })->count() }} </h4>
                        </div>
                        <div class="col-2 p-0 box-inner">
                            <h2>Waiting </h2>
                            <h4> {{ \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['stage', '=', '1'], ['profile_status', 'Waiting']])->where(function ($query) {
                              $query->where('added_as',null)
                                ->orWhere('added_as', '=', 'Group');
                            })->count() }} </h4>
                        </div>
                        <div class="col-3 p-0 box-inner">
                            <h2>Declined </h2>
                            <h4> {{ \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['stage', '=', '1'], ['profile_status', 'Rejected']])->where(function ($query) {
                              $query->where('added_as',null)
                                ->orWhere('added_as', '=', 'Group');
                            })->count() }} </h4>
                        </div>
                        <div class="col-5 p-0 box-inner">
                            <h2>Approve Not Coming </h2>
                            <h4> {{ \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['stage', '=', '1'], ['profile_status', 'ApprovedNotComing']])->where(function ($query) {
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
                <h2>STAGE 2 W/O Spouse </h2>
                <h4> {{ \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['stage', '=', '2']])
                        ->where(function ($query) {
							$query->where('added_as',null)
								->orWhere('added_as', '=', 'Group');
						})->count() }} </h4>
            
            </a>
        </div>

        <div class="col-1 p-0 box-inner">
            <a href="#">
                <h2>STAGE 2 With Spouse </h2>
                <h4> {{ \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['stage', '=', '2']])
                        ->count() }} </h4>
            
            </a>
        </div>

        <div class="col-1 p-0 box-inner">
            <a href="{{url('admin/user/attendees/stage/three')}}">
                <h2>STAGE 3 </h2>
                <h4> {{ \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['stage', '=', '3']])->count() }}
                </h4>
            </a>
        </div>


        <div class="col-1 p-0 box-inner">
            <a href="{{url('admin/user/attendees/stage/four')}}">
                <h2>STAGE 4 </h2>
                <h4> {{ \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['stage', '=', '4']])->count() }} </h4>
            </a>
        </div>


        <div class="col-2 p-0 box-inner">
            <a href="{{url('admin/user/attendees/stage/five')}}">
                <h2>STAGE 5 </h2>
                <h4> {{ \App\Models\User::with('TravelInfo')->with('SessionInfo')->where([['user_type', '!=', '1'], ['designation_id', 2], ['stage', '=', '5']])->count() }} </h4>
            </a>
        </div>

        <!-- cal start -->
        <div class="col-md-4 p-0 brl">
            <!-- <h2>Total Group /Total Group Candidates</h2> -->
            <h2>Counts - Total Groups and Number of Candidates</h2>
            <div class="circle-chart">
                <div id="TotalGroupRegistration" style="width: 100%; height: 310px;"></div>

            </div>
        </div>

        <div class="col-md-4 p-0 brl">
            <!-- <h2>Total Married couples /Total Married Candidates</h2> -->
            <h2>Counts - Single and Married coming without Spouse</h2>
            <div class="circle-chart">
                <div id="TotalMarriedCouples" style="width: 100%; height: 300px;"></div>

            </div>
        </div>

        <div class="col-md-4 p-0 ">
            <!-- <h2>Single /Married coming without Spouse</h2> -->
            <h2>Counts - Married Couples and Number of Candidates</h2>
            <div class="circle-chart">
                <div id="SingleMarriedComing" style="width: 100%; height: 310px;"></div>


            </div>
        </div>
        <!-- cal -->

        <div class="col-md-4 p-0 brl">
            <!-- <h2>Group Registration /Non Group Registration</h2> -->
            <h2>Candidates Groups Info</h2>
            <div class="circle-chart">
                <div id="pieChartGroup" style="width: 100%; height: 310px;"></div>

            </div>
        </div>

        <div class="col-md-4 p-0 brl">
            <!-- <h2>Single/Twin Sharing/Suite/Club Floor/Double Deluxe</h2> -->
            <h2>Selected Room Type</h2>
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
            <!-- <h2>Pastoral Trainer - Both /Aspirational Trainer- Both/ Pastoral Trainer and Aspirational Trainer/ Pastoral Trainer and Not a Trainer/ Aspirational Trainer and Not a Trainer/ Not Trainers - Both </h2> -->
            <h2>Trainer Type - For Married candidates </h2>
            <div class="circle-chart">
                <div id="getSingleMarriedWSChartAjax" style="width: 100%; height: 300px;"></div>
            </div>
        </div>

        <div class="col-md-6 p-0 brl">
            <!-- <h2>Pastoral Trainer/ Aspirational trainer / Not a Trainer</h2> -->
            <h2>Trainer Type - For Single Candidates or Married Candidates coming without Spouse</h2>
            <div class="circle-chart">
                <div id="PastoralTrainersChart" style="width: 100%; height: 310px;"></div>
            </div>
        </div>



        <div class="col-md-3 p-0">
            <h2>Amount in Process</h2>
            <h3 class="counternumber"> ${{ number_format(\App\Models\Transaction::where([['status', '=', Null]])->sum('amount'),2) }} </h3>
        </div>
        <div class="col-md-3 p-0 brlr">
            <h2>Accepted Amount</h2>
            <h3 class="counternumber"> ${{ number_format((\App\Models\Wallet::where([['type', '=', 'Cr'], ['status', '=', 'Success']])->sum('amount')),2) }} </h3>
        </div>

        <div class="col-md-3 p-0">
            <h2>Declined Amount</h2>
            <h3 class="counternumber"> ${{ number_format((\App\Models\Wallet::where([['type', '=', 'Cr'], ['status', '=', 'Failed']])->sum('amount')),2) }} </h3>
        </div>

        <div class="col-md-3 p-0">
            <h2>Pending Amount</h2>
            <h3 class="counternumber"> 
                
            @php 
            
                $totalPendingAmount = 0;

                $results = \App\Models\User::where('profile_status','Approved')->where('stage','2')->get(); 
                if($results){

                    foreach($results as $val){
                        $totalPendingAmount +=\App\Helpers\commonHelper::getTotalPendingAmount($val->id);
                    }
                }
            
            @endphp


                        
            ${{ number_format($totalPendingAmount,2) }} </h3>
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

        

        <div class="col-md-6 p-0 headding">
            <h2>Countries and Continents</h2>
            <div class="circle-chart">
                <div id="continents_chart" style="width: 100%; height: 500px;"></div>
            </div>
        </div>
        

        <div class="col-md-6 p-0 headding">
            <h2>User Ages according Graph</h2>
            <div class="circle-chart">
                <div id="user_age_chart" style="width: 100%; height: 500px;"></div>
            </div>
        </div>

        <div class="col-md-12 p-0 headding">
        @php $country = \App\Models\User::select('users.*','countries.name as cname','countries.id as cId')->where([['users.user_type', '!=', '1'], ['users.designation_id', 2]])->join('countries','users.citizenship','=','countries.id')->groupBy('countries.id')->get(); @endphp
            <h2>Countries (Total Attendees: {{ \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2]])->count() }} Form {{count($country)}} Countries)</h2>
            <div class="circle-chart">
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="card">
                                    <div class="body">
                                        <div class="table-">
                                            <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap4">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                    <table class="table table-hover js-basic-example contact_list dataTable" id="tableSearchData" role="grid" aria-describedby="DataTables_Table_0_info">
                                                        <thead>
                                                            <tr role="row">
                                                                <th class="center sorting sorting_asc" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1"
                                                                    colspan="1" style="width: 48.4167px;" aria-sort="ascending"
                                                                    aria-label="#: activate to sort column descending"># ID</th>
                                                                <th class="center sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                                    style="width: 126.333px;" aria-label=" Name : activate to sort column ascending">Region
                                                                </th>
                                                                <th class="center sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                                    style="width: 126.333px;" aria-label=" Name : activate to sort column ascending"> Country Name
                                                                </th>
                                                                <th class="center sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                                    style="width: 193.017px;" aria-label=" Email : activate to sort column ascending"> Count of Candidates
                                                                </th>
                                                                <th class="center sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                                    style="width: 193.017px;" aria-label=" Email : activate to sort column ascending"> % of Candidates
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        @php 
                                                            $userCountry = \App\Models\User::select('countries.region', 'countries.name as cname', 'countries.id as cId', \DB::raw('count(users.id) as user_count'))
                                                            ->where([['users.user_type', '!=', '1'], ['users.designation_id', 2]])
                                                            ->join('countries', 'users.citizenship', '=', 'countries.id')
                                                            ->groupBy('countries.id')
                                                            ->get();

                                                            $totalUsers = \App\Models\User::where([['user_type', '!=', '1']])->count();
                                                            $totalC = $userCountry->count();
                                                        @endphp

                                                        @if(!empty($userCountry))
                                                            @foreach($userCountry as $key=>$countryData)
                                                            <tr class="gradeX odd">
                                                                <td class="center">{{$key+1}}</td>
                                                                <td class="center">{{$countryData->region}}</td>
                                                                <td class="center">{{ $countryData->cname }}</td>
                                                                <td class="center">{{$countryData->user_count}}</td>
                                                                @php 
                                                                $total = round((($countryData->user_count / $totalUsers) * $totalC),2); 
                                                                @endphp
                                                                <td class="center">{{$total}}</td>
                                                            </tr>
                                                            @endforeach
                                                        @endif 
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <th class="center" rowspan="1" colspan="1">#</th>
                                                                <th class="center" rowspan="1" colspan="1"> Region</th>
                                                                <th class="center" rowspan="1" colspan="1"> Country Name </th>
                                                                <th class="center" rowspan="1" colspan="1"> Count of Candidates </th>
                                                                <th class="center" rowspan="1" colspan="1"> % of Candidates </th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
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

setTimeout(() => {
    
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

}, 1000);

setTimeout(() => {
    
    google.charts.setOnLoadCallback(drawChart1);

    function drawChart1() {

        var data = google.visualization.arrayToDataTable([
            ['Task', 'Hours per Day'],
            ['Credit/Debit Card', 11],
            ['PayPal', 2],
            ['Western Union', 2],
            ['RIA', 2],
            ['Bank Wire Transfer', 7]
        ]);

        var chart = new google.visualization.PieChart(document.getElementById('piechart1'));

        chart.draw(data, {});
    }
}, 2000);


setTimeout(() => {
    
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

}, 3000);


setTimeout(() => {
    
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
}, 4000);


setTimeout(() => {

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

}, 5000);


setTimeout(() => {
    
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
}, 6000);



// setTimeout(() => {
    
//     google.charts.setOnLoadCallback(countries_chart);

//     function countries_chart() {

//         $.getJSON(baseUrl+"/admin/get-users-by-country", function(result){

//             var arr = [['Country', 'Percentage']]
//             $.each(Object.entries(result), function(i, field){
//                 arr.push(field);
//             });

//             var options = {
//                 title: 'Chess opening moves',
//                 legend: {
//                     position: 'none'
//                 },
//                 chart: {
//                     // title: 'Chess opening moves',
//                     // subtitle: 'popularity by percentage'
//                 },
//                 bars: 'horizontal', // Required for Material Bar Charts.
//                 axes: {
//                     x: {
//                         0: {
//                             side: 'top',
//                             label: 'Percentage'
//                         } // Top x-axis.
//                     }
//                 },
//                 bar: {
//                     groupWidth: "90%"
//                 }
//             };

//             var data = google.visualization.arrayToDataTable(arr);
//             var chart = new google.charts.Bar(document.getElementById('countries_chart'));

//             chart.draw(data, options);

//         });

//     };
// }, 3000);



setTimeout(() => {
    
    google.charts.load("current", {packages:["corechart"]});
        google.charts.setOnLoadCallback(drawChart);
        function drawChart() {
        

        
            $.getJSON(baseUrl+"/admin/get-users-by-continents", function(result){

            
            arr = result;
            console.log(arr);

            var data = google.visualization.arrayToDataTable(arr);

            var view = new google.visualization.DataView(data);
            view.setColumns([0, 1,
                            { calc: "stringify",
                                sourceColumn: 1,
                                type: "string",
                                role: "annotation" }]);

            var options = {
                pieSliceText: 'value',
                sliceVisibilityThreshold: 0,
            };
            var chart = new google.visualization.BarChart(document.getElementById("continents_chart"));
            chart.draw(view, options);
        });
    }
}, 4000);



setTimeout(() => {
    
    google.charts.setOnLoadCallback(user_age_chart);

    function user_age_chart() {

        $.getJSON(baseUrl+"/admin/get-users-by-user-age", function(result){

            var arr = result;
            

            var data = google.visualization.arrayToDataTable(arr);

            
            var view = new google.visualization.DataView(data);
            view.setColumns([0, 1,
                            { calc: "stringify",
                                sourceColumn: 1,
                                type: "string",
                                role: "annotation" }]);

            var options = {
                pieSliceText: 'value',
                sliceVisibilityThreshold: 0,
            };

        
            var chart = new google.visualization.BarChart(document.getElementById("user_age_chart"));
            chart.draw(view, options);

        });

    };  
}, 5000);



setTimeout(() => {
    
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
}, 6000);


setTimeout(() => {
    
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
}, 7000);


setTimeout(() => {
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

}, 8000);


setTimeout(() => {
    
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
}, 9000);


setTimeout(() => {
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
}, 10000);


setTimeout(() => {
    
    google.charts.setOnLoadCallback(DoYouSeekPastoralTraining);

    function DoYouSeekPastoralTraining() {

        $.getJSON(baseUrl+"/admin/get-do-you-seek-pastoral-training-chart-ajax", function(result){

            var arr = [['Task', 'Pastoral TrainersChart']];
            $.each(Object.entries(result), function(i, field){
                arr.push(field);
            });
            
            var options = {
                pieSliceText: 'value',
                sliceVisibilityThreshold: 0,
            };
            var data = google.visualization.arrayToDataTable(arr);
            var chart = new google.visualization.PieChart(document.getElementById('DoYouSeekPastoralTraining'));

            chart.draw(data, options);


        });
        
    }
}, 11000);


setTimeout(() => {
    
    google.charts.setOnLoadCallback(PaymentChartData);

    function PaymentChartData() {

        $.getJSON(baseUrl+"/admin/get-payment-chart-ajax", function(result){

            var arr = [['Task', ' ']];
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
}, 12000);


setTimeout(() => {
    google.charts.setOnLoadCallback(PaymentTypeChartData);

    function PaymentTypeChartData() {

        $.getJSON(baseUrl+"/admin/get-payment-type-chart-ajax", function(result){

            var arr = [['Task', ' ']];
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
}, 13000);


setTimeout(() => {
    
    google.charts.setOnLoadCallback(TotalGroupRegistration);

    function TotalGroupRegistration() {

        $.getJSON(baseUrl+"/admin/get-total-group-registration", function(result){

            var arr = [['Task', '']];
            $.each(Object.entries(result), function(i, field){
                arr.push(field);
            });
            console.log(arr);
            var options = {
                pieSliceText: 'value',
                sliceVisibilityThreshold: 0,
            };
            var data = google.visualization.arrayToDataTable(arr);
            var chart = new google.visualization.ColumnChart(document.getElementById('TotalGroupRegistration'));

            chart.draw(data, options);


        });
        
    }
}, 14000);

setTimeout(() => {
    google.charts.setOnLoadCallback(TotalMarriedCouples);

    function TotalMarriedCouples() {

        $.getJSON(baseUrl+"/admin/get-total-married-couples", function(result){

            var arr = [['Task', ' ']];
            $.each(Object.entries(result), function(i, field){
                arr.push(field);
            });
            console.log(arr);
            var options = {
                pieSliceText: 'value',
                sliceVisibilityThreshold: 0,
            };
            var data = google.visualization.arrayToDataTable(arr);
            var chart = new google.visualization.ColumnChart(document.getElementById('TotalMarriedCouples'));

            chart.draw(data, options);


        });
        
    }
}, 15000);


setTimeout(() => {
    
    google.charts.setOnLoadCallback(SingleMarriedComing);

    function SingleMarriedComing() {

        $.getJSON(baseUrl+"/admin/get-single-married-coming", function(result){

            var arr = [['Task', ' ']];
            $.each(Object.entries(result), function(i, field){
                arr.push(field);
            });
            console.log(arr);
            var options = {
                pieSliceText: 'value',
                sliceVisibilityThreshold: 0,
            };
            var data = google.visualization.arrayToDataTable(arr);
            var chart = new google.visualization.ColumnChart(document.getElementById('SingleMarriedComing'));

            chart.draw(data, options);


        });
        
    }
}, 16000);


    $(document).ready( function() {

        $("#stageOneDiv").click( function() {
            $('#stageOneDivShow').toggle();
        });

    });

    $(document).ready(function() {
    
    $('#tableSearchData').DataTable({
        "processing": false,
        "serverSide": false,
        "searching": true,
        "ordering": true,

    });
    });

</script>



@endpush