@extends('layouts.main')

@section('content')
<style>
    li{
        list-style-type: none;
    }
</style>
<div class="container">
    <div class="row">
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </div>
        @endif
        @if (Session::has('success'))
            <div class="alert alert-success">
                {{ Session::get('success') }}
            </div>
        @endif
    </div>
    <form action="{{ route('file.upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="col-md-4 d-flex">
            <input class="form-control mx-3" name="file_data" type="file">
            <input type="submit" class="btn btn-primary mx-3">
        </div>
    </form>
</div>

@endsection