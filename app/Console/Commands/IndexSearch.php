<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\File;
use App\Item;
use App\Category;

class IndexSearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:search {term} {results}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Search file names and items';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $start = microtime(true);
        $terms = explode(':', $this->argument('term'));
        
        if (count($terms) === 2) {
            $term = $terms[1];
            $collection = Item::whereHas('category', function($query) use ($terms) {
                $query->where('name', 'LIKE', "%{$terms[0]}%");
            })
                ->where('title', 'LIKE', "%{$term}%")
                ->get()
                ->take($this->argument('results'));
        } else {
            $term = $terms[0];
            $collection = Item::with('files')
                ->where('title', 'LIKE', "%{$term}%")
                ->get()
                ->take($this->argument('results'));
        }
        
        $rows = [];
        
        $c = count($collection);
        if ($c > 0 && $this->argument('results') > 0) {
            $limit = $this->argument('results') / $c;
        } else {
            $limit = 100;
        }
        
        foreach ($collection as $item) {
            $path = $item->path;
            $rows[] = [
                'Item',
                $item->title,
                $path,
            ];
            $files = $item->files()
                ->where('filename', 'LIKE', "%{$term}%")
                ->get()
                ->take($limit);
            foreach ($files as $file) {
                $rows[] = [
                    'File',
                    "- {$file->filename}",
                    "{$path}/{$file->filename}",
                ];
             }
        }
        
        $end = microtime(true);
        $this->table(['type', 'filename', 'path'], $rows);
        $this->line("Took ".round($end-$start, 3). "s to search");
    }
}
