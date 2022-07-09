<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Requests\CategoryRequest;
use App\Livetv;
use Illuminate\Http\Request;

class CategoryController extends Controller
{





    // returns all genres for the api
    public function index()
    {
        return response()->json(Category::All(), 200);
    }

    // returns all genres for the admin panel
    public function data()
    {
        return response()->json(Category::All(), 200);
    }

    // create a new genre in the database
    public function store(CategoryRequest $request)
    {
        $genre = new Category();
        $genre->fill($request->all());
        $genre->save();

        $data = [
            'status' => 200,
            'message' => 'successfully created',
            'body' => $genre
        ];

        return response()->json($data, $data['status']);
    }

    // delete a genre from the database
    public function destroy(Category $genre)
    {
        if ($genre != null) {
            $genre->delete();
            $data = [
                'status' => 200,
                'message' => 'successfully deleted'
            ];
        } else {
            $data = [
                'status' => 400,
                'message' => 'could not be deleted'
            ];
        }

        return response()->json($data, $data['status']);
    }

    // update a genre in the database
    public function update(CategoryRequest $request, Category $genre)
    {
        if ($genre != null) {
            $genre->fill($request->all());
            $genre->save();
            $data = [
                'status' => 200,
                'message' => 'successfully updated',
                'body' => $genre
            ];
        } else {
            $data = [
                'status' => 400,
                'message' => 'could not be updated'
            ];
        }

        return response()->json($data, $data['status']);
    }

    // return all genres only with the id and name properties
    public function list()
    {
        
        $stream = Category::all();
       
        return response()->json(['categories' => $stream], 200);
    }

 

    public function streamingall()
    {


        $serie = Livetv::where('active', '=', 1)->orderByDesc('created_at')
        ->paginate(12);


        return response()->json($serie, 200);
    }


    // return all Stream of a genre
    public function showStreaming(Category $genre)
    {
        $stream = Livetv::whereHas('genres', function ($query) use ($genre) {
            $query->where('category_id', '=', $genre->id)->where('active', '=', 1)->orderByDesc('created_at');
        })->paginate(4);

        return response()->json($stream, 200);
    }



}
