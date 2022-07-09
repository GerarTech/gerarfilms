<?php

namespace App\Http\Controllers;

use App\Http\Requests\AvatarRequest;
use App\Http\Requests\PasswordUpdateRequest;
use App\Http\Requests\PasswordAppRequest;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequestStore;
use App\Http\Requests\UserUpdateRequest;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Client;
use App\Setting;
use Illuminate\Support\Carbon;

class UserController extends Controller
{

    const MESSAGE = "successfully updated";



    public function checkExpirationDate (Request $request){

        $user = Auth()->user();

       $s =  date('Y-m-d', strtotime($user->expired_in));
        
    
       $s =  date('Y-m-d', strtotime($user->expired_in));

        if(Carbon::now()->startOfDay()->gte($s)){


         $data = ['status' => 200, 'subscription' => "expired"];

        }else{

           $data = ['status' => 200, 'subscription' => "notexpired"];
        }

        return response()->json($data, 200);
     }




    public function create(Request $request)
    {

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6'
        ]);

        $user = User::create([
            'name' => request('name'),
            'email' => request('email'),
            'premuim' => false,
            'password' => bcrypt(request('password'))


        ]);

        return $this->issueToken($request, 'password');

    }





    // returns the authenticated user for admin panel

    public function data()
    {

    
        $user = Auth()->user();
        return response()
        ->json( $user, 200);

    
    }




    public function allusers()
    {

        return response()->json(User::orderByDesc('created_at')
        ->paginate(12), 200);
    }


    // return the logo checking the format
    public function showAvatar()

    {
        if (Storage::disk('public')->exists('users/users.jpg')) {
            $image = Storage::disk('public')->get('users/users.jpg');
            $mime = Storage::disk('public')->mimeType('/users/users.jpg');
            $type = 'jpg';
        } else {
            $image = Storage::disk('public')->get('users/users.png');
            $mime = Storage::disk('public')->mimeType('users/users.png');
            $type = 'png';
        }
        return (new Response($image, 200))
            ->header('Content-Type', $mime)->header('type', $type);
    }


    public function updateAvatar(AvatarRequest $request)
    {
        if ($request->hasFile('image')) {
            Storage::disk('public')->deleteDirectory('users');
            $extension = $request->image->getClientOriginalExtension();
            $filename = Storage::disk('public')->putFileAs('users', $request->image, "users.$extension");
            $data = [
                'status' => 200,
                'image_path' => $request->root() . '/api/image/users?' . time(),
            ];
        } else {
            $data = [
                'status' => 400,
            ];
        }

        return response()->json($data, $data['status']);
    }



    // update user data in the database
    public function update(UserRequest $request)
    {
        $user = Auth()->user();
        
        if ($request->get('password') == '') {
            $user->update($request->except('password'));
        } else {
            $user->update($request->all());
        }  
        $user->save();

        
        $data = [
            'status' => 200,
            self::MESSAGE,
            'body' => $user
        ];

        return response()->json($data, $data['status']);
    }


    public function isSubscribed(Request $request)
    {

     $user = Auth()->user();

     
        
      return response()->json($data = [
        'active' => $user->subscriptions()->active()->count()]);



    }



    public function store(Request $request){


        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6'
        ]);

        $user = User::create([
            'name' => request('name'),
            'email' => request('email'),
            'premuim' => false,
            'password' => bcrypt(request('password'))


        ]);

    }


    public function addUser(Request $request)
    {

        //validation for inputs
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email'
        ]);
        $return['message'] = 'Please enter valid inputs';
        if ($validator->fails()) {
            return $return;
        }

        //Add user
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->password = Hash::make(rand(10000, 99999));
        $user->save();
        return $user;
    }


    public function updateUser(UserUpdateRequest $request, User $user)


    {

        if ($user != null) {

            $user->fill($request->user);
            $user->save();

            $data = [
                'status' => 200,
                self::MESSAGE,
            ];
        } else {
            $data = [
                'status' => 400,
                'message' => 'Error',
            ];
        }

        return response()->json($data, $data['status']);
    }


    public function destroy(User $user)
    {
        if ($user != null) {
            $user->delete();

            $data = [
                'status' => 200,
                'message' => 'successfully removed',
            ];
        } else {
            $data = [
                'status' => 400,
                'message' => 'could not be deleted',
            ];
        }

        return response()->json($data, $data['status']);
    }

    // update user password in the database
    public function passwordUpdate(PasswordUpdateRequest $request)
    {
        $user = Auth()->user();
        $user->password = bcrypt($request->password);
        $user->save();
        $data = [
            'status' => 200,
            self::MESSAGE,
        ];

        return response()->json($data, $data['status']);
    }




    public function passwordUpdateApp(PasswordAppRequest $request)
    {

        $settings = Setting::first();
        $settings->password = bcrypt($request->password);
        $settings->save();
        $data = [
            'status' => 200,
            self::MESSAGE,
        ];

        return response()->json($data, $data['status']);
    }


 // update user password in the database
 public function updateUserPassword (PasswordUpdateRequest $request)
 {
     $user = Auth()->user();
     $user->password = bcrypt($request->password);
     $user->save();
     $data = [
         'status' => 200,
         self::MESSAGE,
     ];

     return response()->json($data, $data['status']);
 }




 public function show($filename)
 {

    $image = Storage::disk('avatars')->get($filename);

    $mime = Storage::disk('avatars')->mimeType($filename);

    return (new Response($image, 200))->header('Content-Type', $mime);
 }

    
}
