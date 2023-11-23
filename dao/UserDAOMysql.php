<?php

require_once 'models/User.php';

class UserDaoMysql implements UserDAO {
    private $pdo;

    public function __construct(PDO $driver){
        $this->pdo = $driver;
    }

    public function findByToken($token)
    {
        if(!empty($token)){
            $sql = $this->pdo->prepare('SELECT * FROM users WHERE token = :token');
            $sql->bindParam(':token',$token);
            $sql->execute();

            if($sql->rowCount() > 0) $data = $sql->fetch(PDO::FETCH_ASSOC);
        }

        return false;
    }
}