<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],


        'avatars' => [
            'driver' => 'local',
            'root' => storage_path('app/avatars'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],


        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],


        'images' => [
            'driver' => 'local',
            'root' => storage_path('app/images'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],


        'movies' => [
            'driver' => 'local',
            'root' => storage_path('app/movies'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        'upcoming' => [
            'driver' => 'local',
            'root' => storage_path('app/upcoming'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],


        'casts' => [
            'driver' => 'local',
            'root' => storage_path('app/casts'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],


        'series' => [
            'driver' => 'local',
            'root' => storage_path('app/series'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        'animes' => [
            'driver' => 'local',
            'root' => storage_path('app/animes'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],


        'preview' => [
            'driver' => 'local',
            'root' => storage_path('app/previews'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        'livetv' => [
            'driver' => 'local',
            'root' => storage_path('app/livetv'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],


        'settings' => [
            'driver' => 'local',
            'root' => storage_path('app/settings'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],


        'videos' => [
            'driver' => 'local',
            'root' => storage_path('app/videos'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        'substitles' => [
            'driver' => 'local',
            'root' => storage_path('app/substitles'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => config('aws_access_key_id'),
            'secret' => config('aws_secret_access_key'),
            'region' => config('aws_default_region'),
            'bucket' => config('aws_bucket'),
            'visibility' => 'public',
        ],

        'wasabi' => [
            'driver' => 'wasabi',
            'key' => config('wasabi_access_key_id'),
            'secret' => config('wasabi_secret_access_key'),
            'region' => config('wasabi_default_region'),
            'bucket' => config('wasabi_bucket'),
            'endpoint' => 'https://s3.wasabisys.com',
            'root' => '/'

        ],

    ],

];
