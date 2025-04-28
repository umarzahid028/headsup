<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
  

Schedule::command('import:vehicles-csv --archive')->everyMinute();