@extends('layouts.app')

@section('content')
<style>
.form-control {
    border: 1px solid #c1c6cb;
}
.badge {
    font-weight: 900;
}
body{
        overflow-x:hidden !important;
    }

</style>
<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
                @if(session()->has('success'))
                    <div class="alert alert-success">
                        {{ session()->get('success') }}
                    </div>
                @endif
                @if(session()->has('error'))
                    <div class="alert alert-danger">
                        {{ session()->get('error') }}
                    </div>
                @endif
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                          <div class="panel-heading">
                            <div class="col-md-12 col-sm-12 col-grid">
                                Edit Geo Zone
                            </div>
                          </div>
                        <div class="form-content p-5">
                        <form id="-data-form" action="{{route('update.geo.zones')}}" method="POST">
                            {{ csrf_field() }}
                            <div class="row">
                                <label class="col-2 control-lable text-black"><strong> Geo Zone Name </strong><span class="text-danger"><b>*</b></span></label>
                                <div class="col-10">
                                    <input type="hidden" name="geo_zone_id" value="{{$geoZone->id}}" class="form-control">
                                    <input type="text" name="name" value="{{$geoZone->name}}" id="geo-form" class="form-control">
                                    <span class="invalid-feedback sku-error" role="alert">
                                    </span>
                                </div>
                            </div>
                            <div class="row pt-4 pb-4">
                                <label class="col-2 control-lable text-black"><strong> Description </strong></label>
                                <div class="col-10">
                                    <input type="text" name="geo_zone_description" value="{{$geoZone->description}}" id="form-geo-description" class="form-control">
                                    <span class="invalid-feedback meta-title-error" role="alert">
                                    </span>
                                </div>
                            </div>

                            <h2><strong> Edit Countries & Zones </strong></h2>
                            <hr class="" style="height:2px;border-width:0;color:gray;background-color:gray" >

                            <div class="table-responsive">
                                <div id="status_changed_msg" style="display: none"></div>
                                 <table class="table" width="100%" style="border: 1px solid #3f51b5">
     
                                  <thead >
     
                                   <tr
                                   style="background-color: #3f51b5;color:white"
                                   >
                                     <th class="th"><center>Country </center></th>
                                     <th scope="col" class="th"><center>Zone</center></th>
                                     <th scope="col" class="th"><center>Areas</center></th>
                                     <th class="th"><center> </center></th>
     
                                    </tr>
     
                                  </thead>
                                  <tbody class="table-body-zone">
                                      @php $key = 0; @endphp
                                    @foreach($geoZone->zones as $key => $zone)
                                     <tr>
                                         <td style="max-width: 35%;
                                         width: 35%;">
                                         <input type="hidden" name="zone_id[{{$key}}]" value="{{$zone->id}}">
                                             <select name="country[]" class="form-control custom-select selecte-country" onchange="getZones(this.value, '{{$key}}')" id="country-select{{$key}}">
                                                 @foreach($countries as $country)
                                                    <option value="{{$country->id}}" @selected($country->id == $zone->country_id)>{{$country->name}}</option>
                                                @endforeach
                                             </select>
                                         </td>
                                         <td>
                                            <select name="zone[]" class="form-control custom-select select-zone" onchange="getAreas(this.value, '{{$key}}')" id="zone-select{{$key}}">
                                                <option value="0">All Zone</option>
                                               @foreach($cities as $city)
                                                    @if($city->country_id == $zone->country_id)
                                                        <option value="{{$city->id}}" @selected($city->id == $zone->city_id)>{{$city->name}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                         </td>
                                         <td class="text-center" style="max-width: 35%;width: 35%;">
                                            <select name="area[{{$key}}][]" class="form-control custom-select select-area" id="area-select{{$key}}" multiple >
                                                      @foreach($zone->zoneAreas as $zarea)
                                                        @foreach($CityArea as $area)
                                                            @if($zone->city_id == $area->city_id)
                                                             <option value="{{$area->id}}" @selected($area->id == $zarea->area_id)>{{$area->name}}</option>
                                                            @endif
                                                             @endforeach
                                                      @endforeach
                                            </select>
                                        </td>
                                        
                                        <td class="text-center"><a href="javascript:;" class="remove-row" onclick="reomveRow('{{$key}}')"><i class="icon-close2 text-danger-o text-danger" style="font-size:25px"></i></a></td>
                                     </tr>
                                     @endforeach
                                  </tbody>
     
                                </table>
                                <div class="text-right pr-4">
                                    
                                    <a href="javascript:;" class="remove-row" id="add-more-discount"><i class="icon-add_box text-success" style="font-size:50px;color:green"></i></a>
                                </div>
                            </div>

                            
                            
                            <hr class="" style="height:1px;border-width:0;color:gray;background-color:gray" >
                            <div class="row pt-4">
                                <div class="col-12">
                                    <button type="submit" id="add_manually" value="{{@$description->store->name}}-data-form" class="btn btn-primary float-right save-data" data-action="">
                                        Save
                                    </button>
                                </div>
                            </div>
                        </form>
                       </div>
                     </div>


                    </div>
                </div>


            </div>
        </div>
    </div>

{{-- <div class="toast-action" data-title="Hey, Bro!" data-message="Paper Panel has toast as well." data-type="success" data-position-class="toast-top-right"></div> --}}
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select-area').select2({
                width: "resolve",
                allowClear: true,
                closeOnSelect: false,
            });
    var button = $('#add-more-discount');
    var wrapper = $('.table-body-zone');
    var x = {{$key+1}};
    
    $(button).on('click', function() {
        var row = '<tr id="row-'+x+'"><td style="max-width: 35%;width: 35%;"><select name="country[]" class="form-control custom-select selecte-country" onchange="getZones(this.value, '+x+')" id="country-select'+x+'" data-index="'+x+'">'+'\n'+
                                                '<option value="">Select Country</option>'+'\n'+
                                                <?php foreach($countries as $country): ?>
                                                '<option value="<?php echo $country->id ?>"><?php echo $country->name ?></option>'+'\n'+
                                                <?php endforeach; ?>
                                            '</select></td>'+'\n'+
                    '<td> <select name="zone[]" class="form-control custom-select select-zone" onchange="getAreas(this.value, '+x+')" id="zone-select'+x+'"></select></td>'+'\n'+
                    '<td class="text-center" style="max-width: 35%;width: 35%;"><select name="area['+x+'][]" class="form-control custom-select select-area" id="area-select'+x+'" multiple></select></td>'+'\n'+
                    '<td class="text-center"><a href="javascript:;" class="remove-row" onclick="reomveRow('+x+')"><i class="icon-close2 text-danger-o text-danger" style="font-size:25px"></i></a></td></tr>';
      $(wrapper).append(row);
      $('#area-select'+x).select2({
                width: "resolve"
            });
    x++;
    });
    
    
});

function getZones(value, row) {
    if(value) {
        var url = "{{route('get.zones', ':country')}}",
        url = url.replace(":country", value);
        console.log(url);
        $.ajax({
            url: url,
            type: 'GET',
            cache: false,
            success: function(resp) {
                console.log(resp);
                var html = '';
                html += '<option value="0">All Zone</option>';
                resp.cities.forEach(city => {
                    html += '<option value="'+city.id+'">'+city.name+'</option>';
                });

                $('#zone-select'+row).html(html);
            }
        })
    }
}

function getAreas(value, row) {
    console.log(value);
    if(value) {
        var url = "{{route('get.areas', ':city')}}",
        url = url.replace(":city", value);
        console.log(url);
        // return;
        $.ajax({
            url: url,
            type: 'GET',
            cache: false,
            success: function(resp) {
                console.log(resp);
                var html = '';
                html += '<option value="0">All Zone</option>';
                resp.areas.forEach(area => {
                    html += '<option value="'+area.id+'">'+area.name+'</option>';
                });

                $('#area-select'+row).html(html);
            }
        })
    }
}

function reomveRow(row) {
    $('#row-'+row).remove();
}

</script>
@endpush
