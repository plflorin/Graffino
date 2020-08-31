<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App;
use DB;
use Response;

class ExportController extends Controller
{
    public function index(){

//        $instagram = App\Instagram::with('tags')
//            ->with('locations')
//            ->with('comments')
//            ->with('likes')
//            ->with('images')
//            ->with('users_in_photo')
//            ->with('captions')
//            ->with('users')
//            ->get();

        $instagram = App\Instagram::all()->toArray();
        $data = array();
        foreach($instagram as $record){
            $id = $record['id'];

            $tagsAll = DB::table('tags')->where('instagram_id', '=', $id)->get()->toArray();
            $tags = [];
            foreach($tagsAll as $tag){
                $tags[] = $tag->name;
            }
            $record['tags'] = $tags;

            $locationAll = DB::table('locations')->where('instagram_id', '=', $id)->get()->toArray();
            $location = new \stdClass();
            if(count($locationAll) == 1){
                $location->latitude = floatval($locationAll[0]->latitude);
                $location->longitude = floatval($locationAll[0]->longitude);
                if($locationAll[0]->name !== ''){
                    $location->name = $locationAll[0]->name;
                }
                if($locationAll[0]->id !== '' ){
                    $location->id = $locationAll[0]->id;
                }

            }
            $record['location'] = $location;

            $commentsAll = DB::table('comments')->where('instagram_id', '=', $id)->get()->toArray();
            $count = count($commentsAll);
            $countData = array();
            foreach($commentsAll as $comment){
                $user = DB::table('users')->where('id', '=', $comment->user_id)->get()->toArray();
//                echo $comment->user_id;
//                var_dump($user);die;
                $tmp = new \stdClass();
                $tmp->created_time = $comment->created_time;
                $tmp->text = $comment->text;
                $tmp->id = $comment->id;
                $from = new \stdClass();
                $from->username = $user[0]->username;
                $from->profile_picture = $user[0]->profile_picture;
                $from->id = $user[0]->id;
                $from->full_name = $user[0]->full_name;
                $tmp->from = $from;
                $countData[] = $tmp;
            }
            $record['comments'] = ['count'=>$count, 'data' => $countData];

            $likesAll = DB::table('likes')->where('instagram_id', '=', $id)->get()->toArray();
            $count = count($likesAll);
            $countData = array();
            foreach($likesAll as $like){
                $user = DB::table('users')->where('id', '=', $like->user_id)->get()->toArray();
                $tmp= new \stdClass();
                $tmp->username = $user[0]->username;
                $tmp->profile_picture = $user[0]->profile_picture;
                $tmp->id = $user[0]->id;
                $tmp->full_name = $user[0]->full_name;
                $countData[] = $tmp;
            }
            $record['likes'] = ['count'=>$count, 'data' => $countData];

            $imagesAll = DB::table('images')->where('instagram_id', '=', $id)->get()->toArray();
            $low_resolution = new \stdClass();
            $low_resolution->url = $imagesAll[0]->low_resolution_url;
            $low_resolution->width = $imagesAll[0]->low_resolution_width;
            $low_resolution->height = $imagesAll[0]->low_resolution_height;
            $thumbnail = new \stdClass();
            $thumbnail->url = $imagesAll[0]->thumbnail_url;
            $thumbnail->width = $imagesAll[0]->thumbnail_width;
            $thumbnail->height = $imagesAll[0]->thumbnail_height;
            $standard_resolution = new \stdClass();
            $standard_resolution->url = $imagesAll[0]->standard_resolution_url;
            $standard_resolution->width = $imagesAll[0]->standard_resolution_width;
            $standard_resolution->height = $imagesAll[0]->standard_resolution_height;
            $record['images'] = ['low_resolution'=>$low_resolution, 'thumbnail' => $thumbnail, 'standard_resolution' => $standard_resolution];

            $users_in_photoAll = DB::table('users_in_photo')->where('instagram_id', '=', $id)->get()->toArray();
            $users_in_photo = [];
            foreach($users_in_photoAll as $user_in_photoAll){
                $tmp = new \stdClass();
                $position = new \stdClass();
                $position->x = $user_in_photoAll->position_x;
                $position->y = $user_in_photoAll->position_y;
                $user = new \stdClass();
                $userRec = DB::table('users')->where('id', '=', $user_in_photoAll->user_id)->get()->toArray();
                $user->username = $userRec[0]->username;
                $user->profile_picture = $userRec[0]->profile_picture;
                $user->id = $userRec[0]->id;
                $user->full_name = $userRec[0]->full_name;
                $tmp->position = $position;
                $tmp->user = $user;
                $users_in_photo[] = $tmp;
            }
            $record['users_in_photo'] = $users_in_photo;

            $captionAll = DB::table('captions')->where('instagram_id', '=', $id)->get()->toArray();
            $captions = new \stdClass();
            if(count($captionAll) !== 0) {
                $captions->created_time = $captionAll[0]->created_time;
                $captions->text = $captionAll[0]->text;
                $from = new \stdClass();
                $userRec = DB::table('users')->where('id', '=', $captionAll[0]->user_id)->get()->toArray();
                $from->username = $userRec[0]->username;
                $from->profile_picture = $userRec[0]->profile_picture;
                $from->id = $userRec[0]->id;
                $from->full_name = $userRec[0]->full_name;
                $captions->from = $from;
            }
            $record['caption'] = $captions;

            $userAll = DB::table('users')->where('instagram_id', '=', $id)->get()->toArray();
            $user = new \stdClass();
            $user->username = $userAll[0]->username;
            $user->website = $userAll[0]->website;
            $user->profile_picture = $userAll[0]->profile_picture;
            $user->full_name = $userAll[0]->full_name;
            $user->bio = $userAll[0]->bio;
            $user->id = $userAll[0]->id;
            $record['user'] = $user;

            $data[] = $record;
        }

        return Response::json($data);
    }
}
