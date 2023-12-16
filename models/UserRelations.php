<?php

class UserRelations {
    public $id;
    public $user_from;
    public $user_to;
}

interface UserRelationsDAO{
    public function insert(UserRelations $relations);
    public function getFollowing($id);
    public function getFollowers($id);
}