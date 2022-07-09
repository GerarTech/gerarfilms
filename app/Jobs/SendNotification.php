<?php

namespace App\Jobs;

use App\Livetv;
use App\Movie;
use App\Anime;
use App\Serie;
use App\Setting;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    const FIREBASE_URL = "https://fcm.googleapis.com/fcm/send";
    const NOTIFS = "/topics/all";
    const NOTIFICATION = "notification";
    const TITLE = "title";
    const ADDED = "has been added";
    const IMAGE = "image";
    const CLICK_ACTION = "click_action";
    

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $settings = Setting::find(1);
        $client = new Client(['headers' => ['Authorization' => "key=$settings->authorization", 'Content-Type' => 'application/json']]);


        try {
            
            if ($this->data instanceof Movie) {
                $client->post(self::FIREBASE_URL, [
                    'json' => [
                        'to' => self::NOTIFS,
                        'data' => ['instanceof' => 'movie', 'tmdb' => $this->data->id,'type' => "0",'title' => $this->data->title
                        ,'message' => $this->data->overview,'image' => $this->data->backdrop_path]
                    ]
                ]);
            }
            if ($this->data instanceof Serie) {
                $client->post(self::FIREBASE_URL, [
                    'json' => [
                        'to' => self::NOTIFS,
                        'data' => ['instanceof' => 'serie', 'tmdb' => $this->data->id,'type' => "1",'title' => $this->data->name
                        ,'message' => $this->data->overview,'image' => $this->data->backdrop_path]
                    ]
                ]);
            }


            if ($this->data instanceof Anime) {
                $client->post(self::FIREBASE_URL, [
                    'json' => [
                        'to' => self::NOTIFS,
                        'data' => ['instanceof' => 'anime', 'tmdb' => $this->data->id,'type' => "2",'title' => $this->data->name
                        ,'message' => $this->data->overview,'image' => $this->data->backdrop_path]
                    ]
                ]);
            }

            if ($this->data instanceof Livetv) {
                $client->post(self::FIREBASE_URL, [
                    'json' => [
                        'to' => self::NOTIFS,
                        'data' => ['instanceof' => 'livetv', 'imdb' => $this->data->id,'type' => "3",'title' => $this->data->name
                        ,'message' => $this->data->overview,'image' => $this->data->backdrop_path,'link' => $this->data->link]
                    ]
                ]);
            }
            $status = 'success';
        } catch (ClientException $ce) {
            $status = 'error';
        } catch (RequestException $re) {
            $status = 'error';
        } catch (Exception $e) {
            $status = 'error';
        }

        return ['status' => $status];
    }
}
