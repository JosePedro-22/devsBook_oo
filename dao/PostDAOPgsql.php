<?php
require_once 'models/Post.php';
require_once 'dao/UserRelationDAOPgsql.php';
require_once 'dao/UserDAOPgsql.php';

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

    public function getHomeFeed($id_user)
    {
        $array = [];

        $urDao = new UserRelationDaoPgsql($this->pdo);
        $userList = $urDao->getFollowing($id_user);
        $userList[] = $id_user;

        $sql = $this->pdo->query('SELECT * FROM posts
        WHERE id_user  IN ('.implode(',',$userList).') ORDER BY created_at DESC');

        if($sql->rowCount() > 0){
            $data = $sql->fetchAll(PDO::FETCH_ASSOC);
            $array = $this->_postListToObject($data, $id_user);
        }
        return $array;
    }

    public function getPerfilFeed($id_user)
    {
        $array = [];

        $sql = $this->pdo->prepare('SELECT * FROM posts WHERE id_user = :id_user ORDER BY created_at DESC');
        $sql->bindParam('id_user', $id_user);
        $sql->execute();

        if($sql->rowCount() > 0){
            $data = $sql->fetchAll(PDO::FETCH_ASSOC);
            $array = $this->_postListToObject($data, $id_user);
        }

        return $array;
    }
    public function _postListToObject($postList, $id_user){
        $posts = [];
        $userDao = new UserDaoPgsql($this->pdo);

        foreach($postList as $postItem){
            $newPost = new Post();
            $newPost->id = $postItem['id'];
            $newPost->type = $postItem['type'];
            $newPost->body = $postItem['body'];
            $newPost->created_at = $postItem['created_at'];
            $newPost->mine = false;

            if($postItem['id_user'] == $id_user){
                $newPost->mine = true;
            }

            $newPost->user = $userDao->findById($postItem['id_user']);

            $newPost->likeCount = 0;
            $newPost->liked = false;

            $newPost->comments = [];
            $posts[] = $newPost;
        }
        return $posts;
    }
}