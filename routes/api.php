<?php

use App\Http\Controllers\Api\RaceLogController;

Route::post('/race-logs', [RaceLogController::class, 'store']);

