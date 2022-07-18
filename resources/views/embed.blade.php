<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="Movies, TV Shows and Live TV">
    <title>{{ config('app.name', 'EASYPLEX') }}</title>
    <style>
        body {
            background-color: black !important;
        }

        .video {
            width: 99% !important;
            height: 45vw !important;
            margin: auto;
        }
    </style>

</head>
<body>

{!! $embed !!}

<script>
    var x = document.getElementsByTagName("div");
    for (var i = 0; i < x.length; i++)
        x[i].className += " video";
</script>
<script>
    var y = document.getElementsByTagName("iframe");
    for (var i = 0; i < y.length; i++)
        y[i].className += " video";
</script>
</body>
</html>
