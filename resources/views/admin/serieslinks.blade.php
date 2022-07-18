@extends('layouts.admin')

@section('content')
    <!-- Breadcrumb-->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{route('admin')}}">Home</a>
        </li>
        <li class="breadcrumb-item active">animes Links</li>
    </ol>
    <div class="container-fluid">
        <animesvideos-component></animesvideos-component>
    </div>

@endsection
