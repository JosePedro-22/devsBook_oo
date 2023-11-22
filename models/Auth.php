<?php

class Auth{
    private $pdo, $base;

    public function __construct(PDO $pdo, $base){
        $this->pdo = $pdo;
        $this->base = $base;
    }
    public function checkToken(){
        if(!empty($_SESSION['token'])){

        }

        header('location:'.$this->base.'/login;php');
        exit;
    }
}