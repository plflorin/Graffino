<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
class ImportData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-data:json';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import data from URL';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $json = @file_get_contents('https://raw.githubusercontent.com/robynitp/networkedmedia/master/week5/00-json/instagram.json');
        if(!$json){
            echo 'Eroare preluare fisier';
            die;
        }

        $tables = ['Captions', 'Comments', 'Images', 'Instagram', 'Likes', 'Locations', 'Tags', 'Users', 'Users_in_photo'];
        foreach($tables as $table){
            $tmpTable = "App\\" . $table;
            $tmpTable::query()->truncate();
        }

        $json = json_decode($json,1);
        $jsonData = $json['data'];

        $usersLikes = array();
        $usersPhoto = array();
        foreach($jsonData as  $key => $value ){
            $id = $value['id'];

            $instagram = array(
                'id' => $id,
                'attribution' => $value['attribution'],
                'filter' => $value['filter'],
                'created_time' => $value['created_time'],
                'link' => $value['link'],
                'type' => $value['type'],
                'user_id' => $value['user']['id'],
            );

            $tagsData = $value['tags'];
            $tags = array();
            foreach($tagsData as $tag){
                $tags[] = ['instagram_id' => $id, 'name' => $tag];
            }

            $locationData = $value['location'];
            $locations = array(
                'instagram_id' => $id,
                'latitude' => $locationData['latitude'],
                'longitude' => $locationData['longitude'],
                'name' => (isset($locationData['name']) ? $locationData['name'] : ''),
                'id' => (isset($locationData['id']) ? $locationData['id'] : '')
            );

            $commentsData = $value['comments']['data'];
            $comments = array();
            foreach($commentsData as $commentData){
                $comments[] = [
                    'id' => $commentData['id'],
                    'instagram_id' => $id,
                    'created_time' => $commentData['created_time'],
                    'text' => $commentData['text'],
                    'user_id' => $commentData['from']['id']
                ];
                $usersComments[] = [
                    'id' => $commentData['from']['id'],
                    'instagram_id' => '',
                    'username' => $commentData['from']['username'],
                    'website' => '',
                    'profile_picture' => $commentData['from']['profile_picture'],
                    'full_name' => $commentData['from']['full_name'],
                    'bio' => '',
                ];
            }

            $likesData = $value['likes']['data'];
            $likes = array();
            foreach($likesData as $likeData){
                $likes[] = [
                    'instagram_id' => $id,
                    'user_id' => $likeData['id']
                ];

                $usersLikes[] = [
                    'id' => $likeData['id'],
                    'instagram_id' => '',
                    'username' => $likeData['username'],
                    'website' => '',
                    'profile_picture' => $likeData['profile_picture'],
                    'full_name' => $likeData['full_name'],
                    'bio' => '',
                ];
            }

            $imagesData = $value['images'];
            $images = array(
                'instagram_id' => $id,
                'low_resolution_url' => $imagesData['low_resolution']['url'],
                'low_resolution_width' => $imagesData['low_resolution']['width'],
                'low_resolution_height' => $imagesData['low_resolution']['height'],
                'thumbnail_url' => $imagesData['thumbnail']['url'],
                'thumbnail_width' => $imagesData['thumbnail']['width'],
                'thumbnail_height' => $imagesData['thumbnail']['height'],
                'standard_resolution_url' => $imagesData['standard_resolution']['url'],
                'standard_resolution_width' => $imagesData['standard_resolution']['width'],
                'standard_resolution_height' => $imagesData['standard_resolution']['height'],
            );

            $users_in_photoData = $value['users_in_photo'];
            $users_in_photo = array();
            foreach($users_in_photoData as $user_in_photoData){
                $users_in_photo[] = [
                    'instagram_id' => $id,
                    'position_x' => $user_in_photoData['position']['x'],
                    'position_y' => $user_in_photoData['position']['y'],
                    'user_id' => $user_in_photoData['user']['id']
                ];

                $usersPhoto[] = [
                    'id' => $user_in_photoData['user']['id'],
                    'instagram_id' => '',
                    'username' => $user_in_photoData['user']['username'],
                    'website' => '',
                    'profile_picture' => $user_in_photoData['user']['profile_picture'],
                    'full_name' => $user_in_photoData['user']['full_name'],
                    'bio' => '',
                ];
            }

            $captionsData = $value['caption'];
            if($captionsData !== null){
                $captions = array(
                    'instagram_id' => $id,
                    'created_time' => $captionsData['created_time'],
                    'text' => $captionsData['text'],
                    'user_id' => $captionsData['from']['id'],
                    'id' => $captionsData['id'],
                );

                $usersCaptions[] = [
                    'id' => $captionsData['from']['id'],
                    'instagram_id' => '',
                    'username' => $captionsData['from']['username'],
                    'website' => '',
                    'profile_picture' => $captionsData['from']['profile_picture'],
                    'full_name' => $captionsData['from']['full_name'],
                    'bio' => '',
                ];
            }

            $usersData = $value['user'];
            $users = array(
                'id' => $usersData['id'],
                'instagram_id' => $id,
                'username' => $usersData['username'],
                'website' => $usersData['website'],
                'profile_picture' => $usersData['profile_picture'],
                'full_name' => $usersData['full_name'],
                'bio' => $usersData['bio']
            );

            DB::table('instagram')->insert($instagram);
            DB::table('tags')->insert($tags);
            DB::table('locations')->insert($locations);
            DB::table('comments')->insert($comments);
            DB::table('likes')->insert($likes);
            DB::table('images')->insert($images);
            DB::table('users_in_photo')->insert($users_in_photo);
            DB::table('captions')->insert($captions);
            DB::table('users')->insert($users);
        }

        DB::table('users')->insertOrIgnore($usersComments);
        DB::table('users')->insertOrIgnore($usersLikes);
        DB::table('users')->insertOrIgnore($usersPhoto);
        DB::table('users')->insertOrIgnore($usersCaptions);

        echo 'All imports are done!';
    }
}
