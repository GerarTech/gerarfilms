<?php


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/cacheClear', function() {
  $optimize = Artisan::call('optimize:clear');
  return 'Routes cache cleared';
});


Route::get('/cacheClearApi', function() {
  $optimize = Artisan::call('responsecache:clear');
  return 'Routes cache cleared';
});

// Supported Hosts

Route::post('/supportedhosts/streamtape', 'UpstreamController@streamtape');
Route::post('/googledrive', 'SettingController@googledrive');


Route::get('password', 'SettingController@password');
Route::post('passwordcheck', 'SettingController@passwordcheck');



Route::post('social/loginFacebook', 'Api\Auth\LoginController@loginFacebook');
Route::post('social/loginGoogle', 'Api\Auth\LoginController@loginGoogle');



Route::get('/installs/store', 'SettingController@storeInstalls');
Route::get('/installs', 'SettingController@statistics');

Route::get('/webview', 'SettingController@webview');
Route::post('/googledrive', 'SettingController@googledrive');


//Videos
Route::get('/video/{filename}', 'VideoController@show');


//Substitles
Route::get('/substitle/{filename}', 'SubstitleController@show');


//Embeds
Route::get('/embeds/show/{embed}', 'EmbedController@show');


Route::post('/password/email', 'Api\Auth\ForgotPasswordController@sendResetLinkEmail');
Route::post('/password/reset', 'Api\Auth\ResetPasswordController@reset');



//Ads
Route::get('/ads', 'AdsController@ads');
Route::get('/casts/image/{filename}', 'CastController@getImg');
Route::get('/animes/image/{filename}', 'AnimeController@getImg');
Route::get('/series/image/{filename}', 'SerieController@getImg');
Route::get('/livetv/image/{filename}', 'LivetvController@getImg');
Route::get('/movies/videos/{movie}', 'MovieController@videos');
Route::get('/series/episode/{episode}/{code}', 'EpisodeController@videos');
Route::get('/movies/image/{filename}', 'MovieController@getImg');
Route::get('/preview/image/{filename}', 'PreviewController@getImg');
Route::get('/featured/image/{filename}', 'FeaturedController@getImg');


Route::get('/avatars/image/{filename}', 'Api\Auth\LoginController@getImg');

Route::post('social_auth', 'Api\Auth\SocialAuthController@socialAuth');


Route::middleware(['auth:api', 'throttle:60,1'])->group(function () {

  Route::get('/subscription/checkexpiration', 'UserController@checkExpirationDate');


  Route::post('/email/resend', 'Api\Auth\EmailVerificationController@resend')->name('verification.resend');

  Route::post('/logout', 'Api\Auth\LoginController@logout');
  Route::post('/update', 'Api\Auth\LoginController@update');
  Route::post('/updatePaypal', 'Api\Auth\LoginController@updatePaypal');
  Route::post('/addPlanToUser', 'Api\Auth\LoginController@addPlanToUser');
  Route::get('/cancelSubscription', 'Api\Auth\LoginController@cancelSubscription');
  Route::get('/cancelSubscriptionPaypal', 'Api\Auth\LoginController@cancelSubscriptionPaypal');
  Route::get('/profile', 'Api\Auth\LoginController@profile');

  Route::get('/user', 'Api\Auth\LoginController@user');
  Route::get('/user/logout', 'Api\Auth\LoginController@logout');
  Route::delete('/user/delete', 'Api\Auth\LoginController@deleteUser');


  Route::get('/avatar/{avatarid}', 'UserController@show');
  Route::put('/account/update', 'UserController@update');
  Route::get('/account/isSubscribed', 'UserController@isSubscribed');
  Route::post('/user/avatar', 'Api\Auth\LoginController@update_avatar');

  Route::post('/setRazorPay', 'Api\Auth\LoginController@setRazorPay');

  Route::post('/movie/addtofav/{movieid}', 'MovieController@addtofav');
  Route::get('/movie/isMovieFavorite/{movieid}', 'MovieController@isMovieFavorite');
  Route::delete('/movie/removefromfav/{movieid}', 'MovieController@removefromfav');


  Route::post('/serie/addtofav/{movieid}', 'SerieController@addtofav');
  Route::get('/serie/isMovieFavorite/{movieid}', 'SerieController@isMovieFavorite');
  Route::delete('/serie/removefromfav/{movieid}', 'SerieController@removefromfav');


    
  Route::post('/anime/addtofav/{movieid}', 'AnimeController@addtofav');
  Route::get('/anime/isMovieFavorite/{movieid}', 'AnimeController@isMovieFavorite');
  Route::delete('/anime/removefromfav/{movieid}', 'AnimeController@removefromfav');




  Route::post('/streaming/addtofav/{movieid}', 'LivetvController@addtofav');
  Route::get('/streaming/isMovieFavorite/{movieid}', 'LivetvController@isMovieFavorite');
  Route::delete('/streaming/removefromfav/{movieid}', 'LivetvController@removefromfav');

  Route::post('/users/addprofile', 'Api\Auth\LoginController@createNewProfile');




  Route::post('/media/addcomment', 'MovieController@addcomment');
  Route::post('/media/series/addcomment', 'SerieController@addcomment');
  Route::post('/media/animes/addcomment', 'AnimeController@addcomment');

  Route::delete('/media/delete/comments/{movieid}', 'MovieController@deletecomment');

  Route::post('/media/episode/addcomment', 'EpisodeController@addcomment');
  Route::post('/media/episodeAnime/addcomment', 'EpisodeController@addcommentAnime');


  
  });


Route::get('/params', 'SettingController@index');
Route::get('/image/users', 'UserController@showAvatar');
Route::get('/image/logo', 'SettingController@showLogo');
Route::get('/image/minilogo', 'SettingController@showMiniLogo');
Route::get('/image/episode', 'SettingController@showEpisode');
Route::get('/image/splash', 'SettingController@showSplash');
Route::get('/image/custombanner', 'SettingController@showcustomBanner');
Route::post('/register', 'Api\Auth\RegisterController@register');
Route::post('/login', 'Api\Auth\LoginController@login');
Route::post('/disconnect', 'Api\Auth\LoginController@logout');
Route::post('/refresh', 'Api\Auth\LoginController@refresh');
Route::get('/paypal', 'Api\Auth\LoginController@paypal');


Route::middleware('decrypter')->group(function () {


Route::get('media/homecontent/{code}', 'MovieController@homecontent');

Route::post('/report/{code}', 'ReportController@sendReport');

Route::get('/genres/allCasters/all/{code}', 'CastController@allCasters');
Route::get('/media/popularCasters/{code}', 'CastController@popularCasters');
Route::get('/cast/detail/{actor}/{code}', 'CastController@show');
Route::get('/filmographie/detail/{actor}/{code}', 'CastController@showFilmographie');


Route::get('/servers/data/{code}', 'ServerController@servers');


  //Search
Route::get('/search/{query}/{code}', 'SearchController@index');

Route::get('/settings/{code}', 'SettingController@index')->middleware('doNotCacheResponse');

Route::post('/suggest/{code}', 'SuggestionsController@sendReport');

Route::get('/mediahome/featured/{code}', 'GenreController@featured');

// Animes
Route::get('/animes/show/{anime}/{code}', 'AnimeController@show');
Route::get('/animes/watch/{serie}/{code}', 'AnimeController@showbyimdb');
Route::get('/animes/recents/{code}', 'AnimeController@recents');
Route::get('/animes/relateds/{anime}/{code}', 'AnimeController@relateds');
Route::get('/animes/season/{season}/{code}', 'AnimeSeasonController@show');
Route::get('/animes/seasons/{season}/{code}', 'AnimeSeasonController@showAnimeEpisodes');
Route::get('/series/seasons/{season}/{code}', 'EpisodeController@showAnimeEpisodes');




// Comments
Route::get('/media/detail/comments/{movie}/{code}', 'MovieController@moviecomment');

Route::get('/media/series/detail/comments/{movie}/{code}', 'SerieController@moviecomment');


Route::get('/media/animes/detail/comments/{movie}/{code}', 'AnimeController@moviecomment');


Route::get('/media/episodes/comments/{movie}/{code}', 'EpisodeController@moviecomment');
Route::get('/media/episodesanimes/comments/{movie}/{code}', 'EpisodeController@animeEpisodecomment');




// Movies
Route::get('/media/choosedcontent/{code}', 'MovieController@choosedcontent');
Route::get('/media/latestcontent/{code}', 'MovieController@latestcontent');
Route::get('/media/recommendedcontent/{code}', 'MovieController@recommendedcontent');
Route::get('/media/popularcontent/{code}', 'MovieController@popularcontent');
Route::get('/media/recentscontent/{code}', 'MovieController@recentscontent');
Route::get('/media/thisweekcontent/{code}', 'MovieController@thisweekcontent');
Route::get('/media/recommendedcontent/{code}', 'MovieController@recommendedcontent');
Route::get('/media/trendingcontent/{code}', 'MovieController@trendingcontent');
Route::get('/media/featuredcontent/{code}', 'FeaturedController@featured');
Route::get('/media/pinnedcontent/{code}', 'MovieController@pinnedcontent');
Route::get('/media/suggestedcontent/{code}', 'MovieController@suggestedcontent');
Route::get('/media/randomcontent/{code}', 'MovieController@randomcontent');
Route::get('/media/randomMovie/{code}', 'MovieController@randomMovie');
Route::get('/media/relateds/{movie}/{code}', 'MovieController@relateds');
Route::get('/media/substitles/{movie}/{code}', 'MovieController@substitles');
Route::get('/media/kids/{code}', 'MovieController@kids');
Route::get('/media/view/{movie}/{code}', 'MovieController@view');
Route::get('/media/detail/{movie}/{code}', 'MovieController@show');
Route::post('/movies/sendResume/{code}', 'MoviesResumeController@sendResume');
Route::get('/movies/resume/show/{movie}/{code}', 'MoviesResumeController@show');
Route::get('/media/topcontent/{code}', 'MovieController@topcontent');
Route::get('/media/previewscontent/{code}', 'MovieController@previewscontent');
Route::get('/media/playsomething/{code}', 'MovieController@playSomething');

Route::get('/movies/latestadded/{code}', 'GenreController@showLatestAdded');
Route::get('/movies/byyear/{code}', 'GenreController@showByYear');
Route::get('/movies/byrating/{code}', 'GenreController@showByRating');
Route::get('/movies/byviews/{code}', 'GenreController@showByViews');


// Series

Route::get('/series/show/{serie}/{code}', 'SerieController@show');
Route::get('/series/watch/{serie}/{code}', 'SerieController@showbyimdb');
Route::get('/series/recommended/{code}', 'SerieController@recommended');
Route::get('/series/popular/{code}', 'SerieController@popular');
Route::get('/series/recentscontent/{code}', 'SerieController@recents');
Route::get('/series/kids/{code}', 'SerieController@kids');
Route::get('/series/relateds/{serie}/{code}', 'SerieController@relateds');
Route::get('/series/newEpisodescontent/{code}', 'SerieController@newEpisodes');
Route::get('/animes/newEpisodescontent/{code}', 'AnimeController@newEpisodes');
Route::get('/media/seriesEpisodesAll/{code}', 'SerieController@seriesEpisodesAll');
Route::get('/media/animesEpisodesAll/{code}', 'AnimeController@animesEpisodesAll');


Route::get('/series/latestadded/{code}', 'GenreController@showLatestAddedtv');
Route::get('/series/byyear/{code}', 'GenreController@showByYeartv');
Route::get('/series/byrating/{code}', 'GenreController@showByRatingtv');
Route::get('/series/byviews/{code}', 'GenreController@showByViewstv');
Route::get('/series/latestepisodes/{code}', 'EpisodeController@latestEpisodes');



Route::get('/animes/latestadded/{code}', 'GenreController@showLatestAddedAnime');
Route::get('/animes/byyear/{code}', 'GenreController@showByYearAnime');
Route::get('/animes/byrating/{code}', 'GenreController@showByRatingAnime');
Route::get('/animes/byviews/{code}', 'GenreController@showByViewsAnime');



// Upcoming
Route::get('/upcoming/latest/{code}', 'UpcomingController@latest');
Route::get('/upcoming/show/{upcoming}/{code}', 'UpcomingController@show');

// previews
Route::get('/previews/latest/{code}', 'PreviewController@latest');


// Seasons and Episodes
Route::get('/series/season/{season}/{code}', 'SeasonController@show');
Route::get('/series/episodeshow/{episode}/{code}', 'EpisodeController@show');
Route::get('/animes/episodeshow/{episode}/{code}', 'EpisodeController@showAnime');
Route::get('/animes/episode/{episode}/{code}', 'EpisodeController@videosAnime');
Route::get('/series/substitle/{episode}/{code}', 'EpisodeController@substitles');
Route::get('/animes/substitle/{episode}/{code}', 'EpisodeController@substitlesAnimes');
Route::get('/series/view/{episode}/{code}', 'EpisodeController@view');
Route::get('/series/showEpisodeNotif/{episode}/{code}', 'SerieController@showEpisodeFromNotifcation');
Route::get('/animes/showEpisodeNotif/{episode}/{code}', 'AnimeController@showEpisodeFromNotifcation');



// Live TV
Route::get('/stream/show/{livetv}/{code}', 'LivetvController@show');
Route::get('/streaming/relateds/{movie}/{code}', 'LivetvController@relateds');
Route::get('/livetv/latest/{code}', 'LivetvController@latest');
Route::get('/livetv/featured/{code}', 'LivetvController@featured');
Route::get('/livetv/mostwatched/{code}', 'LivetvController@mostwatched');
Route::get('/livetv/show/{livetv}/{code}', 'LivetvController@show');
Route::get('/livetv/random/{livetv}/{code}', 'LivetvController@random');

//Genres
Route::get('/genres/movies/show/{genre}/{code}', 'GenreController@showMovies');
Route::get('/genres/series/show/{genre}/{code}', 'GenreController@showSeries');
Route::get('/genres/animes/show/{genre}/{code}', 'GenreController@showAnimes');
Route::get('/genres/movies/all/{code}', 'GenreController@showMoviesAllGenres');
Route::get('/genres/series/all/{code}', 'GenreController@showSeriesAllGenres');
Route::get('/genres/animes/all/{code}', 'GenreController@showAnimesAllGenres');
Route::get('/genres/list/{code}', 'GenreController@list');



Route::get('/networks/media/show/{genre}/{code}', 'NetworkController@showNetworks');
Route::get('/networks/list/{code}', 'NetworkController@list');
Route::get('/networks/lists/{code}', 'NetworkController@lists');
Route::get('/genres/network/show/{genre}/{code}', 'GenreController@networkGenre');


Route::get('/genres/series/showPlayer/{genre}/{code}', 'GenreController@showSeriesPlayer');
Route::get('/genres/movies/showPlayer/{genre}/{code}', 'GenreController@showMoviesPlayer');
Route::get('/genres/animes/showPlayer/{genre}/{code}', 'GenreController@showAnimesPlayer');


Route::get('/genres/choosed/all/{code}', 'GenreController@choosed');
Route::get('/genres/topteen/all/{code}', 'GenreController@topteen');
Route::get('/genres/recommended/all/{code}', 'GenreController@recommended');
Route::get('/genres/trending/all/{code}', 'GenreController@trending');
Route::get('/genres/new/all/{code}', 'GenreController@new');
Route::get('/genres/popularseries/all/{code}', 'GenreController@popularseries');
Route::get('/genres/latestseries/all/{code}', 'GenreController@latestseries');
Route::get('/genres/thisweek/all/{code}', 'GenreController@thisweek');
Route::get('/genres/latestanimes/all/{code}', 'GenreController@latestanimes');
Route::get('/genres/popularmovies/all/{code}', 'GenreController@popularmovies');
Route::get('/categories/streaming/show/streaming/{code}', 'CategoryController@streamingall');



// Streaming Categories
Route::get('/categories/streaming/show/{genre}/{code}', 'CategoryController@showStreaming');
Route::get('/categories/list/{code}', 'CategoryController@list');


  // Plans
  Route::get('/plans/plans/{code}', 'PlanController@plans');
  Route::post('/plans/subscribe/{code}', 'PlanController@subscribe');
  Route::get('/plans/show/{plan}/{code}', 'PlanController@show');
  Route::get('/subscriptions/all/{code}', 'PlanController@all');
  Route::get('/subscriptions/paypal/{code}', 'PlanController@paypal');



});


