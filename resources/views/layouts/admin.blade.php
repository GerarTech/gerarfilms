<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <base href="{{route('admin')}}">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="Movies, TV Shows and Live TV">


    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <!-- Title -->
    <title>{{ config('app.name') }}</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}"/>


     <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/base/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">

    

</head>
<body>
<div class="container-scroller">

@include('layouts.header')

<!-- partial -->
    <div class="main-panel">
        <div class="content-wrapper">


            <main id="app">
                @yield('content')
            </main>
        </div>
    </div>
 
</div>


<!-- Scripts -->
<script type="text/javascript">window.url = <?php echo json_encode(url('/')); ?>;</script>



<script src="{{ asset('js/manifest.js') }}"></script>   
<script src="{{ asset('js/vendor.js') }}"></script>   
    <script src="{{ asset('js/app.js') }}"></script>    
    <script src="{{ asset('js/custom.js') }}"></script>
    <script src="https://kit.fontawesome.com/91264baf3a.js" crossorigin="anonymous"></script>


  

</body>
</html>
