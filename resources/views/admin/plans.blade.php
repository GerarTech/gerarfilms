@extends('layouts.admin')

@section('content')
    <!-- Breadcrumb-->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{route('admin')}}">Home</a>
        </li>
        <li class="breadcrumb-item active">Plans & Subscriptions</li>
    </ol>
    <div class="container-fluid">
        <plans-component></plans-component>
    </div>

@endsection
