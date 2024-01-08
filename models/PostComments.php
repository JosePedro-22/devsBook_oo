<?php

class PostComment {
    public $id;
    public $id_post;
    public $id_user;
    public $created_at;
    public $body;
    public $user;
}

interface PostCommentDAO {
    public function getComments($id_post);
    public function addComment(PostComment $postComment);
}