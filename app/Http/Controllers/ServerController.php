<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServerRequest;
use App\Server;
use App\Drm;

class ServerController extends Controller
{








    public function storeDrm(ServerRequest $request)
    {
        $server = new Drm();
        $server->fill($request->all());
        $server->save();

        $data = [
            'status' => 200,
            'message' => 'successfully created',
            'body' => $server
        ];

        return response()->json($data, $data['status']);
    }



    // create a new server in the database
    public function store(ServerRequest $request)
    {
        $server = new Server();
        $server->fill($request->all());
        $server->save();

        $data = [
            'status' => 200,
            'message' => 'successfully created',
            'body' => $server
        ];

        return response()->json($data, $data['status']);
    }

    // returns all server for admin panel
    public function serversdata()
    {
        return response()->json(Server::all(), 200);
    }




     // returns all server for admin panel
     public function drmdataserversweb()
     {
        
         return response()->json(Drm::orderByDesc('created_at')
         ->paginate(12), 200);
     }

     // returns all server for admin panel
     public function drmdataservers()
     {
        return response()->json(Drm::all(), 200);
     }

    public function servers()
    {

        return response()
        ->json(['servers' => Server::all()], 200);
    }



    public function drmservers()
    {

        return response()
        ->json(['drmservers' => Drm::all()], 200);
    }

    // update a server from database
    public function update(ServerRequest $request, Server $server)
    {
        $server->fill($request->all());
        $server->save();
        $data = [
            'status' => 200,
            'message' => 'successfully updated',
            'body' => $server
        ];

        return response()->json($data, $data['status']);
    }



    public function updateDrm(ServerRequest $request, Drm $server)
    {
        $server->fill($request->all());
        $server->save();
        $data = [
            'status' => 200,
            'message' => 'successfully updated',
            'body' => $server
        ];

        return response()->json($data, $data['status']);
    }

    // delete a server from database
    public function destroy(Server $server)
    {
        if ($server != null) {
            $server->delete();
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



    public function destroyDrm(Drm $server)
    {
        if ($server != null) {
            $server->delete();
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


}
