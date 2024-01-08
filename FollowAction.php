<?php

require_once 'config.php';
require_once 'models/Auth.php';
require_once 'dao/UserRelationDAOPgsql.php';
require_once 'dao/UserDAOPgsql.php';

$auth = new Auth($pdo, $base);
$userInfo = $auth->checkToken();

$id = filter_input(INPUT_GET, 'id');

if($id){

    $userRelationDao = new UserRelationDaoPgsql($pdo);

    $userDao = new UserDaoPgsql($pdo);

    if($userDao->findById($id)){
        $relation = new UserRelations();
        $relation->user_from = $userInfo->id;
        $relation->user_to = $id;

        if($userRelationDao->isFollowing($userInfo->id, $id)){    
            $userRelationDao->delete($relation);
        }else{
            $userRelationDao->insert($relation);
        }

        header('Location:Perfil.php?id='.$id);
        exit;
    }
}

header('Location:'.$base);
exit;