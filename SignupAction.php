<?php

require 'config.php';

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

    if(strtotime($birthdate) === false){
        $_SESSION['flash'] = 'Data de nascimento invalida!';
        header('Location: '.$base.'/Signup.php');
        exit;
    }

    if($auth->emailExists($email)){
        
    }else {
        $_SESSION['flash'] = 'E-mail já cadastrado';
        header('Location: '.$base.'/Signup.php');
        exit;
    }
}
$_SESSION['flash'] = 'Campos nâo enviados!';
header('Location: '.$base.'/Signup.php');
exit;