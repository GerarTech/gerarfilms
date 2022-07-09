<?php

namespace App\Http\Controllers;

use App\Http\Requests\LivetvRequest;
use App\Http\Requests\StoreImageRequest;
use App\Jobs\SendNotification;
use App\Livetv;
use App\LivetvGenre;
use App\LivetvVideo;
use App\Category;
use App\Setting;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class LivetvController extends Controller
{
    

    public function show($movie)
    {


        $streaming = Livetv::with(['videos'])->where('id', '=', $movie)->first();

        $streaming->increment('views',1);
        
        return response()->json($streaming, 200);

    }



    public function addtofav($movie,Request $request)
    {

    $movie = Livetv::where('id', '=', $movie)->first()->addFavorite($request->user()->id);
 
            return response()->json("Success", 200);

    }


    public function removefromfav($id,Request $request)
    {

        $movie = Livetv::where('id', '=', $id)->first()->removeFavorite($request->user()->id);


        return response()->json("Added", 200);


    }


    public function isMovieFavorite($id,Request $request)
    {

        $movie = Livetv::where('id', '=', $id)->first();

        if($movie->isFavorited($request->user()->id)) {

            $data = ['status' => 200, 'status' => 1,];

        }else {
            
            $data = ['status' => 400, 'status' => 0,];
        }

        return response()->json($data, 200);
    }



    public function relateds(Livetv $movie)
    {
        
 
        $genre = $movie->genres[0]->category_id;
        $movies = LivetvGenre::where('category_id', $genre)->where('livetv_id', '!=', $movie->id)
            ->limit(6)
            ->get();
        $movies->load('livetv')->where('active', '=', 1);
        $relateds = [];
        foreach ($movies as $item) {
            array_push($relateds, $item['livetv']);
        }

        return response()->json(['relateds' => $relateds], 200);
    }





    public function latest()
    {

        $streaming = Livetv::orderByDesc('created_at')->where('active', '=', 1)
            ->limit(10)
            ->get();

        return response()
            ->json(['livetv' => $streaming], 200);
    }






    public function featured()

    {

     $movies = Livetv::where('featured', 1)->where('active', '=', 1)
            ->orderByDesc('created_at')
            ->get();
        return response()
            ->json(['featured_streaming' => $movies], 200);

    }


    public function mostwatched()

    {

        $movies = Livetv::orderByDesc('views')->where('active', '=', 1)
            ->get();


        return response()
            ->json(['watched' => $movies], 200);

    }


    // returns all livetv for admin panel
    public function data()
    {

        return response()->json(Livetv::with(['videos'])->orderByDesc('created_at')
        ->paginate(6), 200);
    }

    // create a new livetv in the database
    public function store(LivetvRequest $request)
    {
        if (isset($request->livetv)) {

            $livetv = new Livetv();
            $livetv->fill($request->livetv);
            $livetv->save();



            if ($request->livetv['genres']) {
                foreach ($request->livetv['genres'] as $genre) {

                    $find = Category::find($genre['id']);
                    if ($find == null) {
                        $find = new Category();
                        $find->fill($genre);
                        $find->save();
                    }
                    $movieGenre = new LivetvGenre();
                    $movieGenre->category_id = $genre['id'];
                    $movieGenre->livetv_id = $livetv->id;
                    $movieGenre->save();
                }
            }


            $this->onStoreVideo($request,$livetv);




            $data = [
                'status' => 200,
                'message' => 'successfully created',
                'body' => $livetv
            ];
        } else {
            $data = [
                'status' => 400,
                'message' => 'could not be created',
            ];
        }

        if ($request->notification) {
            $this->dispatch(new SendNotification($livetv));
        }

        return response()->json($data, $data['status']);
    }





    public function onStoreVideo($request,$livetv) {

        if ($request->links) {
            foreach ($request->links as $link) {

                $movieVideo = new LivetvVideo();
                $movieVideo->fill($link);
                $movieVideo->livetv_id = $livetv->id;
                $movieVideo->save();
            }
        }

    }





    // update a livetv in the database
    public function update(LivetvRequest $request, Livetv $livetv)
    {
        if ($livetv != null) {

            $livetv->fill($request->livetv);
            $livetv->save();


            if ($request->livetv['genres']) {
                foreach ($request->livetv['genres'] as $genre) {
                    if (!isset($genre['category_id'])) {
                        $find = Category::find($genre['id'] ?? 0) ?? new Category();
                        $find->fill($genre);
                        $find->save();
                        $movieGenre = LivetvGenre::where('livetv_id', $livetv->id)
                            ->where('category_id', $genre['id'])->get();
                        if (count($movieGenre) < 1) {
                            $movieGenre = new LivetvGenre();
                            $movieGenre->category_id = $genre['id'];
                            $movieGenre->livetv_id = $livetv->id;
                            $movieGenre->save();
                        }
                    }
                }
            }


            $this->onUpdateVideo($request,$livetv);


            $data = [
                'status' => 200,
                'message' => 'successfully updated',
                'body' => $livetv
            ];
        } else {
            $data = [
                'status' => 400,
                'message' => 'could not be updated',
            ];
        }

        return response()->json($data, $data['status']);
    }



    public function onUpdateVideo($request,$livetv) {

        if ($request->links) {
            foreach ($request->links as $link) {
                if (!isset($link['id'])) {
                    $movieVideo = new LivetvVideo();
                    $movieVideo->livetv_id = $livetv->id;
                    $movieVideo->fill($link);
                    $movieVideo->save();
                }
            }
        }

    }


     // remove the genre of a movie from the database
     public function destroyGenre($genre)
     {
        if ($genre != null) {

            LivetvGenre::find($genre)->delete();

            $data = ['status' => 200, 'message' => 'successfully deleted',];
        } else {
            $data = ['status' => 400, 'message' => 'could not be deleted',];
        }

        return response()->json($data, 200);
     }

    // delete a livetv in the database
    public function destroy(Livetv $livetv)
    {
        if ($livetv != null) {
            $livetv->delete();
            $data = [
                'status' => 200,
                'message' => 'successfully deleted',
            ];
        } else {
            $data = [
                'status' => 400,
                'message' => 'could not be deleted',
            ];
        }

        return response()->json($data, $data['status']);
    }

    // save a new image in the livetv folder of the storage
    public function storeImg(StoreImageRequest $request)
    {
        if ($request->hasFile('image')) {
            $filename = Storage::disk('livetv')->put('', $request->image);
            $data = [
                'status' => 200,
                'image_path' => $request->root() . '/api/livetv/image/' . $filename,
                'message' => 'successfully uploaded'
            ];
        } else {
            $data = [
                'status' => 400,
            ];
        }

        return response()->json($data, $data['status']);
    }

    // return an image from the livetv folder of the storage
    public function getImg($filename)
    {

        $image = Storage::disk('livetv')->get($filename);

        $mime = Storage::disk('livetv')->mimeType($filename);

        return (new Response($image, 200))
            ->header('Content-Type', $mime);
    }



     // remove a video from a movie from the database
     public function videoDestroy($video)
     {
         if ($video != null) {
 
            LivetvVideo::find($video)->delete();
 
             $data = ['status' => 200, 'message' => 'successfully deleted',];
         } else {
             $data = ['status' => 400, 'message' => 'could not be deleted',];
         }
 
         return response()->json($data, 200);
     }
 


       // return all the videos of a stream
       public function videos($movie)
       {

        $streaming = Livetv::where('id', '=', $movie)->first();
        
        return response()->json($streaming->videos, 200);
       }
   
}
