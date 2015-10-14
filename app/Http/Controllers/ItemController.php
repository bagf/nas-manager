<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ItemRequest;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Item;

class ItemController extends Controller
{

    protected function formatBytes($size, $precision = 2)
    {
        $base = log($size, 1024);
        $suffixes = array('', 'k', 'M', 'G', 'T');

        if (!isset($suffixes[$base])) {
            return $size;
        }
        return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
    }

    public function getFiles(ItemRequest $request)
    {
        $item = Item::findOrFail($request->get('item_id'));
        $list = [];
        $size = 0;
        
//        foreach ($item->items as $item) {
//            $list[] = [
//                'file' => $item->title,
//                'path' => $item->path,
//                'id' => $item->id,
//            ];
//        }
        
        foreach ($item->files()->get() as $file) {
            $size += $file->size;
            $list[] = [
                'file' => $file->filename,
                'path' => "{$item->path}/{$file->filename}",
                'size' => $this->formatBytes($file->size),
            ];
        }
        
        return response()->json([
            'files' => $list,
            'count' => count($list),
            'size' => $this->formatBytes($size)
        ]);
    }
}
