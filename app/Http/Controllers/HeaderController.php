<?php

namespace App\Http\Controllers;

use App\Http\Requests\HeaderRequest;
use App\Http\Requests\UserAgentRequest;
use App\Header;
use App\UserAgent;

class HeaderController extends Controller
{



    const STATUS = "status";
    const MESSAGE = "message";
    const VIEWS = "views";



    // create a new server in the database
    public function store(HeaderRequest $request)
    {
        $server = new Header();
        $server->fill($request->all());
        $server->save();

        $data = [
            'status' => 200,
            'message' => 'successfully created',
            'body' => $server
        ];

        return response()->json($data, $data['status']);
    }



    public function useragentsstore(UserAgentRequest $request)
    {
        $server = new UserAgent();
        $server->fill($request->all());
        $server->save();

        $data = [
            'status' => 200,
            'message' => 'successfully created',
            'body' => $server
        ];

        return response()->json($data, $data['status']);
    }



    public function userAgentweb()
    
    {

        return response()->json(UserAgent::orderByDesc('created_at')
            ->paginate(4), 200);
    }


    
    public function datausersagentoptions()
    
    {

        return response()->json(UserAgent::all(), 200);
    }




    // returns all server for admin panel
    public function dataheaders()
    {
        return response()->json(Header::all(), 200);
    }


    public function headersdata()
    {

        return response()
        ->json(Header::all(), 200);
    }

    // update a server from database
    public function update(HeaderRequest $request, Header $header)
    {
        if ($header != null) {


            $header->fill($request->all());
            $header->save();
            $data = [
                self::STATUS => 200,
                self::MESSAGE => 'successfully updated',
                'body' => $header
            ];


        } else {
            $data = [
                self::STATUS => 400,
                self::MESSAGE => 'could not be updated',
            ];
        }


        return response()->json($data, $data[self::STATUS]);
    }


    public function useragentsupdate(UserAgentRequest $request, UserAgent $useragent)
    {
        if ($useragent != null) {


            $useragent->fill($request->all());
            $useragent->save();
            $data = [
                self::STATUS => 200,
                self::MESSAGE => 'successfully updated',
                'body' => $useragent
            ];


        } else {
            $data = [
                self::STATUS => 400,
                self::MESSAGE => 'could not be updated',
            ];
        }


        return response()->json($data, $data[self::STATUS]);
    }


    // delete a server from database
    public function destroy($server)
    {
        if ($server != null) {
 
            Header::find($server)->delete();
            $data = ['status' => 200, 'message' => 'successfully deleted',];
        } else {
            $data = [
                'status' => 400,
                'message' => 'could not be deleted',
            ];
        }

        return response()->json($data, $data['status']);
    }


    public function useragentsdestroy($server)
    {
        if ($server != null) {
 
            UserAgent::find($server)->delete();
            $data = ['status' => 200, 'message' => 'successfully deleted',];
        } else {
            $data = [
                'status' => 400,
                'message' => 'could not be deleted',
            ];
        }

        return response()->json($data, $data['status']);
    }

    
}
