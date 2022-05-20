<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedulepost;
use App\Models\Facebook;
use App\Models\Socialuser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

use App\Http\Controllers\Facebook\FacebookFeedController;
use App\Http\Controllers\Instagram\InstagramFeedController;
use App\Http\Controllers\Linkedin\LinkedinFeedController;
use Timezone;
use Image;

class SchedulePostController extends Controller
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
     * show view page to create post for social meida
     * 
     * @return \Illuminate\Http\View
     * 
     */

    public function schedulePostCreate()
    {
        //facebook page list of autheticated user
        $fb_pages = Facebook::select('page_name', 'page_id', 'page_access_token')->where('auth_id', Auth::user()->id)->get();

        $socialuser  = Socialuser::select('social_id')->where(['media' => 'Linkedin', 'auth_id' => Auth::user()->id])->first();
        $linkedin_id = null;

        if ($socialuser) {
            $linkedin_id = $socialuser->social_id;
        }

        $connected_with_facebook = Socialuser::where('auth_id', Auth::user()->id)->where('media', 'Facebook')->first();
        $connected_with_linkedin = Socialuser::where('auth_id', AUth::user()->id)->where('media', 'Linkedin')->first();

        if ($connected_with_facebook || $connected_with_linkedin) {
            return view('backend.schedule-post.create', ['title' => 'Crate Schedule Post', 'fb_pages' => $fb_pages, 'linkedin_id' => $linkedin_id, 'connected_with_facebook' => $connected_with_facebook]);
        } else {
            return redirect('social/connection');
        }
    }

    /**
     * 
     * post will be save on Database
     * 
     * @param \Illuminate\Http\Request $request
     * 
     * @return Boolean
     * 
     */

    public function schedulePostSave(Request $request)
    {
        //  return $request->all();
        //validate request data
        $request->validate([
            'body'      => 'required',
            'facebook'  => 'required_without_all:instagram,linkedin',
            'instagram' => 'required_without_all:facebook,linkedin',
            'linkedin'  => 'required_without_all:facebook,instagram',
            'image'     => 'required|mimes:jpg,png|dimensions:min_width=540'
        ]);


        //image upload
        $image = getimagesize($request->image);
        //findout image width
        $width = $image[0];
        //findout image height
        $height = $image[1];

        //findout image retio
        $ratio = $width / $height;

        //image upload with custom size
        $original_image = $request->file('image');
        if (isset($original_image)) {
            if (!file_exists('uploads/posts_image')) {
                mkdir('uploads/posts_image', 0777, true);
            }
            $instagram_image = Image::make($original_image);
            //$image_path = public_path() . '\uploads\posts_image\\';
            $image_path = public_path('/uploads/posts_image/');
            $uniqid = uniqid();
            $image_extension = $uniqid . '.' . $original_image->clientExtension();

            $instagram_image->save($image_path . $image_extension);


            if ($ratio == 1) {
                $height = 1080;
                $width  = 1080;
            }
            if ($ratio > 1) {
                $height = 1080;
                $width  = 608;
            }
            if ($ratio < 1) {
                $height = 1080;
                $width  = 1350;
            }

            $instagram_image->resize($height, $width);
            $ig_image_extension = $uniqid . '_instagram.' . $original_image->clientExtension();
            $instagram_image->save($image_path . $ig_image_extension);
        }

        //  $image = url('/uploads/posts_image/' . $imagename);
        $image = url('/uploads/posts_image/' . $image_extension);


        $arr_of_ig_account_and_fb_pages = [];
        /*if not null $request->instagram then get authenticaed user all facebook pages and check whcih page is connedted with instagram account
        */
        if (!is_null($request->instagram)) {

            //get connected instagram account whcih is connected authenticated facebook pages
            $authenticated_user_fb_pages = Facebook::where('auth_id', Auth::user()->id)->get();

            //  $instagram_accounts = [];
            foreach ($authenticated_user_fb_pages as $page) {
                $page_access_token = $page->page_access_token;
                $page_id = $page->page_id;
                $feed_url = "https://graph.facebook.com/v13.0/" . $page_id . "?fields=instagram_business_account,id&access_token=" . $page_access_token;

                $response = Http::get($feed_url);
                if (isset($response["instagram_business_account"])) {

                    //instagram account id which is connect to facebook page
                    $arr_of_ig_account_id =     $response["instagram_business_account"]["id"];
                    //facebook page id where instagram account id connected
                    $arr_of_facebook_page_id =     $response["id"];
                    $arr_of_ig_account_and_fb_pages[] = [$arr_of_ig_account_id, $arr_of_facebook_page_id];
                }
            }
        }

        $UTC_TIME = Timezone::convertFromLocal($request->date_time);

        //store data into the database
        $schedulepost              = new Schedulepost();
        $schedulepost->body        = $request->body;
        $schedulepost->image       = $image;
        $schedulepost->is_schedule = $request->is_schedule ? $request->is_schedule : 0;
        $schedulepost->date_time   = $UTC_TIME;
        // $schedulepost->media       = implode(",", $arr_of_media);
        $schedulepost->facebook    = $request->facebook ? implode(',', $request->facebook) : NULL;
        $schedulepost->linkedin    = $request->linkedin ? implode(',', $request->linkedin) : NULL;
        $schedulepost->instagram    = count($arr_of_ig_account_and_fb_pages) > 0 ? json_encode($arr_of_ig_account_and_fb_pages) : NULL;
        $schedulepost->auth_id = Auth::user()->id;
        $schedulepost->save();
        return redirect('schedule/post/all')->with('success', 'Your schedule post saved successfully');
    }


    /**
     * 
     * List of schedule psot
     * 
     * @return Array of object
     * 
     */

    public function schedulePostList()
    {
        $scheduleposts = Schedulepost::orderBy('id', 'desc')->get();
        return view('backend.schedule-post.index', ['title' => 'Schedule Post Show', 'scheduleposts' => $scheduleposts]);
    }


    /**
     * 
     * delete schedule psot
     * 
     * @param int $post_id
     * 
     * @return Boolean
     * 
     */

    public function deletePost($post_id)
    {
        $schedule_post = Schedulepost::find($post_id);
        $schedule_post->delete();
        return redirect()->back()->with('success', 'Schedule Post Deleted Successfully');
    }


    /**
     * 
     * call method for publish post on social media
     * 
     * @return Boolean
     */

    public function schedulePostPublish()
    {
        //get all post which is_published is 0
        //$schedule_post = Schedulepost::where('date_time', '<', Carbon::now()->addMinutes(30)->toDateTimeString())->where('is_published', 0)->get();

        $schedule_post = Schedulepost::all();
        $arr_of_facebook_posts  = [];
        $arr_of_linkedin_posts  = [];
        $arr_of_instagram_posts = [];


        foreach ($schedule_post as $post) {
            //post insert into $arr_of_facebook_posts
            if ($post->facebook) {
                $arr_of_facebook_posts[] = $post;
            }
            //post insert into $arr_of_linkedin_posts
            if ($post->linkedin) {
                $arr_of_linkedin_posts[] = $post;
            }
            //post insert into $arr_of_instagram_posts
            if ($post->instagram) {
                $arr_of_instagram_posts[] = $post;
            }
        }

        //this method will be called when the $arr_of_facebook_posts is greater then 0
        if (count($arr_of_facebook_posts) > 0) {
            // echo "post on facebook = ".count($arr_of_facebook_posts)."<br/>"; 
            $this->facebook->postOnfacebookPages($arr_of_facebook_posts);
            //echo "called facebook post";
        }

        // //this method will be called when the $arr_of_linkedin_posts is greater then 0
        // if (count($arr_of_linkedin_posts) > 0) {
        //     // echo "post on linkedin = ".count($arr_of_linkedin_posts)."<br/>"; 
        //     $this->linkedin->postOnLinkedinFeed($arr_of_linkedin_posts);
        //     //echo "called linkedin post";
        // }
        // //this method will be called when the $arr_of_instagram_posts is greater then 0
        // if (count($arr_of_instagram_posts) > 0) {
        //     //echo "post on instagram = ".count($arr_of_instagram_posts)."<br/>"; 
        //     $this->instagram->ig_business_account($arr_of_instagram_posts);
        //     //echo "called instagram post";
        // }
    }
}
