<?php

class Post {
    public $id;
    public $id_user;
    public $type;
    public $created_at;
    public $body;
    //para PostDAOPgsql
    public $mine;
    public $user;
    public $likeCount;
    public $liked;
}

interface PostDAO{
    public function insert(Post $p);
    public function getHomeFeed($id_user);
}