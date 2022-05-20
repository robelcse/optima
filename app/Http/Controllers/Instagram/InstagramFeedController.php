<?php

namespace App\Http\Controllers\Instagram;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedulepost;
use App\Models\Facebook;

class InstagramFeedController extends Controller
{

   /**
    * 
    * make api method which is called from postOnInstagramFeed whcih is stay in this calss 
    * @return Object
    *
    */

    function makeApiCall($endpoint, $type, $params)
    {
        $ch = curl_init();

        if ('POST' == $type) {
            curl_setopt($ch, CURLOPT_URL, $endpoint);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            curl_setopt($ch, CURLOPT_POST, 1);
        } elseif ('GET' == $type) {
            curl_setopt($ch, CURLOPT_URL, $endpoint . '?' . http_build_query($params));
        }

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    /**
     * 
     * post on instagram Feed
     * 
     * @param int $ig_account
     * @param String $facebook_page_access_token
     * @param String $post_image
     * @param String $post_body
     * 
     */

    public function postOnInstagramFeed($ig_account, $facebook_page_access_token, $post_image, $post_body)
    {

        $access_token = $facebook_page_access_token;
        // endpoint formats
        $imagesEndpointFormat = 'https://graph.facebook.com/v5.0/{ig-user-id}/media?image_url={image-url}&caption={caption}&access_token={access-token}';
        $videoEndpointFormat = 'https://graph.facebook.com/v5.0/{ig-user-id}/media?video_url={video-url}&media_type&caption={caption}&access_token={access-token}';
        $publishMediaEndpointFormat = 'https://graph.facebook.com/v5.0/{ig-user-id}/media_publish?creation_id={creation-id}&access_token={access-token}';
        $userApiLimitEndpointFormat = 'https://graph.facebook.com/v5.0/{ig-user-id}/content_publishing_limit';
        $mediaObejctStatusEndpointFormat = 'https://graph.facebook.com/v5.0/{ig-container-id}?fields=status_code';

        /***
         * IMAGE
         */
        $imageMediaObjectEndpoint = env('ENDPOINT_BASE') . $ig_account . '/media';
        $imageMediaObjectEndpointParams = array( // POST 
            'image_url' => $post_image,
            'caption' => $post_body,
            'access_token' => $access_token
        );
        $imageMediaObjectResponseArray = $this->makeApiCall($imageMediaObjectEndpoint, 'POST', $imageMediaObjectEndpointParams);

        // print_r($imageMediaObjectResponseArray);
        // exit;

        // set status to in progress
        $imageMediaObjectStatusCode = 'IN_PROGRESS';
        while ($imageMediaObjectStatusCode != 'FINISHED') { // keep checking media object until it is ready for publishing
            $imageMediaObjectStatusEndpoint = env('ENDPOINT_BASE') . $imageMediaObjectResponseArray['id'];
            $imageMediaObjectStatusEndpointParams = array( // endpoint params
                'fields' => 'status_code',
                'access_token' => $access_token
            );
            $imageMediaObjectResponseArray = $this->makeApiCall($imageMediaObjectStatusEndpoint, 'GET', $imageMediaObjectStatusEndpointParams);
            $imageMediaObjectStatusCode = $imageMediaObjectResponseArray['status_code'];
            sleep(5);
        }

        // publish image
        $imageMediaObjectId = $imageMediaObjectResponseArray['id'];
        $publishImageEndpoint = env('ENDPOINT_BASE') . $ig_account . '/media_publish';
        $publishEndpointParams = array(
            'creation_id' => $imageMediaObjectId,
            'access_token' => $access_token
        );
        $publishImageResponseArray = $this->makeApiCall($publishImageEndpoint, 'POST', $publishEndpointParams);
        /***
         * API LIMIT
         */
        // check user api limit
        $limitEndpoint = env('ENDPOINT_BASE') . $ig_account . '/content_publishing_limit';
        $limitEndpointParams = array( // get params
            'fields' => 'config,quota_usage',
            'access_token' => $access_token
        );
        $limitResponseArray = $this->makeApiCall($limitEndpoint, 'GET', $limitEndpointParams);

        return;
    }

   /**
    * 
    * get instagram business account by traversing fb_page and called postOnInstagramFeed method for post on   Instagram Feed
    *
    * @param Array of $schedule_post
    * 
    * @return Boolean
    */
    
    public function ig_business_account($schedule_post)
    {
        $ids = [];
        foreach ($schedule_post as $post) {

            $ids[] = $post->id;
            //get instagram account id and facebook page id
            $instagram_account_and_fb_page_id = json_decode($post->instagram);

            //get instagram account id
            $instagram_account = $instagram_account_and_fb_page_id[0][0];

            //get in facebook page id which is connected ig_account
            $facebook_page_id = $instagram_account_and_fb_page_id[0][1];

            //schedule post image
            $post_image = $post->image;

            //http://localhost:8000/uploads/posts_image/2022-02-26-621b0383eeac5.jpeg
            $image = explode(".",$post_image);
           
            $post_image = $image[0].'.'.$image[1]."_instagram.".$image[2];
          
            //  //schedule post body
            $post_body = $post->body;

            //get facebook page access token which is connected with instagram account 
            $fb_page_access_token = Facebook::select('page_access_token')->where('page_id', $facebook_page_id)->first();
            $facebook_page_access_token = $fb_page_access_token->page_access_token;

            $this->postOnInstagramFeed($instagram_account, $facebook_page_access_token, $post_image, $post_body);
        }

        $schedule_post = Schedulepost::whereIn('id', $ids)->update([
            'is_published' => 1
        ]);

        return;
    }

}
