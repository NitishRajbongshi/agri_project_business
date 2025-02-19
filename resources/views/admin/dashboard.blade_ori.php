@extends('admin.common.layout')

@section('title', 'Dashboard')

@section('custom_header')    
<meta name="X-CSRF-Token" content="{{ csrf_token() }}" />
@endsection

@section('main')
<div class="row">
    <div class="col-lg-8 mb-4 order-0">

      <div class="card mb-3">
        <div class="d-flex align-items-end row">
          <div class="col-sm-7">
            <div class="card-body">
              <h5 class="card-title text-primary">Congratulations, {{ auth()->user()->name }}</h5>
              <p class="mb-4">Welcome {{ auth()->user()->name }}, to the Dashboard of 
                Crop Health Diagnosis System (BETA). Here are some key system information at a glance.</p>
              {{-- <a href="javascript:;" class="btn btn-sm btn-outline-primary">View Badges</a> --}}
            </div>
          </div>
          <div class="col-sm-5 text-center text-sm-left">
            <div class="card-body pb-0 px-0 px-md-4">
              <img src="https://demos.themeselection.com/sneat-bootstrap-html-laravel-admin-template-free/demo/assets/img/illustrations/man-with-laptop-light.png" height="140" alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png" data-app-light-img="illustrations/man-with-laptop-light.png">
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header" id=""><h3 id="queryHeader">Header Text</h3></div>  
        <div class="card-body" id="queryChart">
          {{-- Java Script Chart on queries --}}
        </div>
      </div>  
    </div>
    <div class="col-lg-4 col-md-4 order-1">
      <div class="row">
        <div class="col-lg-6 col-md-12 col-6 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="card-title d-flex align-items-start justify-content-between">
                <div class="avatar flex-shrink-0 text-success">
                  {{-- <img src="https://demos.themeselection.com/sneat-bootstrap-html-laravel-admin-template-free/demo/assets/img/icons/unicons/chart-success.png" alt="chart success" class="rounded"> --}}
                  <i class='bx bx-user-pin'></i>
                </div>
              </div>
              
              <span class="fw-semibold d-block mb-1">Moderators</span>
              <h3 class="card-title mb-2">{{$moderators}}</h3>
              <small class="text-success fw-semibold"><i class='bx bx-user-plus' ></i> +{{$moderatorThisMonth}} This Month</small>
            </div>
          </div>
        </div>
        <div class="col-lg-6 col-md-12 col-6 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="card-title d-flex align-items-start justify-content-between">
                <div class="avatar flex-shrink-0  text-info">
                  {{-- <img src="https://demos.themeselection.com/sneat-bootstrap-html-laravel-admin-template-free/demo/assets/img/icons/unicons/wallet-info.png" alt="Credit Card" class="rounded"> --}}
                  <i class='bx bxs-user-pin'></i>
                </div>
                
              </div>
              <span class="fw-semibold d-block mb-1">Agri-Expert</span>
              <h3 class="card-title mb-2">{{$agriExperts}}</h3>
              <small class="text-info fw-semibold"><i class='bx bxs-user-plus' ></i> +{{$agriExpertThisMonth}} This Month</small>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
          <div class="card">
            <div class="card-header" id=""><h3 id="newsHeader">Agri News</h3></div>  
            <div class="card-body" id="newsChart">
              {{-- Java Script Chart on queries --}}
            </div>
          </div>  
      </div>
    </div>

    
</div>



          <div class="card"> 
            <div class="d-flex align-items-center">
              <h5 class="card-header">Diseasewise Data  </h5>
            </div>
          <div class="table-responsive text-nowrap px-4">
          <table class="table" id="tblDiseaseWise">
            <thead>
              <tr>
                <th>No.</th>
                <th>Disease Name</th>
                <th>No. of Diagnosed</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody class="table-border-bottom-0">
               
                  
                @foreach ($jsonFromDataSetFile as $index => $item)
                <tr>
                  {{-- @forelse ($item as $x => $val) --}}
                    <td>
                      <strong>{{$index + 1}}</strong>
                    </td>
                    <td>{{$item->disease_name  }}</td>
                    <td>{{($item->total)}}</td>
                    <td>
                      <button class="btn btn-sm btn-outline-primary OpenDiseaseWiseModalBtn" 
                      data-id="{{$item->disease_cd}}">
    
                      <i class='bx bx-show'></i>Show</button>
                    </td>
                </tr> 
                @endforeach 
                 
                
              
            </tbody>
          </table>
          </div>
          </div>

        <div class="card"> 
          <div class="d-flex align-items-center">
            <h5 class="card-header">Districtwise Data  </h5>
          </div>
          <div class="table-responsive text-nowrap px-4">
          <table class="table" id="tblDistrictWise">
            <thead>
              <tr>
                <th>No.</th>
                <th>District Name</th>
                <th>No. of Diagnosed</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody class="table-border-bottom-0">
              @foreach ($districtWisejsonData as $index => $item)
                <tr>
                  {{-- @forelse ($item as $x => $val) --}}
                    <td>
                      <strong>{{$index + 1}}</strong>
                    </td>
                    <td>{{$item->district_name  }}</td>
                    <td>{{($item->total)}}</td>
                    <td>
                      <button class="btn btn-sm btn-outline-primary OpenDistrictWiseModalBtn" 
                      data-id="{{$item->district_cd}}">
    
                      <i class='bx bx-show'></i>Show</button>
                    </td>
                </tr> 
                @endforeach 
            </tbody>
          </table>
          </div>
        </div>
    {{-- </div>
  </div>
</div> --}}


{{-- Modal Window for Diseasewise Data --}}
<div class="modal modal-lg" id="diseaseWiseModal" tabindex="-1" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="diseaseWiseTitle">Disease Wise Data</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body modal-dialog-centered modal-lg" id='map' style='width: 700px; height: 500px;'>
          {{-- <form action="#" id="diseaseWiseDataForm">
            @csrf
          </form> --}}
      </div>
    </div>
  </div>
</div>


{{-- Modal Window for District Wise Data --}}
<div class="modal fade" id="districtWiseModal" tabindex="-1" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-dialog-centered  modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="districtWiseTitle">District Wise Data</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      
       
        <div class="modal-body mapboxgl-map  modal-lg" id='mapx' style='width: 700px; height: 500px;'>
          <form action="#" id="districtWiseDataForm">
            @csrf
          </form>
        </div>
    </div>
  </div>
</div>
@endsection

@section('custom_js')    
<script src='https://api.mapbox.com/mapbox-gl-js/v2.9.1/mapbox-gl.js'></script>
<link href='https://api.mapbox.com/mapbox-gl-js/v2.9.1/mapbox-gl.css' rel='stylesheet' />
<style>
.modal-lg {
  max-width: 80%;
}

.marker {
background-image: url("{{ asset('admin_assets/img/icons/mapbox_icons/mapbox-icon.png') }}");
background-size: cover;
width: 50px;
height: 50px;
border-radius: 50%;
cursor: pointer;
}
.mapboxgl-popup {
max-width: 200px;
}
.mapboxgl-popup-content {
text-align: center;
font-family: 'Open Sans', sans-serif;
}



</style>
<script>
  $(document).ready(function(){

    $("#tblDiseaseWise").on("click", ".OpenDiseaseWiseModalBtn", function(){

      $('#diseaseWiseModal').on('shown.bs.modal', function (e) {
        // $('#editState').focus();
        map.resize();
      }).modal('show');
    });


    $("#tblDistrictWise").on("click", ".OpenDistrictWiseModalBtn", function(){

      $('#districtWiseModal').on('shown.bs.modal', function (e) {
        // $('#editState').focus();
        mapx.resize();
      }).modal('show');
    });


    mapboxgl.accessToken = 'pk.eyJ1IjoiY3JvcGhlYWx0aDIyIiwiYSI6ImNsZmM3NmxwbzBobmMzeHBna3QwYTNqcDYifQ.cDrgvqT9hpd8JSWaOO-Cdw';
    const map = new mapboxgl.Map({
      container: 'map',
      style: 'mapbox://styles/crophealth22/clivl924z00y801qv28v3aoc1',
      center: [92.657,26.191],
      zoom: 6, 
    });

    const mapx = new mapboxgl.Map({
      container: 'mapx',
      style: 'mapbox://styles/crophealth22/clivl924z00y801qv28v3aoc1',
      center: [92.657,26.191],
      zoom: 6, 
    });

    $.getJSON('http://43.205.45.246:80/loadDistrictWiseGeoJsonData', function(jd) {
      geojson = JSON.parse(jd);
      for (const feature of geojson.features) 
      {
          // create a HTML element for each feature
          const el = document.createElement('div');
          el.className = 'marker';
          
          // make a marker for each feature and add it to the map
          new mapboxgl.Marker(el)
          .setLngLat(feature.geometry.coordinates)
          .setPopup(
          new mapboxgl.Popup({ offset: 25 }) // add popups
          .setHTML(`<h3>${feature.properties.title}</h3><p>${feature.properties.description}</p>`)
          ).addTo(map);


          new mapboxgl.Marker(el)
          .setLngLat(feature.geometry.coordinates)
          .setPopup(
          new mapboxgl.Popup({ offset: 25 }) // add popups
          .setHTML(`<h3>${feature.properties.title}</h3>
          <p>${feature.properties.description}</p>
          <p>${feature.district_name}</p>`)
          ).addTo(mapx);

          console.log(feature.properties.description);
      }
    });
    
    
// $('#diseaseWiseModal').DataTable();
   
  $('#queryHeader').text("Farmer Query Information");

  options = {
      chart: {
        type: 'bar',
        height: 300
      },
      plotOptions: {
        bar: {
          horizontal: true
        }
      },
      series: [{
        data: [{
          x: 'Queries Received',
          y: 560
        }, {
          x: 'Queries Moderated',
          y: 435
        }, {
          x: 'Queries Answered',
          y: 300
        }]
      }],
      title: {
          text: "Farmer Queries"
      }
    }

  var chartFarmersQryInfo = new ApexCharts(document.querySelector("#queryChart"),options);
  chartFarmersQryInfo.render();


  var optionsPiChart = {
      series: [80,30,90],
      chart: {
      width: '360',
      type: 'donut',
    },
    labels: ["General","Schemes", "FAQ"],
    dataLabels: {
      enabled: false
    },
    responsive: [{
      breakpoint: 480,
      options: {
        chart: {
          width: 200
        },
        legend: {
          show: false
        }
      }
    }],
    theme: {
      monochrome: {
        enabled: true
      }
    },
    // plotOptions: {
    //   pie: {
    //     dataLabels: {
    //       offset: -5
    //     }
    //   }
    // },
    title: {
      text: "Agri News Pie"
    },
    // dataLabels: {
    //   formatter(val, opts) {
    //     const name = opts.w.globals.labels[opts.seriesIndex]
    //     return [name, val.toFixed(1) + '%']
    //   }
    // },
    legend: {
      position: 'right',
      offsetY: 0,
      height: 230,
    }
  };

  var chartAgriNews = new ApexCharts(document.querySelector("#newsChart"), optionsPiChart);
  chartAgriNews.render();
  
  // createEmptyChart('line');

  // function createEmptyChart(chartType)
  // {
  //     var options = {
  //         chart: {
  //             height: 350,
  //             type: chartType,
  //         },
  //         dataLabels: {
  //             enabled: false
  //         },
  //         series: [],
  //         title: {
  //             text: 'Ajax Example',
  //         },
  //         noData: {
  //         text: 'Loading...'
  //         }
  //     }
      
  //     var chart = new ApexCharts(
  //         document.querySelector("#queryChart"),
  //         options
  //     );
  //     chart.render();
  // }
  $.ajax(
    {
      url : "{{route('admin.dashboard1')}}",
      type: "GET",
     
      headers: {
                    'X-CSRF-Token': '{{ csrf_token() }}',
                    'accept' : 'application/json'
                },
      contentType: "application/json",
      success: function(response)
      {
        chartFarmersQryInfo.updateSeries([{
                 name: 'Queries',
                  data: response
              }]);
      }
    });

  $.ajax(
    {
      url : "{{route('admin.jsondataAgriNews')}}",
      success: function(response)
      {
        var arr = chartAgriNews.w.globals.series.slice()
        arr[0]= response[0];
        arr[1] = response[1];
        arr[2]= response[2];
        chartAgriNews.updateSeries(arr);
      }
    });

  // $.getJSON("http://43.205.45.246:81/adminjson", function(response) {
  //   chartFarmersQryInfo.updateSeries([{
  //         name: 'Queries',
  //         data: response
  //     }]);
  // });


  // $.getJSON("http://43.205.45.246:81/adminjsonAgriNews", function(response) {
  //   var arr = chartAgriNews.w.globals.series.slice()
  //   arr[0]= response[0];
  //   arr[1] = response[1];
  //   arr[2]= response[2];
  //   chartAgriNews.updateSeries(arr);
  // });


  // $('#tableID').DataTable({
  //   "ajax": 'DataSetforDashboard_countDiseaseWise.txt'
  // });
  



 
  
});
</script>
{{-- <script src="{{asset('admin_assets/js/dashboard/admin.js')}}"></script> --}}
@endsection