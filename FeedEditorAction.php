<?php

require 'config.php';
require 'models/Auth.php';
require_once 'dao/PostDAOPgsql.php';

$auth = new Auth($pdo, $base);
$userInfo = $auth->checkToken();

$body = filter_input(INPUT_POST, 'body');


if($body){
    $postDao = new PostDAOPgsql($pdo);
    
    $newPost = new Post();
    $newPost->id_user = $userInfo->id;
    $newPost->type = 'text';
    $newPost->created_at = date('Y-m-d H:i:s');
    $newPost->body = $body;
    
    $postDao->insert($newPost);
}
header('Location: '.$base);
exit;