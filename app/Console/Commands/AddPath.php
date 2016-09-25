<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DirectoryIterator;
use App\Item;

class AddPath extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:path {path} {category}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds directories in the specified path';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        clearstatcache();
        $path = $this->argument('path');
        $category = $this->argument('category');
        
        $directory = new DirectoryIterator($path);
        
        foreach ($directory as $file) {
            if ($file->isDot() || !$file->isReadable()) {
                continue;
            }
            if ($file->isDir()) {
                $path = $file->getPathname();
                if (!Item::where('path', $path)->count()) {
                    $this->call('files:add', compact('path', 'category'));
                }
            }
        }
    }
}
