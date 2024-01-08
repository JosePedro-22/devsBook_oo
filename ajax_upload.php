<?php

require_once 'config.php';
require_once 'models/Auth.php';
require_once 'models/Post.php';
require_once 'dao/PostDAOPgsql.php';

$auth = new Auth($pdo, $base);
$postDao = new PostDAOPgsql($pdo);

$userInfo = $auth->checkToken();

$array = ['error' => ''];
$maxHeight = 800;
$maxWidth = 800;

if(isset($_FILES['photo']) && !empty($_FILES['photo']['tmp_name'])){
    $newPhotoPost = $_FILES['photo'];

    if(in_array($newPhotoPost['type'], ['image/jpeg','image/png','image/jpg'])){

        list($widthOrig, $heighOrig) = getimagesize($newPhotoPost['tmp_name']);
        $radio = $widthOrig / $heighOrig;

        $newWidth = $maxWidth;
        $newHeight = $maxHeight;
        $radioMax = $widthOrig / $heighOrig;
        
        if($radioMax < $radio) $newWidth = $newHeight * $radio;
        else $newHeight = $newWidth / $radio;

        $finalImage = imagecreatetruecolor($maxWidth, $maxHeight);

        switch($newPhotoPost['type']){
            case 'image/jpg':
            case 'image/jpeg':
                $image = imagecreatefromjpeg($newPhotoPost['tmp_name']);
                break;
            case 'image/png':
                $image = imagecreatefrompng($newPhotoPost['tmp_name']);
                break;
        }

        imagecopyresampled(
            $finalImage, $image,
            0, 0, 0,0,
            $newWidth, $newHeight, $widthOrig, $heighOrig
        );

        $photoName = md5(time(). rand(0,9999)). '.jpg';
        
        imagejpeg($finalImage, './media/uploads/'. $photoName);

      
        $newPost = new Post();
        $newPost->id_user = $userInfo->id;
        $newPost->type = 'photo';
        $newPost->created_at = date('Y-m-d H:i:s');
        $newPost->body = $photoName;

        $postDao->insert($newPost);
    }else{
        $array['error'] = 'Arquivo não suportado!'; 
    }
}
else{
    $array['error'] = 'Nenhuma imagen enviada!'; 
}

header('Content-Type: application/json');
echo json_encode($array);
exit;