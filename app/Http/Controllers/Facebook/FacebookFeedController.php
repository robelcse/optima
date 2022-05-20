<?php

namespace App\Http\Controllers\Facebook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Socialuser;
use App\Models\Facebook;
use Facebook\Facebook as Facebooksdk;
use Illuminate\Support\Facades\Http;
use App\Models\Schedulepost;
use Carbon\Carbon;

class FacebookFeedController extends Controller
{
    protected $helper;
    protected $facebook;
    protected $oAuth2Client;

    /**
     * 
     * constructor method
     * 
     * @return Object of facebook 
     *
     */
    public function __construct()
    {
        // facebook credentials array
        $credentials = array(
            'app_id' => env('FACEBOOK_APP_ID'),
            'app_secret' => env('FACEBOOK_APP_SECRET'),
            'default_graph_version' => 'v13.0'
        );

        // create Object of Facebook SDK
        $this->facebook = new Facebooksdk($credentials);
        // helper
        $this->helper = $this->facebook->getRedirectLoginHelper();
        //oAuth2Client
        $this->oAuth2Client = $this->facebook->getOAuth2Client();
    }

    /**
     * 
     * user can connect via fb account
     * 
     * @return String of facebook login url
     * 
     */
    public function facebookConnect()
    {
        //get permission from facebook
        $permissions = [
            'pages_manage_posts',
            'pages_read_engagement',
            'pages_show_list',
            'pages_manage_metadata'
        ];

        $facebook_login_url = $this->helper->getLoginUrl(env('FACEBOOK_REDIRECT_URI'), $permissions);
        return $facebook_login_url;
    }

    /**
     * 
     * an unique access_token will be generated who will connect with fb account
     * 
     * @return an unique access_token of authenticated user of fb
     * 
     */

    public function generateAccessToken()
    {
        if (request('state')) {
            $this->helper->getPersistentDataHandler()->set('state', request('state'));
        }

        if (isset($_GET['code'])) { // get access token
            try {
                $accessToken = $this->helper->getAccessToken();
            } catch (Facebook\Exceptions\FacebookResponseException $e) { // graph error
                echo 'Graph returned an error ' . $e->getMessage;
            } catch (Facebook\Exceptions\FacebookSDKException $e) { // validation error
                echo 'Facebook SDK returned an error ' . $e->getMessage;
            }

            if (!$accessToken->isLongLived()) { // exchange short for long
                try {
                    $accessToken = $this->oAuth2Client->getLongLivedAccessToken($accessToken);
                } catch (Facebook\Exceptions\FacebookSDKException $e) {
                    echo 'Error getting long lived access token ' . $e->getMessage();
                }
            }
            $access_token = (string) $accessToken;
            //get profile inforation useing access_token
            return $this->storeFbProfileInformation($access_token);
        } //endif
    }

    /**
     * 
     * fetch fb profile information by access_token and store into DB
     * 
     * @param String $access_token
     * 
     * @return Boolean
     * 
     */

    public function storeFbProfileInformation($access_token)
    {
        $user = $this->facebook->get('/me?fields=name,id', $access_token);

        $userNode = $user->getGraphUser();
        $data = [
            'social_id'    => $userNode["id"],
            'name'         => $userNode["name"],
            'media'        => 'Facebook',
            'access_token' => $access_token,
            'auth_id'      => Auth::user()->id
        ];

        Socialuser::updateOrCreate(
            ['social_id' => $userNode["id"]],
            $data
        );
        return redirect('facebook/accounts/' . $userNode["id"] . '/details');
    }

    /**
     * 
     * get accounts details of authenticaed user
     * 
     * @param int $user_id
     * 
     * @return Object of data
     * 
     */

    public function fbAccountDetails($user_id)
    {

        $access_token = Socialuser::select('access_token')->where('social_id', $user_id)->first();
        $accounts = $this->facebook->get('me/accounts?fields=name,id,access_token,cover,fan_count,picture,url', $access_token->access_token);
        $accounts = $accounts->getDecodedBody();

        $pages = [];
        foreach ($accounts['data'] as $account) {
            $page = [
                'page_id'           => $account['id'],
                'page_name'         => $account['name'],
                'page_cover'        => isset($account['cover']) ? $account['cover']['source'] : '',
                'page_picture'      => $account['picture']['data']['url'] ? $account['picture']['data']['url'] : '',
                'fan_count'         => $account['fan_count'] ? $account['fan_count'] : 0,
                'page_access_token' => $account['access_token'],
                'user_id'           => $user_id,
                'auth_id'           => Auth::user()->id,
                'created_at'        => now(),
                'updated_at'        => now()
            ];
            $pages[] = $page;
        }

        Facebook::where('user_id', $user_id)->delete();
        Facebook::insert($pages);
        return redirect('social/connection')->with('success', 'You are now successfully connected via facebook:)');
    }

    /**
     * 
     * post on facebook pages
     * 
     * @param array of facebook pages
     * 
     * @return Boolean
     * 
     */

    
    // public function postOnfacebookPages2()
    // {

    //     $facebook_post_image = 'https://cdn.pixabay.com/photo/2018/11/29/21/51/social-media-3846597__480.png';
    //     $facebook_post_body = 'Hello Fans, How are you ?'; 
    //     $access_token = 'EAAqYKTWtLV0BAGC2EEdQFssjtMa07B87hhXNrsahSMuQUnlNLqbLU7LF4sgWZAYmq8CSM1NScW4WzIRm0zLg4bMOsJ2vT20fi1TGoUQ4st5IZCCyO9PW6ikE5y70WDr2SOgXeBcqtcrcqZAHvVfvZCohN5DWlXtzVDH9jXEnUxmeJWQ9sXdt';

    //     $feed_url = "https://graph.facebook.com/103623938942273/photos?url=" . $facebook_post_image . "&message=" . $facebook_post_body . "&access_token=" . $access_token;
    //     $response =  Http::post($feed_url);

    //     echo "<pre>";
    //     print_r($response);
    //     echo "</pre>";

    // }    

   

    public function postOnfacebookPages($schedule_post)
    {

        
        $ids = [];
        foreach ($schedule_post as $post) {
            $ids[] = $post->id;

            $facebook_post_image = $post->image;
            $facebook_post_body = $post->body;

            $arr_of_facebook_page_ids = explode(',', $post->facebook);
            for ($i = 0; $i < count($arr_of_facebook_page_ids); $i++) {
                //get facebook_page_access_token by facebook_page_id
                $p_access_token = Facebook::select('page_access_token')->where('page_id', $arr_of_facebook_page_ids[$i])->first();
                $access_token = $p_access_token->page_access_token;
                $feed_url = "https://graph.facebook.com/" . $arr_of_facebook_page_ids[$i] . "/photos?url=" . $facebook_post_image . "&message=" . $facebook_post_body . "&access_token=" . $access_token;
                $response =  Http::post($feed_url);
            }
        }

        //update is_published to 1 which post already published
        $schedule_post = Schedulepost::whereIn('id', $ids)->update([
            'is_published' => 1
        ]);
        return;
    }


    //logout from facebook
    public function facebookLogout($user_id,$user_access_token)
    {

        $feed_url = 'https://graph.facebook.com/'.$user_id.'/permissions?method=delete&access_token='.$user_access_token;
        $response =  Http::delete($feed_url);
        return "logout successfull";
    }

}
