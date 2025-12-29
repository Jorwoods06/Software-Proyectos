<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\VerificarTareasVencimiento;

Schedule::command(VerificarTareasVencimiento::class)->hourly();
