<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    $useCpuTemp = false;
    $cpuTemp = 0;
    if (is_executable('/usr/bin/sensors')) {
        $command = 'sensors | grep Physical\ id\ 0:';
        $result = exec($command);
        $cePos = strpos($result, ' C');
        $plPos = strpos($result, '+');
        if ($cePos !== false && $plPos !== false) {
            $useCpuTemp = true;
            $cpuTemp = substr($r, $plPos, $cePos);
        }
    }
    return view('welcome')
        ->with('useCpuTemp', $useCpuTemp)
        ->with('cpuTemp', $cpuTemp);
});
