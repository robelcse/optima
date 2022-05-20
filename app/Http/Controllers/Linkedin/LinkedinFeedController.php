<?php

namespace App\Http\Controllers\Linkedin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Socialuser;
use Illuminate\Support\Facades\Auth;
use App\Models\Schedulepost;

class LinkedinFeedController extends Controller
{
    protected $ssl = true;

    /**
     * 
     * user can connect via linkedin account
     * 
     * @return String 
     * 
     */

    public function linkedinConnect()
    {
        $linkedin_login_url = "https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id=" . env('LINKEDIN_APP_ID') . "&redirect_uri=" . env('LINKEDIN_REDIRECT_URI') . "&state=DCEeFWf45A53sdfKe23ew3&scope=" . env('LINKEDIN_SCOPES');
        return $linkedin_login_url;
    }

    /**
     * 
     * generate an unique accessToken for authenticaed linkedin user
     * 
     * @return String $access_token
     * 
     */

    public function genereateAccessToken()
    {
        try {
            $client = new Client(['base_uri' => 'https://www.linkedin.com']);
            $response = $client->request('POST', '/oauth/v2/accessToken', [
                'form_params' => [
                    "grant_type" => "authorization_code",
                    "code" => $_GET['code'],
                    "redirect_uri" => env('LINKEDIN_REDIRECT_URI'),
                    "client_id" => env('LINKEDIN_APP_ID'),
                    "client_secret" => env('LINKEDIN_APP_SECRET'),
                ],
            ]);
            $data = json_decode($response->getBody()->getContents(), true);
            $access_token = $data['access_token'];

            //echo $access_token = $data['access_token']; // store this token somewhere
            return $this->storeLinkedinProfileInfo($access_token);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * 
     * fetch linkedin user account and store into DB
     * 
     * @param String $access_token
     * 
     * @return Object of linkedin user
     * 
     */

    public function storeLinkedinProfileInfo($access_token)
    {
        try {
            $client = new Client(['base_uri' => 'https://api.linkedin.com']);
            $response = $client->request('GET', '/v2/me', [
                'headers' => [
                    "Authorization" => "Bearer " . $access_token,
                ],
            ]);
            $data = json_decode($response->getBody()->getContents(), true);

            $arr_of_linkedin_user = [];
            $arr_of_linkedin_user['media'] = 'Linkedin';
            $arr_of_linkedin_user['social_id'] = $data['id'];
            $arr_of_linkedin_user['access_token'] = $access_token;
            $arr_of_linkedin_user['name'] = $data['firstName']['localized']['en_US'] . ' ' . $data['lastName']['localized']['en_US'];
            $arr_of_linkedin_user['auth_id'] = Auth::user()->id;

            // var_dump($arr_of_linkedin_user); 
            Socialuser::updateOrCreate(
                ['social_id' => $data['id']],
                $arr_of_linkedin_user
            );

            return redirect('social/connection')->with('success', 'You are now successfully connected via Linkedin:)');
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * 
     * post on linkedin personal feed
     * 
     * @param String $access_token
     * @param String $linkedinUserId
     * 
     * @return Boolean
     * 
     */

    public function postOnLinkedinFeed($schedule_post)
    {

        $ids = [];
        foreach ($schedule_post as $post) {
            $ids[] = $post->id;

            $linkedin_accessToken = Socialuser::select('access_token')->where('social_id', $post->linkedin)->where('media', 'Linkedin')->first();
            $accessToken          = $linkedin_accessToken->access_token;
            $person_id            = $post->linkedin;
            $image_path           = $post->image ? $post->image : ' ';
            $message              = $post->body ? $post->body : ' ';
            $image_title          = ' ';
            $image_description    = ' ';
            $visibility           = "PUBLIC";


            $prepareUrl = "https://api.linkedin.com/v2/assets?action=registerUpload&oauth2_access_token=" . $accessToken;
            $prepareRequest =  [
                "registerUploadRequest" => [
                    "recipes" => [
                        "urn:li:digitalmediaRecipe:feedshare-image"
                    ],
                    "owner" => "urn:li:person:" . $person_id,
                    "serviceRelationships" => [
                        [
                            "relationshipType" => "OWNER",
                            "identifier" => "urn:li:userGeneratedContent"
                        ],
                    ],
                ],
            ];

            $prepareReponse = $this->curl($prepareUrl, json_encode($prepareRequest), "application/json");
            $uploadURL = json_decode($prepareReponse)->value->uploadMechanism->{"com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest"}->uploadUrl;
            $asset_id = json_decode($prepareReponse)->value->asset;

            $client = new Client();
            $response = $client->request('PUT', $uploadURL, [
                'headers' => ['Authorization' => 'Bearer ' . $accessToken],
                'body' => fopen($image_path, 'r'),
                'verify' => $this->ssl
            ]);

            // dump($response);
            
            $post_url = "https://api.linkedin.com/v2/ugcPosts?oauth2_access_token=" . $accessToken;
            $request = [
                "author" => "urn:li:person:" . $person_id,
                "lifecycleState" => "PUBLISHED",
                "specificContent" => [
                    "com.linkedin.ugc.ShareContent" => [
                        "shareCommentary" => [
                            "text" => $message
                        ],
                        "shareMediaCategory" => "IMAGE",
                        "media" => [[
                            "status" => "READY",
                            "description" => [
                                "text" => substr($image_description, 0, 200),
                            ],
                            "media" =>  $asset_id,

                            "title" => [
                                "text" => $image_title,
                            ],
                        ]],
                    ],

                ],
                "visibility" => [
                    "com.linkedin.ugc.MemberNetworkVisibility" => $visibility,
                ]
            ];

            $post = $this->curl($post_url, json_encode($request), "application/json");
        }

        $schedule_post = Schedulepost::whereIn('id', $ids)->update([
            'is_published' => 1
        ]);

        return;
    }


    /**
     * curl method whcih is called from postOnLinkedinFeed method which is stay in this class
     * 
     */

    public function curl($url, $parameters, $content_type, $post = true)
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->ssl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($post) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
        }

        curl_setopt($ch, CURLOPT_POST, $post);
        $headers = [];
        $headers[] = "Content-Type: {$content_type}";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);

        return $result;
    }
}