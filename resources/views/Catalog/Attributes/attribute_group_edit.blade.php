@extends('layouts.app')

@section('content')

<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12">
            <div class="row mb-4">
                <div class="col-md-12 col-sm-12">

                    <div class="card no-b form-box">
                        @if(session()->has('success'))
                            <div class="alert alert-success">
                                {{ session()->get('success') }}
                            </div>
                        @endif
                </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <div class="panel-heading">Add Attribute Groups</div>
                            <form action="{{route('attribute.groups.update')}}" method="post" name="form-setting">
                                {{ csrf_field() }}
                                    <div class="form-group p-4">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <label>Name</label>
                                                <input type="text" name="name" class="form-control m-input" value="{{ old('name') ? old('name') : $row->name }}" placeholder="Enter Name" autocomplete="off">
                                                @error('name')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-6">
                                                <label>Sort Order</label>
                                                <input type="text" name="sort_order" value="{{ old('sort_order') ? old('sort_order') : $row->sort_order  }}" class="form-control m-input" placeholder="Enter Sort Order" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <input type="submit" name="submit" value="S A V E" class="btn btn-success btn-lg float-right">
                                            </div>
                                        </div>
                                    </div>
                            </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="toast-action" data-title="Hey, Bro!" data-message="Paper Panel has toast as well." data-type="success" data-position-class="toast-top-right"></div>
@endsection
