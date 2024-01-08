<?php

require_once 'config.php';
require_once 'models/Auth.php';
require_once 'dao/PostLikeDAOPgsql.php';

$auth = new Auth($pdo, $base);
$userInfo = $auth->checkToken();

$id = filter_input(INPUT_POST, 'id');
$txt = filter_input(INPUT_POST, 'txt');

$array = [];

if($id && $txt){
    $postcommentDao = new PostCommentDAOPgsql($pdo);
    $newComment = new PostComment();
    $newComment->id_post = $id;
    $newComment->id_user = $userInfo->id;
    $newComment->body = $txt;
    $newComment->created_at = date('Y-m-d H:i:s');

    $postcommentDao->addComment($newComment);

    $array = [
        'error' => '',
        'link' => $base.'/Perfil.php?id='.$userInfo->id,
        'comment' => $base.'media/avatars/'.$userInfo->avatar,
        'name' => $userInfo->name,
        'body' => $txt
    ];
}

header('Content-Type: application/json');
echo json_encode($array);