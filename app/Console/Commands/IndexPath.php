<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\File;
use App\Category;
use App\Item;

class IndexPath extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:index {path} {category}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Indexes the passed path';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function indexPath($path, Category $category)
    {
        $directory = new \DirectoryIterator($path);
        $dev = stat($path)[0];
        $files = [];
        
        foreach ($directory as $file) {
            if ($file->isDot() || !$file->isReadable()) continue;
            if ($file->isDir()) {
                $files = $this->indexPath($file->getPathname(), $category);
                if (count($files) > 0) {
                    $item = Item::where('path', $file->getRealPath())->get();
                    if (is_null($item) || !count($item)) {
                        $item = $category->items()->create([
                            'title' => $file->getFilename(),
                            'path' => $file->getRealPath(),
                        ]);
                    } else {
                        $item = $item->first();
                    }
                    foreach ($files as $ob) {
                        $ob->item()->associate($item);
                        $ob->save();
                    }
                }
            } else if ($file->isFile()) {
                $node = $file->getInode();
                if ($node <= 0 || $node == false) continue;
                $f = File::where('dev', $dev)
                    ->where('inode', $node)
                    ->get();
                if (is_null($f) || !count($f)) {
                    $f = new File(['dev' => $dev, 'inode' => $node ]);
                } else {
                    $f = $f->first();
                }
                $f->filename = $file->getFilename();
                $f->size = $file->getSize();
                if ($f->isDirty()) {
                    $f->save();
                }
                $files[] = $f;
            }
        }
        return $files;
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $start = microtime(true);
        $path = $this->argument('path');
        $categoryTitle = $this->argument('category');
        
        $category = Category::where('name', $categoryTitle)->get();
        if (is_null($category) || !count($category)) {
            $category = Category::create([ 'name' => $categoryTitle ]);
        } else {
            $category = $category->first();
        }
        $this->indexPath($path, $category);
        $end = microtime(true);
        $this->line("Took ".round($end-$start, 3). "s to index");
    }
}
