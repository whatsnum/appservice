<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

use Laravel\Passport\Console\ClientCommand;
use Laravel\Passport\Console\InstallCommand;
use Laravel\Passport\Console\KeysCommand;

use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
      Validator::extend('imageable', function ($attribute, $value, $params, $validator) {
        if (!$value) {
          return true;
        }
        try {
            Image::make($value);
            return true;
        } catch (\Exception $e) {
            return false;
        }
      });

      Schema::defaultStringLength(191);
      $this->commands([
        InstallCommand::class,
        ClientCommand::class,
        KeysCommand::class,
      ]);
    }
}
