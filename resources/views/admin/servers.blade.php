@extends('layouts.admin')

@section('content')
    <!-- Breadcrumb-->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{route('admin')}}">Home</a>
        </li>
        <li class="breadcrumb-item active">Streaming Quality</li>
    </ol>
    <div class="container-fluid">
        <streamingquality-component></streamingquality-component>
    </div>

@endsection
