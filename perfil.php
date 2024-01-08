<?php

require_once 'config.php';
require_once 'models/Auth.php';
require_once 'dao/PostDAOPgsql.php';
require_once 'dao/UserRelationDAOPgsql.php';

$auth = new Auth($pdo, $base);
$userInfo = $auth->checkToken();
$activeMenu = 'profile';
$id = filter_input(INPUT_GET, 'id');

$postDao = new PostDAOPgsql($pdo);
$userDao = new UserDaoPgsql($pdo);
$UserRelationsDao = new UserRelationDAOPgsql($pdo);

$user = $userDao->findById($id, true);
if(!$user){
    header("Location: ".$base);
    exit;
}

$seguindo = count($user->following);
$seguidores = count($user->followers);
$fotos = count($user->photos);

$date = date('d/m/Y', strtotime(str_replace('-','/',$userInfo->birthdate)));
$dataNascimento = new DateTime($date);
$dataAtual = new DateTime();
$diferenca = $dataAtual->diff($dataNascimento);
$years = $diferenca->y;

$feed = $postDao->getUserFeed($id);

if($id != $userInfo->id) $activeMenu = '';

$isFollowing = $UserRelationsDao->isFollowing($userInfo->id, $id);

require './partials/header.php';
require './partials/menuLateral.php';

?>
    <section class="feed">

        <div class="row">
            <div class="box flex-1 border-top-flat">
                <div class="box-body">
                    <div class="profile-cover" style="background-image: url('<?=$base?>/media/covers/<?=$user->cover?>');"></div>
                    <div class="profile-info m-20 row">
                        <div class="profile-info-avatar">
                            <img src="<?=$base?>/media/avatars/<?=$user->avatar?>" />
                        </div>
                        <div class="profile-info-name">
                            <div class="profile-info-name-text"><?=$user->name?></div>
                            <?php if(!empty($user->city)):?>
                                <div class="profile-info-location"><?=$user->city?></div>
                            <?php endif?>
                        </div>
                        <div class="profile-info-data row">
                            <?php if($id != $userInfo->id):?>
                            <div class="profile-info-item m-width-20">
                                <a href="<?=$base;?>/FollowAction.php?id=<?=$id;?>" class="button"><?=(!$isFollowing ?'Seguir' : 'Deixar de seguir')?></a>
                            </div>
                            <?php endif;?>
                            <div class="profile-info-item m-width-20">
                                <div class="profile-info-item-n"><?php echo $seguidores?></div>
                                <div class="profile-info-item-s">Seguidores</div>
                            </div>
                            <div class="profile-info-item m-width-20">
                                <div class="profile-info-item-n"><?php echo $seguindo?></div>
                                <div class="profile-info-item-s">Seguindo</div>
                            </div>
                            <div class="profile-info-item m-width-20">
                                <div class="profile-info-item-n"><?php echo $fotos?></div>
                                <div class="profile-info-item-s">Fotos</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="column side pr-5">

                <div class="box">
                    <div class="box-body">

                        <div class="user-info-mini">
                            <img src="<?=$base?>/assets/images/calendar.png" />
                            <?= $date?>    (<?= $years?>  anos)
                        </div>

                        <div class="user-info-mini">
                            <?php if(!empty($user->city)):?>
                                <img src="<?=$base?>/assets/images/pin.png" />
                                <?=$user->city?>, Brasil
                            <?php endif?>
                        </div>

                        <div class="user-info-mini">
                        <?php if(!empty($user->work)):?>
                                <img src="<?=$base?>/assets/images/work.png" />
                                <?=$user->work?>
                            <?php endif?>
                        </div>

                    </div>
                </div>

                <div class="box">
                    <div class="box-header m-10">
                        <div class="box-header-text">
                            Seguindo
                            <span>(<?php echo $seguindo?>)</span>
                        </div>
                        <div class="box-header-buttons">
                            <a href="<?=$base;?>/Amigos.php?id=<?=$user->id;?>">ver todos</a>
                        </div>
                    </div>
                    <div class="box-body friend-list">
                        <?php if(count($user->following) > 0 ):?>
                            <?php foreach($user->following as $item):?>
                                <div class="friend-icon">
                                    <a href="<?=$base?>/Perfil.php/?id=<?=$item->id?>">
                                        <div class="friend-icon-avatar">
                                            <img src="<?=$base?>/media/avatars/<?=$item->avatar?>" />
                                        </div>
                                        <div class="friend-icon-name">
                                            <?php echo $item->name?>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach;?>
                        <?php endif;?>
                    </div>
                </div>

            </div>
            <div class="column pl-5">
                <div class="box">
                    <div class="box-header m-10">
                        <div class="box-header-text">
                            Fotos
                            <span>(<?php echo $fotos?>)</span>
                        </div>
                        <div class="box-header-buttons">
                            <a href="<?=$base?>/fotos.php?id=<?=$userInfo->id?>">ver todos</a>
                        </div>
                    </div>
                    <div class="box-body row m-20">

                        <?php if(count($user->photos) > 0):?>
                            <?php foreach($user->photos as $key => $item_photos):?>
                                <?php if($key < 4):?>
                                <div class="user-photo-item">
                                    <a href="#modal-<?=$key;?>" rel="modal:open">
                                        <img src="<?=$base?>/media/uploads/<?=$item_photos->body?>" />
                                    </a>
                                    <div id="modal-<?=$key;?>" style="display:none">
                                        <img src="<?=$base?>/media/uploads/<?=$item_photos->body?>" />
                                    </div>
                                </div>
                                <?php endif;?>
                            <?php endforeach;?>
                        <?php endif;?>
                    </div>
                </div>

                <?php if($id == $userInfo->id):?>
                    <?php require('./partials/feedEditor.php')?>
                <?php endif;?>
                <?php if(count($feed) > 0):?>
                    <?php foreach($feed as $item):?>
                        <?php require('./partials/body.php')?>
                    <?php endforeach;?>
                <?php else:?>
                    Não há postagens deste usuário.
                <?php endif;?>
            </div>

        </div>

    </section>
<script>
    window.onload = function(){
        var modal = new VanillaModal();
    }
</script>
<?php
require './partials/footer.php';