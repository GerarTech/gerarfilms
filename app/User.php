<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Cashier\Billable;
use App\Notifications\PasswordReset;
use Laravel\Passport\HasApiTokens;
use BeyondCode\Comments\Contracts\Commentator;
use ChristianKuri\LaravelFavorite\Traits\Favoriteability;
use Illuminate\Contracts\Auth\MustVerifyEmail;



class User extends Authenticatable implements Commentator ,MustVerifyEmail
{
    use Notifiable, HasApiTokens,Billable,HasFactory,Favoriteability;

    protected $appends = ['favoritesMovies','favoritesSeries','favoritesAnimes','favoritesStreaming'];


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','avatar', 'premuim','manual_premuim','pack_name','pack_id','start_at','expired_in','role','email_verified_at'
        ,'type', 'provider_name', 'provider_id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    protected $casts = [
        'premuim' => 'int',
    
    ];


    protected $dates = [
        'email_verified_at' => 'datetime', 'trial_ends_at', 'subscription_ends_at',
    ];



    public function findFacebookUserForPassport($token) {
        // Your logic here using Socialite to push user data from Facebook generated token.
    }

    public function getFavoritesMoviesAttribute()
    {

        $movies = $this->favorite(Movie::class);

        $newEpisodes = [];
        foreach ($movies as $item) {

            array_push($newEpisodes, $item->makeHidden(['videos',
            'casterslist','casters','downloads','networks','networkslist','substitles']));
        }

        return $newEpisodes;
    }


    public function getFavoritesSeriesAttribute()
    {


        $movies = $this->favorite(Serie::class);

        $newEpisodes = [];
        foreach ($movies as $item) {

              array_push($newEpisodes, $item->makeHidden(['videos','casterslist','casters','downloads','networks','networkslist','substitles']));
        }

        return $newEpisodes;
    }



    public function getFavoritesAnimesAttribute()
    {

      
        $movies = $this->favorite(Anime::class);

        $newEpisodes = [];
        foreach ($movies as $item) {
            array_push($newEpisodes, $item->makeHidden(['seasons','videos','casterslist','casters','downloads','networks','networkslist','substitles']));
        }
        return $newEpisodes;
    }


    public function getFavoritesStreamingAttribute()
    {

        $livetv = $this->favorite(Livetv::class);

        $newEpisodes = [];
        foreach ($livetv as $item) {
            array_push($newEpisodes, $item->makeHidden(['videos']));
        }
        return $newEpisodes;
    }


    public function sendPasswordResetNotification($token)
{
    $this->notify(new PasswordReset($token));
}



    public function needsCommentApproval($model): bool
    {
        return false;    
    }



}
