<?php

class Post {
    public $id;
    public $id_user;
    public $type;
    public $created_at;
    public $body;
    public $mine;
    public $user;
    public $likeCount;
    public $liked;
    public $comments;
}

interface PostDAO{
    public function insert(Post $p);
    public function delete($id, $id_from);
    public function getHomeFeed($id_user, $page);
    public function getUserFeed($id_user, $page);
    public function getPhotosFrom($id_user);
}