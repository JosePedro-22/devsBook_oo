<?php

require_once 'config.php';
require_once 'models/Auth.php';
require_once 'dao/PostDAOPgsql.php';

$auth = new Auth($pdo, $base);
$userInfo = $auth->checkToken();
$activeMenu = 'friends';
$id = filter_input(INPUT_GET, 'id');
$userDao = new UserDaoPgsql($pdo);

$user = $userDao->findById($id, true);
if(!$user){
    header("Location: ".$base);
    exit;
}

$seguindo = count($user->following);
$seguidores = count($user->followers);
$fotos = count($user->photos);

if($id != $userInfo->id) $activeMenu = '';

require './partials/header.php';
require './partials/menuLateral.php';

?>
<section class="feed">
    <div class="row">
        <div class="box flex-1 border-top-flat">
            <div class="box-body">
                <div class="profile-cover" style="background-image: url('<?=$base?>/media/covers/cover.jpg');"></div>
                <div class="profile-info m-20 row">
                    <div class="profile-info-avatar">
                        <a href="<?=$base?>/perfil.php/?id=<?=$user->id?>">
                            <img src="<?=$base?>/media/avatars/<?=$user->avatar?>" />
                        </a>
                    </div>
                    <div class="profile-info-name">
                        <div class="profile-info-name-text"><a href=""><?=$user->name?></a></div>
                        <div class="profile-info-location">
                            <?php if(!empty($user->city)):?>
                                <?=$user->city?>
                            <?php endif?>
                        </div>
                    </div>
                    <div class="profile-info-data row">
                        <div class="profile-info-item m-width-20">
                            <div class="profile-info-item-n"><?=$seguidores?></div>
                            <div class="profile-info-item-s">Seguidores</div>
                        </div>
                        <div class="profile-info-item m-width-20">
                            <div class="profile-info-item-n"><?=$seguindo?></div>
                            <div class="profile-info-item-s">Seguindo</div>
                        </div>
                        <div class="profile-info-item m-width-20">
                            <div class="profile-info-item-n"><?=$fotos?></div>
                            <div class="profile-info-item-s">Fotos</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="column">
            <div class="box">
                <div class="box-body">
                    <div class="tabs">
                        <div class="tab-item" data-for="followers">
                            Seguidores
                        </div>
                        <div class="tab-item active" data-for="following">
                            Seguindo
                        </div>
                    </div>
                    <div class="tab-content">
                        <div class="tab-body" data-item="followers">
                            <div class="full-friend-list">
                            <?php if(count($user->followers) > 0 ):?>
                                <?php foreach($user->followers as $item):?>
                                    <div class="friend-icon ">
                                        <a href="<?=$base?>/perfil.php/?id=<?=$item->id?>">
                                            <div class="friend-icon-avatar">
                                                <img src="<?=$base?>/media/avatars/<?=$item->avatar?>" />
                                            </div>
                                            <div class="friend-icon">
                                                <?php echo $item->name?>
                                            </div>
                                        </a>
                                    </div>
                                <?php endforeach;?>
                                <?php else :?>
                                <div class="user-photo-item">
                                    <div class="profile-info-item-s m-20 row ">
                                        Não há fotos deste usuário.
                                    </div>
                                </div>
                            <?php endif;?>
                            </div>
                        </div>
                        <div class="tab-body" data-item="following">
                            <div class="full-friend-list">
                                <?php if(count($user->following) > 0 ):?>
                                    <?php foreach($user->following as $item):?>
                                        <div class="friend-icon">
                                            <a href="<?=$base?>/perfil.php/?id=<?=$item->id?>">
                                                <div class="friend-icon-avatar">
                                                    <img src="<?=$base?>/media/avatars/<?=$item->avatar?>" />
                                                </div>
                                                <div class="">
                                                    <?php echo $item->name?>
                                                </div>
                                            </a>
                                        </div>
                                    <?php endforeach;?>
                                <?php endif;?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
require './partials/footer.php';