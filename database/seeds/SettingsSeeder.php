<?php

use App\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::create([
            'app_name' => 'EASYPLEX',
            'authorization' => '',
            'tmdb_api_key' => '',
            'purchase_key' => '',
            'tmdb_lang' => [
                'english_name' => "English",
                'iso_639_1' => "en",
                'name' => "English"
            ],
            'app_url_android' => '',
            'autosubstitles' => 1,
            'livetv' => 1,
            'ads_player' => 1,
            'anime' => 1,
            'facebook_show_interstitial' => 0,
            'ad_show_interstitial' => 0,
            'ad_interstitial' => 0,
            'ad_unit_id_interstitial' => '',
            'ad_banner' => 0,
            'ad_unit_id_banner' => '',
            'ad_face_audience_interstitial' => 0,
            'ad_face_audience_banner' => 0,
            'ad_unit_id_facebook_interstitial_audience' => '',
            'ad_unit_id_facebook_banner_audience' => '',
            'privacy_policy' => '',
            'latestVersion' => '',
            'update_title' => '',
            'custom_message' => '',
            'enable_custom_message' => 0,
            'releaseNotes' => '',
            'featured_home_numbers' => '5',
            'url' => '',
            'imdb_cover_path' => 'http://image.tmdb.org/t/p/w500',
            'paypal_client_id' => '',
            'paypal_amount' => '',
            'startapp_id' => '',
            'ad_unit_id_rewarded' => '',
            'ad_unit_id__facebook_rewarded' => '',
            'unity_game_id' => '',
            'default_network' => '',
            'wach_ads_to_unlock' => 0,
            'default_media_placeholder_path' => '',
            'next_episode_timer' => 15,
            'facebook_url' => 'http://facebook.com',
            'twitter_url' => 'http://twitter.com',
            'instagram_url' => 'http://instagram.com',
            'telegram_url' => 'http://telegram.com',
            'ad_unit_id_native' => '',
            'default_payment' => 'Stripe',
            'appodeal_show_interstitial' => 0,
            'ad_unit_id_native_enable' => 0,
            'appodeal_banner' => 0,
            'appodeal_interstitial' => 0,
            'server_dialog_selection' => 0,
            'download_premuim_only' => 0,
            'custom_banner_image' => '',
            'custom_banner_image_link' => '',
            'wach_ads_to_unlock_player' => 0,
            'enable_custom_banner' => 0,
            'default_network_player' => '',
            'mantenance_mode_message' => '',
            'splash_image' => '',
            'default_youtube_quality' => '',
            'mantenance_mode' => 0,
            'allow_adm' => 0,
            'enable_pinned' => 0,
            'startapp_banner' => 0,
            'startapp_interstitial' => 0,
            'enable_vlc' => 0,
            'resume_offline' => 1,
            'unityads_banner' => 0,
            'unityads_interstitial' => 0,
            'user_agent' => 'EasyPlex',
            'unityads_banner' => 0,
            'unityads_interstitial' => 0,
            'streaming' => 1,
            'enable_banner_bottom' => 0,
            'ad_face_audience_native' => 0,
            'ad_unit_id_facebook_native_audience' => '',
            'enable_upcoming' => 0
    
        ]);
    }
}
