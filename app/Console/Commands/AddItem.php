<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\SelfHandling;
use App\Item;
use App\Category;
use App\Jobs\ScanItemFiles;

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
        
        $item = new Item;
        
        $name = basename($path);
        $item->title = $this->ask('Rename this item? (type no to cancel)', $name);
        
        if ($item->title !== $name) {
            $newPath = dirname($path).DIRECTORY_SEPARATOR.$item->title;
            rename($path, $newPath);
            $path = $newPath;
        }
        
        $item->path = $path;
        
        $choices = Category::all()->lists('name')->toArray();
        $category = $this->askWithCompletion('Which category?', $choices, $defaultCategory);
        
        $item->category()->associate($this->category($category));
        
        $item->save();
        
        dispatch(new ScanItemFiles($item, $path));
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
