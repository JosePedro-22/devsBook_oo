<?php

require_once 'models/User.php';
require_once 'dao/UserRelationDAOPgsql.php';

class UserDaoPgsql implements UserDAO {
    private $pdo;

    public function __construct(PDO $driver){
        $this->pdo = $driver;
    }

    private function generateUser($array, $full = false){
        $user = new User();

        $user->id = $array['id'] ?? 0;
        $user->email = $array['email'] ?? '';
        $user->password = $array['password'] ?? '';
        $user->name = $array['name'] ?? '';
        $user->city = $array['city'] ?? '';
        $user->work = $array['work'] ?? '';
        $user->avatar = $array['avatar'] ?? '';
        $user->cover = $array['cover'] ?? '';
        $user->token = $array['token'] ?? '';
        $user->birthdate = $array['birthdate'] ?? ''; 

        if($full){
            $urDaoPgsql = new UserRelationDAOPgsql($this->pdo);
            
            $user->followers = $urDaoPgsql->getFollowers($user->id);
            $user->following = $urDaoPgsql->getFollowing($user->id);

            $user->photos = [];

        }
        return $user;
    }

    public function findByToken($token)
    {
        if(!empty($token)){
            $sql = $this->pdo->prepare('SELECT * FROM users WHERE token = :token');
            $sql->bindParam(':token',$token);
            $sql->execute();

            if($sql->rowCount() > 0) {
                $data = $sql->fetch(PDO::FETCH_ASSOC);
                $user = $this->generateUser($data);
                return $user;
            }
        }

        return false;
    }

    public function findByEmail($email){
        if(!empty($email)){
            $sql = $this->pdo->prepare('SELECT * FROM users WHERE email = :email');
            $sql->bindParam(':email',$email);
            $sql->execute();

            if($sql->rowCount() > 0) {
                $data = $sql->fetch(PDO::FETCH_ASSOC);
                $user = $this->generateUser($data);
                return $user;
            }
        }

        return false;
    }

    public function findById($id, $full = false){
        if(!empty($id)){
            $sql = $this->pdo->prepare('SELECT * FROM users WHERE id = :id');
            $sql->bindParam(':id',$id);
            $sql->execute();

            if($sql->rowCount() > 0) {
                $data = $sql->fetch(PDO::FETCH_ASSOC);
                $user = $this->generateUser($data, $full);
                return $user;
            }
        }

        return false;
    }

    public function update(User $user){

        $sql = $this->pdo->prepare('UPDATE users SET 
            email = :email,
            password = :password,
            name = :name,
            city = :city,
            work = :work,
            avatar = :avatar,
            cover = :cover,
            token = :token,
            birthdate = :birthdate
            WHERE id = :id');
        
        $sql->bindParam(':id',$user->id);
        $sql->bindParam(':email',$user->email);
        $sql->bindParam(':password',$user->password);
        $sql->bindParam(':name',$user->name);
        $sql->bindParam(':city',$user->city);
        $sql->bindParam(':work',$user->work);
        $sql->bindParam(':avatar',$user->avatar);
        $sql->bindParam(':cover',$user->cover);
        $sql->bindParam(':token',$user->token);
        $sql->bindParam(':birthdate',$user->birthdate);
        $sql->execute();

        return true;
    }

    public function insert(User $user){
        $sql = $this->pdo->prepare('INSERT INTO users (email,password,name,token,birthdate) VALUES (:email,:password,:name ,:token,:birthdate)');

        $sql->bindParam(':email',$user->email);
        $sql->bindParam(':name',$user->name);
        $sql->bindParam(':password',$user->password);
        $sql->bindParam(':birthdate',$user->birthdate);
        $sql->bindParam(':token',$user->token);
        $sql->execute();
        
        return true;
    }
}