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
        $collection = Item::with('files')
            ->search($this->argument('term'))
            ->get()
            ->take($this->argument('results'));
        
        $rows = [];
        
        foreach ($collection as $item) {
            $path = $item->path;
            $rows[] = [
                'Item',
                $item->title,
                $path,
            ];
            
            foreach ($item->files as $file) {
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
