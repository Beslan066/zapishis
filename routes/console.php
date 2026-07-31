<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Artisan::command('app:health', function () {
    $this->info('Application is healthy!');
    $this->info('PHP version: ' . PHP_VERSION);
    $this->info('Laravel version: ' . app()->version());
})->purpose('Check application health');
