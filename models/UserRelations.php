<?php

class UserRelations {
    public $id;
    public $user_from;
    public $user_to;
}

interface UserRelationsDAO{
    public function insert(UserRelations $relations);
    public function getRelationsFrom($id);
}