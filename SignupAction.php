<?php

require 'config.php';
require './models/Auth.php';

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password = filter_input(INPUT_POST,'password');
$name = filter_input(INPUT_POST,'name');
$birthdate = filter_input(INPUT_POST,'birthdate');


if($email && $password && $name && $birthdate){
    
    $auth = new Auth($pdo, $base);

    $birthdate = explode('/',$birthdate);
    if(count($birthdate) != 3){
        $_SESSION['flash'] = 'Data de nascimento invalida!';
        header('Location: '.$base.'/Signup.php');
        exit;
    }

    $birthdate = $birthdate[2].'-'.$birthdate[1].'-'.$birthdate[0];
    if(strtotime($birthdate) === false){
        $_SESSION['flash'] = 'Data de nascimento invalida!';
        header('Location: '.$base.'/Signup.php');
        exit;
    }

    if($auth->emailExists($email) === false){
        $auth->registerUser($name, $email, $password, $birthdate);
        header('Location: '.$base);
        exit;
    }else {
        $_SESSION['flash'] = 'E-mail já cadastrado';
        header('Location: '.$base.'/Signup.php');
        exit;
    }
}
$_SESSION['flash'] = 'Campos nâo enviados!';
header('Location: '.$base.'/Signup.php');
exit;