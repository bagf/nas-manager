<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SearchRequest;
use App\Item;
use App\Category;

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

    protected function formatBytes($size, $precision = 2)
    {
        $base = log($size, 1024);
        $suffixes = array('', 'k', 'M', 'G', 'T');

        if (!isset($suffixes[$base])) {
            return $size;
        }
        return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
    }

    public function search(SearchRequest $request)
    {
        $items = Item::with('files')
            ->search($request->get('term'))
            ->get()
            ->take(1024);
        $list = [];
        
        foreach ($items as $item) {
            $path = $item->path;
            $size = 0;
            $files = [];
            foreach ($item->files as $file) {
                $size += $file->size;
                $files[] = [
                    'file' => $file->filename,
                    'path' => "{$path}/{$file->filename}",
                    'size' => $this->formatBytes($file->size),
                ];
            }
            $list[] = [
                'file' => $item->title,
                'path' => $path,
                'id' => $item->id,
                'size' => $this->formatBytes($size),
                'files' => $files,
            ];
        }
        
        return response()->json([ 'files' => $list, 'count' => count($list) ]);
    }
    
    public function index(Request $request)
    {
        $cpuTemp = $this->cpuTemp();
        $hdds = $this->hddUsages();
    
        return view('welcome')
            ->with('hddUsage', $hdds)
            ->with('useCpuTemp', !is_null($cpuTemp))
            ->with('cpuTemp', $cpuTemp)
            ->with('categories', Category::all());
    }
}
