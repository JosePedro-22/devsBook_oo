<?php

require 'config.php';
require 'models/Auth.php';
require_once 'dao/PostDAOPgsql.php';

$auth = new Auth($pdo, $base);
$userInfo = $auth->checkToken();
$userDao = new UserDaoPgsql($pdo);

$name = filter_input(INPUT_POST, 'name');
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$birthdate = filter_input(INPUT_POST, 'birthdate');
$city = filter_input(INPUT_POST, 'city');
$work = filter_input(INPUT_POST, 'work');
$password = filter_input(INPUT_POST, 'password');
$password_confirmed = filter_input(INPUT_POST, 'password_confirmed');


if($name && $email){
    $userInfo->name = $name;
    $userInfo->city = $city;
    $userInfo->work = $work;

    if($userInfo->email != $email){
        if($userDao->findByEmail($email) === false){
            $userInfo->email = $email;
        }else{
            $_SESSION['FLASH'] = 'Email já existe';
            header('Location: '.$base.'/Configuracoes.php');
            exit;
        }
    }

    $birthdate = explode('/',$birthdate);
    if(count($birthdate) != 3){
        $_SESSION['flash'] = 'Data de nascimento invalida!';
        header('Location: '.$base.'/Configuracoes.php');
        exit;
    }

    $birthdate = $birthdate[2].'-'.$birthdate[1].'-'.$birthdate[0];
    if(strtotime($birthdate) === false){
        $_SESSION['flash'] = 'Data de nascimento invalida!';
        header('Location: '.$base.'/Configuracoes.php');
        exit;
    }
    $userInfo->birthdate = $birthdate;

    if(!empty($password)){
        if($password === $password_confirmed){
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $userInfo->password = $hash;
        }else{
            header('Location: '.$base.'Configuracoes.php');
            exit;
        }
    }

    if(isset($_FILES['avatar']) && !empty($_FILES['avatar']['tmp_name'])){
        $newCover = $_FILES['avatar'];

        if(in_array($newCover['type'], ['image/jpeg','image/png','image/jpg'])){
            $coverHeight = 200;
            $coverWidth = 200;

            list($widthOrig, $heighOrig) = getimagesize($newCover['tmp_name']);
            $radio = $widthOrig / $heighOrig;

            $newWidth = $coverWidth;
            $newHeight = $coverHeight / $radio;

            if($newHeight < $coverHeight){
                $newHeight = $coverHeight;
                $newWidth = $newHeight * $radio;
            }

            $x = $coverHeight - $newHeight;
            $y = $coverWidth - $newWidth;
            $x = $x<0 ? $x/2 : $x;
            $y = $y<0 ? $y/2 : $y;

            $finalImage = imagecreatetruecolor($coverWidth, $coverHeight);

            switch($newCover['type']){
                case 'image/jpg':
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($newCover['tmp_name']);
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($newCover['tmp_name']);
                    break;
            }

            imagecopyresampled(
                $finalImage, $image,
                $x, $y, 0,0,
                $newWidth, $newHeight, $widthOrig, $heighOrig
            );

            $coverName = md5(time(). rand(0,9999) . 'jpg');
            
            imagejpeg($finalImage, './media/avatars/'. $coverName, 100);

            $userInfo->avatar = $coverName;
        }
    }

    if(isset($_FILES['cover']) && !empty($_FILES['cover']['tmp_name'])){
        $newCover = $_FILES['cover'];

        if(in_array($newCover['type'], ['image/jpeg','image/png','image/jpg'])){
            $coverHeight = 313;
            $coverWidth = 850;

            list($widthOrig, $heighOrig) = getimagesize($newCover['tmp_name']);
            $radio = $widthOrig / $heighOrig;

            $newWidth = $coverWidth;
            $newHeight = $coverHeight / $radio;

            if($newHeight < $coverHeight){
                $newHeight = $coverHeight;
                $newWidth = $newHeight * $radio;
            }

            $x = $coverHeight - $newHeight;
            $y = $coverWidth - $newWidth;
            $x = $x<0 ? $x/2 : $x;
            $y = $y<0 ? $y/2 : $y;

            $finalImage = imagecreatetruecolor($coverWidth, $coverHeight);

            switch($newCover['type']){
                case 'image/jpg':
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($newCover['tmp_name']);
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($newCover['tmp_name']);
                    break;
            }

            imagecopyresampled(
                $finalImage, $image,
                $x, $y, 0,0,
                $newWidth, $newHeight, $widthOrig, $heighOrig
            );

            $coverName = md5(time(). rand(0,9999) . 'jpg');
            
            imagejpeg($finalImage, './media/covers/'. $coverName, 100);

            $userInfo->cover = $coverName;
        }
    }
    $userDao->update($userInfo);
}

header('Location: '.$base.'/Configuracoes.php');
exit;