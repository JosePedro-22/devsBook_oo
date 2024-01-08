<?php

require 'config.php';
require 'models/Auth.php';
require_once 'dao/PostDAOPgsql.php';

$auth = new Auth($pdo, $base);
$userInfo = $auth->checkToken();

$id = filter_input(INPUT_GET, 'id');


if($id){
    $postDao = new PostDAOPgsql($pdo);
    $postDao->delete($id, $userInfo->id);
}
header('Location: '.$base);
exit;