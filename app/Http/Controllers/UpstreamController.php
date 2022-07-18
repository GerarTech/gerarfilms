<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UpstreamController extends Controller
{



    public function __construct()
    {
        $this->middleware('doNotCacheResponse', ['only' => ['streamtape']]);
    }

     public $name = 'Streamtape';
    private $id = '';
    private $title = '';
    private $image = '';
    private $referer = '';
    private $status = 'fail';
    private $url = 'https://strtpe.link/e/';
    private $cookies = [];
    private $tracks = [];
    private $ch;


public function streamtape(Request $request)
{ 

    $this->geturl($request->url,$request->url);

    $id = $this->get_sources();

     return response()->json($id, 200);
 }



 public function geturl($url,$id)
    {

    
        session_write_close();
        if (!empty($id)) {
            $id = strtr(rtrim($id, '/'), ['/e/' => '', '/f/' => '', '/v/' => '']);
            $id = explode('/', $id);
            $this->id = end($id);
            $this->url = $url;


            $scheme = parse_url($this->url, PHP_URL_SCHEME);
            $host = parse_url($this->url, PHP_URL_HOST);
            $port = parse_URL($this->url, PHP_URL_PORT);
            if (empty($port)) $port = $scheme == 'https' ? 443 : 80;
            $ipv4 = gethostbyname($host);
            $resolveHost = implode(':', array($host, $port, $ipv4));

            session_write_close();
            $this->ch = curl_init();
            curl_setopt($this->ch, CURLOPT_URL, $this->url);
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($this->ch, CURLOPT_MAXREDIRS, 5);
            curl_setopt($this->ch, CURLOPT_ENCODING, '');
            curl_setopt($this->ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($this->ch, CURLOPT_RESOLVE, array($resolveHost));
            curl_setopt($this->ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($this->ch, CURLOPT_TCP_NODELAY, 1);
            curl_setopt($this->ch, CURLOPT_FORBID_REUSE, 1);
            curl_setopt($this->ch, CURLOPT_REFERER, $this->url);
            curl_setopt($this->ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Linux; U; Android 2.2) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1");
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, array(
                'accept: */*',
                "cache-control: no-cache",
                'pragma: no-cache',
                'Connection: keep-alive'
            ));
            curl_setopt($this->ch, CURLOPT_HEADERFUNCTION, function ($ch, $head) {
                if (preg_match('/^Set-Cookie:\s*([^;]*)/mi', $head, $cookie) && !empty($cookie[1])) {
                    $this->cookies[] = $cookie[1];
                }
                return strlen($head);
            });
        }
    }

    private function getNewSources(array $oldSources = [])
    {
        session_write_close();
        $mh = curl_multi_init();
        $ch = [];

        foreach ($oldSources as $i => $dt) {
            $host = parse_url($dt['file'], PHP_URL_HOST);
            $ch[$i] = curl_init($dt['file']);
            curl_setopt($ch[$i], CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch[$i], CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch[$i], CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch[$i], CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch[$i], CURLOPT_MAXREDIRS, 5);
            curl_setopt($ch[$i], CURLOPT_ENCODING, '');
            curl_setopt($ch[$i], CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch[$i], CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($ch[$i], CURLOPT_TCP_NODELAY, 1);
            curl_setopt($ch[$i], CURLOPT_FORBID_REUSE, 1);
            curl_setopt($ch[$i], CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($ch[$i], CURLOPT_TIMEOUT, 30);
            curl_setopt($ch[$i], CURLOPT_HEADER, 1);
            curl_setopt($ch[$i], CURLOPT_NOBODY, 1);
            curl_setopt($ch[$i], CURLOPT_HTTPHEADER, array(
                'accept: */*',
                "cache-control: no-cache",
                'pragma: no-cache',
                'Connection: keep-alive',
                'range: bytes=0-'
            ));
            curl_setopt($ch[$i], CURLOPT_COOKIE, trim(implode(';', $this->cookies), ';'));
            curl_setopt($ch[$i], CURLOPT_REFERER, $this->referer);
            curl_setopt($ch[$i], CURLOPT_USERAGENT, "Mozilla/5.0 (Linux; U; Android 2.2) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1");
            curl_multi_add_handle($mh, $ch[$i]);
        }

        $active = null;
        do {
            $mrc = curl_multi_exec($mh, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);

        while ($active && $mrc == CURLM_OK) {
            if (curl_multi_select($mh) == -1) {
                usleep(10);
            }
            do {
                $mrc = curl_multi_exec($mh, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);
        }

        $result = [];
        foreach ($oldSources as $i => $dt) {
            $newUrl = curl_getinfo($ch[$i], CURLINFO_EFFECTIVE_URL);
            if (strpos($newUrl, 'streamtape_do_not_delete') === FALSE) {
                if (!empty($newUrl) && $dt['file'] !== $newUrl) {
                    $result[] = $newUrl;
                } else {
                    $result[] = [
                        'file' => $dt['file'],
                        'label' => $dt['label'],
                        'type' => $dt['type']
                    ];
                }
            }
            curl_multi_remove_handle($mh, $ch[$i]);
        }
        curl_multi_close($mh);
        return $result;
        
    }

    function get_sources()
    {
        session_write_close();
        if (!empty($this->id)) {
            session_write_close();
            $response = curl_exec($this->ch);
            $status = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
            $err = curl_error($this->ch);

            if ($status >= 200 && $status < 400) {
                if (strpos($response, '<video') !== FALSE) {
                    $dom = \KubAT\PhpSimple\HtmlDomParser::str_get_html($response);
                    $attr = 'poster';
                    $ex = explode('vidconfig =', $response, 2);
                    $ex = explode(';', end($ex), 2);
                    $ex = @json_decode(trim($ex[0]), TRUE);
                    $this->status = 'ok';
                    $this->referer = $this->url;
                    $this->image = $dom->find('#mainvideo', 0)->$attr;
                    $this->title = isset($ex['showtitle']) ? $ex['showtitle'] : '';

                    $tracks = $dom->find('track[kind="captions"]');
                    if (!empty($tracks)) {
                        foreach ($tracks as $dt) {
                            $this->tracks[] = [
                                'file' => $dt->src,
                                'label' => $dt->label
                            ];
                        }
                    }

                    $getVideo = explode('innerHTML = "', $response, 2);
                    $getVideo = explode("')", end($getVideo), 2);
                    $getVideo = parse_url($getVideo[0], PHP_URL_QUERY);
                    parse_str($getVideo, $qry);
                    $getVideo = 'https://strtpe.link/get_video?' . http_build_query($qry) . '&stream=1';

                    $result[] = [
                        'file' => $getVideo,
                        'type' => 'video/mp4',
                        'label' => 'Original'
                    ];


                    $data = ['status' => 200, 'url' => $getVideo];
                    return $data;
                }
            } else {
               
            }
        }
        return [];
    }

    function get_cookies()
    {
        session_write_close();
        return $this->cookies;
    }

    function get_tracks()
    {
        session_write_close();
        return $this->tracks;
    }

    function get_status()
    {
        session_write_close();
        return $this->status;
    }

    function get_title()
    {
        session_write_close();
        return $this->title;
    }

    function get_image()
    {
        session_write_close();
        return $this->image;
    }

    function get_referer()
    {
        session_write_close();
        return $this->referer;
    }

    function get_id()
    {
        session_write_close();
        return $this->id;
    }

    function __destruct()
    {
        session_write_close();
        curl_close($this->ch);
    }
 
}
