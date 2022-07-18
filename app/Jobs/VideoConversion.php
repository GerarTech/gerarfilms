<?php

namespace App\Jobs;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class VideoConversion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct($data)
    {
        $this->data = $data;
    }


    public function handle()
    {
 $lowBitrate = (new X264)->setKiloBitrate($settings->low_bitrate);
   $midBitrate = (new X264)->setKiloBitrate($settings->medium_bitrate);
    $highBitrate = (new X264)->setKiloBitrate($settings->high_bitrate);
                
          FFMpeg::fromDisk('videos')
            ->open($filename)
               ->exportForHLS()
              ->addFormat($lowBitrate)
             ->addFormat($midBitrate)
            ->addFormat($highBitrate) 
         ->save('adaptive_steve.m3u8');
         
    }
}
