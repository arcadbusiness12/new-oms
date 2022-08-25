@extends('layouts.app')

@section('content')

<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <div class="panel-heading">All Orders</div>
                        @if(session()->has('success'))
                        <div role="alert" class="alert alert-success">
                            {{ session()->get('success') }}
                        </div>
                        @endif
                        <div class="card-header white">
                            <h1>Testing...</h1>
                        </div>
                    </div> {{--  end class card no-b  --}}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

