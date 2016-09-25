<?php

namespace App\Jobs;

use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Item;
use App\File;
use DirectoryIterator;

class ScanItemFiles extends Job implements ShouldQueue, SelfHandling
{
    use InteractsWithQueue, SerializesModels;
    
    protected $item;
    protected $path;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Item $item, $path)
    {
        $this->item = $item;
        $this->path = $path;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $files = $this->addFilePath($this->path);
        $this->item->files()->sync($files);
    }
    
    protected function addFilePath($path)
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
}
