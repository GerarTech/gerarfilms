@extends('layouts.admin')

@section('content')
    <!-- Breadcrumb-->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{route('admin')}}">Home</a>
        </li>
        <li class="breadcrumb-item active">Featured Movies</li>
    </ol>
    <div class="container-fluid">
        <featured-component></featured-component>
    </div>

@endsection
