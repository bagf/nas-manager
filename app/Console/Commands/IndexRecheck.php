<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\File;
use App\Item;

class IndexRecheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:recheck';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process each database entry validating their existance in the file system';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function checkExistance($file)
    {
        return (is_null($file) || !file_exists($file) || !is_readable($file));
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        clearstatcache();
        $records = 0;
        $deleted = 0;
        
        foreach (File::with('item')->get() as $fileOb) {
            $file = $fileOb->getFilepath();
            if ($this->checkExistance($file) || fileinode($file) !== $fileOb->inode) {
                $fileOb->delete();
                $deleted++;
            }
            $records++;
        }
        
        foreach (Item::doesntHave('files')->get() as $item) {
            $file = $item->path;
            if ($this->checkExistance($file)) {
                $item->delete();
                $deleted++;
            }
            $records++;
        }
        
        $this->line("{$records} processed");
        $this->line("{$deleted} deleted");
    }
}
