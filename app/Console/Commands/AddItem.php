<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\SelfHandling;
use DirectoryIterator;
use App\Item;
use App\Category;
use App\File;

class AddItem extends Command implements SelfHandling
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:add {path} {category}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    public function addFilePath($path)
    {
        $directory = new DirectoryIterator($path);
        $dev = stat($path)[0];
        $files = [];
        $directories = [];
        
        foreach ($directory as $file) {
            if ($file->isDot() || !$file->isReadable()) {
                continue;
            }
            if ($file->isFile()) {
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
                $files[] = $f->id;
            } else if ($file->isDir()) {
                $directories[] = $file->getPathname();
            }
        }
        
        foreach ($directories as $dir) {
            $dirFiles = $this->addFilePath($dir);
            $files = array_merge($files, $dirFiles);
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
        $path = $this->argument('path');
        $defaultCategory = $this->argument('category');
        
        $this->line('Directory List:');
        foreach (scandir($path) as $name) {
            $this->warn($name);
        }
        if (!$this->confirm("Add ". substr($path, -100) ."?", true)) {
            return false;
        }
        
        $item = new Item(compact('path'));
        
        $item->title = $this->ask('What should we call this item? (type no to cancel)', basename($path));
        
        $choices = Category::all()->lists('name')->toArray();
        $category = $this->askWithCompletion('Which category?', $choices, $defaultCategory);
        
        $item->category()->associate($this->category($category));
        
        $item->save();
        
        $files = $this->addFilePath($path);
        $item->files()->sync($files);
    }
    
    protected function category($name)
    {
        $category = Category::where('name', $name)->get()->first();
        if (!is_null($category)) {
            return $category;
        }
        return Category::create([ 'name' => $name ]);
    }
}
