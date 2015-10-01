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
        $plPos = strpos($result, '+');
        if ($plPos !== false) {
            $cpuTemp = substr($result, $plPos);
            $cePos = strpos($cpuTemp, '°C');
            if ($cePos === false) {
                $cePos = strpos($cpuTemp, ' C');
            }
            if ($cePos !== false) {
                $useCpuTemp = true;
                $cpuTemp = substr($cpuTemp, 0, $cePos+4);
            }
        }
    }
    return view('welcome')
        ->with('useCpuTemp', $useCpuTemp)
        ->with('cpuTemp', $cpuTemp);
});
