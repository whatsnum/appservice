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
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
        ],

        'user_avatars' => [
            'driver'      => env('FILESYSTEM_DRIVER', 'local'),
            'root'        =>  base_path('/images/user_avatars'),
            'visibility'  => 'public',
            'url'         => env('APP_URL').'/'. 'images/user_avatars',
        ],

        'msg_images' => [
          'driver' => env('FILESYSTEM_DRIVER', 'local'),
          'root'   =>  base_path('/medias/msg_images'),
          'visibility' => 'public',
          'url'         => env('APP_URL').'/medias/msg_images',
        ],

        'msg_videos' => [
          'driver' => env('FILESYSTEM_DRIVER', 'local'),
          'root'   =>  base_path('/medias/msg_videos'),
          'visibility' => 'public',
          'url'         => env('APP_URL').'/medias/msg_videos',
        ],

        'user_galleries' => [
          'driver' => env('FILESYSTEM_DRIVER', 'local'),
          'root'   =>  base_path('/images/user_gallery'),
          'visibility' => 'public',
          'url'         => env('APP_URL').'/'. 'images/user_gallery',
        ],

        'user_post_images' => [
            'driver' => env('FILESYSTEM_DRIVER', 'local'),
            'root'   =>  base_path('/images/user_post_images'),
            'visibility' => 'public',
            'url'         => env('APP_URL').'/'. 'images/user_post_images',
        ],
        'user_group_photos' => [
            'driver' => env('FILESYSTEM_DRIVER', 'local'),
            'root'   =>  base_path('/images/user_group_photos'),
            'visibility' => 'public',
            'url'         => env('APP_URL').'/'. 'images/user_group_photos',
        ],
        'user_covers' => [
          'driver' => env('FILESYSTEM_DRIVER', 'local'),
          'root'   =>  base_path('/images/user_covers'),
          'visibility' => 'public',
          'url'         => env('APP_URL').'/images/user_covers',
        ],
        'activity_images' => [
          'driver' => env('FILESYSTEM_DRIVER', 'local'),
          'root'   =>  base_path('/images/activity_images'),
          'visibility' => 'public',
          'url'         => env('APP_URL').'/images/activity_images',
        ],
    ],

];
