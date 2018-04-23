<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Socialite;
use App\User;
use App\SocialProfile;

class SocialAuthController extends Controller
{
    //
    public function facebook()
    {
    	return Socialite::driver('facebook')->redirect();
    }

     public function callback()
    {
    	$user = Socialite::driver('facebook')->user();

        $existing = User::whereHas('socialProfiles', function($query) use ($user){
            $query->where('social_id', $user->id);
        })->first();

        if($existing !== null){
             auth()->login($existing);

            return redirect('/');
        }

    	session()->flash('facebookUser', $user);//datos que se guardan temporalmente en sesión

    	return view('users.facebook', [
    		'user' => $user,
		]);
    }

    public function register(Request $request)
    {
        $data = session('facebookUser');

        $username = $request->input('username');

        $user = User::create([
            'name' => $data->name,
            'email' => $data->email,
            'avatar' => $data->avatar,
            'username' => $username,
            'password' => str_random(16),
        ]);

        $profile = SocialProfile::create([
            'social_id' => $data->id,
            'user_id' => $user->id,
        ]);

        auth()->login($user);//le decimos a laravel que haga login con el usuario recien creado

        return redirect('/');
    }
}
