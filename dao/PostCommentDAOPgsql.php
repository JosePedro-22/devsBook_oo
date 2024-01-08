<?php 

require_once './models/User.php';
require_once './models/PostComments.php';

class PostCommentDAOPgsql implements PostCommentDAO{
    private $pdo;

    public function __construct(PDO $driver) {
        $this->pdo = $driver;
    }

    public function getComments($id_post){
        $array = [];

        $sql = $this->pdo->prepare("SELECT * FROM postcomments WHERE
        id_post = :id_post");
        $sql->bindValue("id_post",$id_post);
        $sql->execute();

        if($sql->rowCount() > 0){
            $data = $sql->fetchAll(PDO::FETCH_ASSOC);
            $userDao = new UserDaoPgsql($this->pdo);

            foreach($data as $item){
                $commentItem = new PostComment();

                $commentItem->id = $item['id'];
                $commentItem->id_post = $item['id_post'];
                $commentItem->id_user = $item['id_user'];
                $commentItem->body = $item['body'];
                $commentItem->created_at = $item['created_at'];
                $commentItem->user = $userDao->findById($item['id_user']);

                $array[] = $commentItem;
            }
        }

        return $array;
    }
    public function addComment(PostComment $postComment){
        $sql = $this->pdo->prepare("INSERT INTO postcomments 
        (id_post, id_user, body, created_at) VALUES
        (:id_post, :id_user, :body, :created_at)");

        $sql->bindValue('id_user',$postComment->id_user);
        $sql->bindValue('id_post',$postComment->id_post);
        $sql->bindValue('body',$postComment->body);
        $sql->bindValue('created_at',$postComment->created_at);
        $sql->execute();
    }

    public function deleteFromPost($id){
        $sql = $this->pdo->prepare("DELETE FROM postlikes 
            WHERE id_post = :id_post "); 

        $sql->bindParam('id_post',$id);
        $sql->execute();
    }
}