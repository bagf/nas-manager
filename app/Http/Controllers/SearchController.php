<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{
    protected function cpuTemp()
    {
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
                    return substr($cpuTemp, 0, $cePos+4);
                }
            }
        }
    }
    
    protected function hddUsages()
    {
        if (is_executable('/bin/df')) {
            $command = 'df -h --output=source,used,avail,pcent -B GB | grep "/dev/*"';
            $result = [];
            exec($command, $result);
            $stats = [];
            if (count($result) > 0) {
                for($i=0;$i<count($result);$i++) {
                    $stat = explode(' ', $result[$i]);
                    $stat = array_filter($stat, function($ob) {
                        return !empty($ob);
                    });
                    $stat = array_values($stat);
                    $stats[] = [
                        'source' => $stat[0],
                        'used' => $stat[1],
                        'avail' => $stat[2],
                        'pcent' => $stat[3],
                        'total' => str_replace('GB', '', $stat[1])+str_replace('GB', '', $stat[2])."GB"
                    ];
                }
            }
            return $stats;
        }
    }
    
    public function index(Request $request)
    {
        $cpuTemp = $this->cpuTemp();
        $hdds = $this->hddUsages();
    
        return view('welcome')
            ->with('hddUsage', $hdds)
            ->with('useCpuTemp', !is_null($cpuTemp))
            ->with('cpuTemp', $cpuTemp);
    }
}
