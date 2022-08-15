@extends('layouts.app')

@section('content')
<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12">
            <div class="row">
              @foreach($staffOperationRecords['duties'] as $duty)
                <div class="col-md-3 col-sm-3">
                   
                    <div class="card no-b mb-3 bg-danger text-white staff-duty-box">
                        <div class="card-body duties-section">
                            <div class="d-flex justify-content-between align-items-center">
                                <div><span>{{$duty['name']}}</span></div>
                                <div><span class="text-success">{{$duty['quantity']}}</span></div>
                            </div>
                            <hr size="10">
                          <div class="d-flex justify-content-between align-items-center">
                                <div><span>Achieved</span></div>
                                <div><span class="text-success">{{$duty['achieved']}}</span></span></span></div>
                            </div>

                        </div>
                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </div>
    <div class="card no-b my-3">
        <div class="card no-b p-3 my-3">
            <p>Working Progress Chart</p>
            <div class="height-300">
                <canvas
                        data-chart="bar"
                        data-dataset="[[{{implode(',',$staffOperationRecords['yValue'])}}]]"
                        data-labels="[{{implode(',',$staffOperationRecords['xValue'])}}]"
                        data-dataset-options="[
                                    { label:'Work', borderColor:  'rgba(255,99,132,1)', backgroundColor: 'rgba(255, 99, 132, 0.2)'}]"
                        data-options="{
                                        maintainAspectRatio: false,
                                        legend: {
                                            display: true
                                        },
                                        scales: {
                                            xAxes: [{
                                                display: true,
                                                gridLines: {
                                                    zeroLineColor: '#eee',
                                                    color: '#eee',

                                                    borderDash: [5, 5],
                                                }
                                            }],
                                            yAxes: [{
                                                display: true,
                                                gridLines: {
                                                    zeroLineColor: '#eee',
                                                    color: '#eee',
                                                    borderDash: [5, 5],
                                                }
                                            }]

                                        },
                                        elements: {
                                            line: {

                                                tension: 5,
                                                borderWidth: 5
                                            },
                                            point: {
                                                radius: 2,
                                                hitRadius: 10,
                                                hoverRadius: 6,
                                                borderWidth: 4
                                            }
                                        }
                                    }">
                </canvas>
            </div>
        </div>
    </div>

    <div class=" row my-6">
        <div class="col-md-12">
            <div class=" card b-0">
                <div class="card-body p-12">
                    <canvas
                        data-chart="line"
                        data-dataset="[[{{implode(',',$staffOperationRecords['yValue'])}}]]"
                        data-labels="[{{implode(',',$staffOperationRecords['xValue'])}}]"
                        data-dataset-options="[
                                    { label:'Work', borderColor:  'rgba(255,99,132,1)', backgroundColor: '#7dc855'}]"
                        data-options="{
                                        maintainAspectRatio: false,
                                        legend: {
                                            display: true
                                        },
                                        scales: {
                                            xAxes: [{
                                                display: true,
                                                gridLines: {
                                                    zeroLineColor: '#eee',
                                                    color: '#eee',

                                                    borderDash: [5, 5],
                                                }
                                            }],
                                            yAxes: [{
                                                display: true,
                                                gridLines: {
                                                    zeroLineColor: '#eee',
                                                    color: '#eee',
                                                    borderDash: [5, 5],
                                                }
                                            }]

                                        },
                                        elements: {
                                            line: {

                                                tension: 5,
                                                borderWidth: 5
                                            },
                                            point: {
                                                radius: 2,
                                                hitRadius: 10,
                                                hoverRadius: 6,
                                                borderWidth: 4
                                            }
                                        }
                                    }">
                </canvas>
                </div>

                

            </div>
        </div>
    </div>

</div>
@endsection
@push('scripts')

    <script>
        var xValues = [];
        var yValues = [];
        $(document).ready(function(){
            // var filter = ('#date-filter').val();
            let urlParams = getUrlParams(location.search);
            console.log("Ok");
            var filter = urlParams.filter;
            console.log(urlParams.filter);
        var user_id = <?php echo session('user_id') ?>;
        if(user_id) {
            $.ajax({
                url: "{{url('employee-performance/operation/records')}}/"+user_id + "/" + filter,
                type: "GET",
                cache: false,
                success: function(response) {
                    console.log(response);
                    // varxValues = response.xValues;
                    // yValues = response.yValues;
                    createChart(response.xValue, response.yValue, response.max, filter);
                }
            })
        }

        });
        function getUrlParams(urlOrQueryString) {
        if ((i = urlOrQueryString.indexOf('?')) >= 0) {
            const queryString = urlOrQueryString.substring(i+1);
            console.log(queryString);
            if (queryString) {
            return _mapUrlParams(queryString);
            } 
        }

        return {filter: 'today'};
        }
        function _mapUrlParams(queryString) {
        return queryString    
            .split('&') 
            .map(function(keyValueString) { return keyValueString.split('=') })
            .reduce(function(urlParams, [key, value]) {
            if (Number.isInteger(parseInt(value)) && parseInt(value) == value) {
                urlParams[key] = parseInt(value);
            } else {
                urlParams[key] = decodeURI(value);
            }
            return urlParams;
            }, {});
        }
        function createChart(xvalue, yvalue, max, title) {
            var title = title.charAt(0).toUpperCase() + title.slice(1);
            console.log(xvalue);
            var xValues = xvalue;
            var yValues = yvalue;
            var barColors = ["red", "green","blue","orange","brown"];
            var  ctx = document.getElementById("myChart");	
            new Chart(ctx, {
            type: "bar",
            data: {
                labels: xValues,
                datasets: [{
                backgroundColor: barColors,
                data: yValues
                }]
            },
            options: {
                legend: {display: false},
                scales: {
                yAxes: [{ticks: {min: 0, max:max}}],
                },
                title: {
                display: true,
                text: title + " Work",
                fontSize: 16
                },
            }
            });


            var  ctx1 = document.getElementById("lineChart");	
            new Chart(ctx1, {
            type: "line",
            data: {
                labels: xValues,
                datasets: [{
                fill: false,
                pointRadius: 5,
                borderColor: "rgba(255,0,0,0.5)",
                data: yValues
                }]
            },    
            options: {
                legend: {display: true},
                scales: {
                yAxes: [{ticks: {min: 0, max:max}}],
                },
                title: {
                display: true,
                text: title + " Work",
                fontSize: 16,
                color: "green"
                }
            }
            });
        }
        function generateData(value, i1, i2, step = 1) {
            console.log(value);
        for (let x = i1; x <= i2; x += step) {
            yValues.push(eval(value));
            xValues.push(x);
        }
        }
    </script>
    
@endpush
