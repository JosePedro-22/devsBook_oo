<?php
require_once 'models/Post.php';


class PostDAOPgsql implements PostDAO {
    private $pdo;

    public function __construct(PDO $driver){
        $this->pdo = $driver;
    }

    public function insert(Post $p)
    {
        $sql = $this->pdo->prepare("INSERT INTO posts 
            (id_user, type, created_at, body) 
        VALUES 
            (:id_user, :type, :created_at, :body)");

        $sql->bindParam('id_user', $p->id_user);
        $sql->bindParam('type', $p->type);
        $sql->bindParam('created_at', $p->created_at);
        $sql->bindParam('body', $p->body);
        $sql->execute();
    }
}