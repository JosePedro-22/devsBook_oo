<?php

require_once 'config.php';
require_once 'models/Auth.php';

$auth = new Auth($pdo, $base);
$userInfo = $auth->checkToken();
$activeMenu = 'photos';
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
                <div class="profile-cover" style="background-image: url('<?=$base?>/media/covers/<?=$user->cover?>');"></div>
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
                    <?php if(count($user->photos) > 0):?>
                        <?php foreach($user->photos as $item_photos):?>
                            <div class="user-photo-item">
                                <a href="#modal-2" rel="modal:open">
                                    <img src="<?=$base?>/media/uploads/<?=$item_photos->body?>" />
                                </a>
                                <div id="modal-2" style="display:none">
                                    <img src="<?=$base?>/media/uploads/<?=$item_photos->body?>" />
                                </div>
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
        </div>
</section>
<?php
require './partials/footer.php';