<?php
require_once 'models/Post.php';
require_once 'dao/UserRelationDAOPgsql.php';
require_once 'dao/UserDAOPgsql.php';
require_once 'dao/PostLikeDAOPgsql.php';
require_once 'dao/PostCommentDAOPgsql.php';

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

    public function delete($id, $id_from)
    {
        $postLikeDao = new PostLikeDAOPgsql($this->pdo);
        $postCommentDao = new PostCommentDAOPgsql($this->pdo);

        $sql = $this->pdo->prepare("SELECT * FROM posts 
            WHERE id = :id AND id_user = :id_user"); 
    
        $sql->bindParam('id',$id);
        $sql->bindValue('id_user', $id_from);
        $sql->execute();

        if($sql->rowCount()){
            $post = $sql->fetch(PDO::FETCH_ASSOC);

            $postLikeDao->deleteFromPost($id);
            $postCommentDao->deleteFromPost($id);

            if($post['type'] === 'photo'){
                $img = 'media/uploads'.$post['body'];
                if(file_exists($img)){
                    unlink($img);
                }
            }

            $sql = $this->pdo->prepare("DELETE FROM posts 
            WHERE id = :id AND id_user = :id_user"); 
        
            $sql->bindParam('id',$id);
            $sql->bindValue('id_user', $id_from);
            $sql->execute();
        }
        
    }

    public function getHomeFeed($id_user)
    {
        $array = [];
        $parpage = 3;

        $page = intval(filter_input(INPUT_GET, 'p'));
        if ($page < 1){
            $page = 1;
        }

        $offset = ($page -1)*$parpage;


        $urDao = new UserRelationDaoPgsql($this->pdo);
        $userList = $urDao->getFollowing($id_user);
        $userList[] = $id_user;

        // $sql = $this->pdo->query("SELECT * FROM posts WHERE id_user  
        // IN (".implode(',',$userList).") 
        // ORDER BY created_at DESC, id DESC LIMIT $offset, $parpage");

        $sql = $this->pdo->query("
            SELECT * FROM posts 
            WHERE id_user IN (".implode(',',$userList).") 
            ORDER BY created_at DESC, id DESC
            LIMIT $parpage OFFSET $offset
        ");

        if($sql->rowCount() > 0){
            $data = $sql->fetchAll(PDO::FETCH_ASSOC);
            $array = $this->_postListToObject($data, $id_user);
        }
        return $array;
    }

    public function getUserFeed($id_user)
    {
        $array = [];

        $sql = $this->pdo->prepare('SELECT * FROM posts
        WHERE id_user = :id_user ORDER BY 
        created_at DESC');
        $sql->bindParam('id_user', $id_user);
        $sql->execute();

        if($sql->rowCount() > 0){
            $data = $sql->fetchAll(PDO::FETCH_ASSOC);
            $array = $this->_postListToObject($data, $id_user);
        }

        return $array;
    }
    public function getPhotosFrom($id_user)
    {
        $array = [];

        $sql = $this->pdo->prepare('SELECT * FROM posts 
        WHERE id_user = :id_user 
        AND type = :post_type
        ORDER BY created_at DESC');
        $sql->bindValue('id_user',$id_user);
        $sql->bindValue(':post_type', 'photo', PDO::PARAM_STR);
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
        $postLike = new PostLikeDAOPgsql($this->pdo);
        $postComments = new PostCommentDAOPgsql($this->pdo);

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

            $newPost->likeCount = $postLike->getlikePost($newPost->id);
            $newPost->liked = $postLike->isLiked($newPost->id, $id_user);

            $newPost->comments = $postComments->getComments($newPost->id);
            $posts[] = $newPost;
        }
        return $posts;
    }
}