<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Socialuser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Facebook\FacebookFeedController;
use App\Http\Controllers\Instagram\InstagramFeedController;
use App\Http\Controllers\Linkedin\LinkedinFeedController;

class SocialConnectController extends Controller
{

    public $facebook;
    public $linkedin;
    public $instagram;

    /**
     * 
     * constructor method
     * 
     * @return instance of facebook,linkedin & instagram class
     * 
     */

    public function __construct()
    {
        $this->facebook  = new FacebookFeedController();
        $this->linkedin  = new LinkedinFeedController();
        $this->instagram = new InstagramFeedController();
    }

    /**
     * 
     * social connection method to connect with social media
     * 
     * @param NULL
     * 
     * @return \Illuminate\Http\View
     * 
     */
    public function socialConnection()
    {
        //create and empty array
        $data = [];
        $data['title'] = 'Social Connection';

        //create instance of FacebookFeedController and acces facebookLogin method to connect via facebook
        $facebook_connection       = new FacebookFeedController();
        $data['facebook_connection_url'] = $facebook_connection->facebookConnect();

        //create instance of LinkedinFeedController and access linkedinLogin method to connect via linkedin
        $linkedin_connection       = new LinkedinFeedController();
        $data['linkedin_connection_url'] = $linkedin_connection->linkedinConnect();

        //check authenticaed user coneected or not via social media
        $data['connected_facebook_data']  = Socialuser::where('auth_id', Auth::user()->id)->where('media', 'Facebook')->first();
        $data['connected_linkedin_data']  = Socialuser::where('auth_id', Auth::user()->id)->where('media', 'Linkedin')->first();
        $data['connected_instrgram_data'] = Socialuser::where('auth_id', Auth::user()->id)->where('media', 'Instrgram')->first();

        //return view page to connect via social media 
        return view('backend.social-connect.social_connect', $data);
    }
}
