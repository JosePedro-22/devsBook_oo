<?php
require_once 'dao/UserDAOMysql.php';

class Auth{
    private $pdo, $base;

    public function __construct(PDO $pdo, $base){
        $this->pdo = $pdo;
        $this->base = $base;
    }
    public function checkToken(){
        if(!empty($_SESSION['token'])){
            $token = $_SESSION['token'];

            $userDao = new UserDaoMysql($this->pdo);
            $user = $userDao->findByToken($token);

            if($user) return $user;
        }

        header('location:'.$this->base.'/login;php');
        exit;
    }
}