<div id="myCarousel" class="carousel slide" data-interval="false" >
    <!-- Indicators -->
    <!-- <ol class="carousel-indicators">
      <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
      <li data-target="#myCarousel" data-slide-to="1"></li>
      <li data-target="#myCarousel" data-slide-to="2"></li>
    </ol> -->

    <!-- Wrapper for slides -->
    <div class="carousel-inner">
     @if(count($files) > 0)
      @foreach($files as $k => $file)

      <div class="item {{$file->id == $select_file ? 'active' : ''}}">
      @if(in_array($file->extension, $file_extensions))
        <img src="{{asset($file->file)}}" alt="Los Angeles" style="width:100%;">
      @else
        <video width="100%" controls class="video-tag">
          <source src="{{asset($file->file)}}" type="video/{{$file->extension}}">
            Your browser does not support HTML video.
           </video>
      @endif
      </div>
     @endforeach
    @else
    <div class="item {{$k ==0 ? 'active' : ''}}">
        <h5>No Attachemnt</h5>
      </div>
    @endif
      
    </div>

    <!-- Left and right controls -->
    <a class="left carousel-control" href="#myCarousel" data-slide="prev">
      <span class="glyphicon glyphicon-chevron-left"></span>
      <span class="sr-only">Previous</span>
    </a>
    <a class="right carousel-control" href="#myCarousel" data-slide="next">
      <span class="glyphicon glyphicon-chevron-right"></span>
      <span class="sr-only">Next</span>
    </a>
  </div>