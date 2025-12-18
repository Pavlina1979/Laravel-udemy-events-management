<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
  $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
Artisan::command('app:send-event-reminders', function () {
  $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
