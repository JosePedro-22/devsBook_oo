<?php

require_once 'config.php';
require_once 'models/Auth.php';
require_once 'dao/UserDAOPgsql.php';

$auth = new Auth($pdo, $base);
$userInfo = $auth->checkToken();
$activeMenu = 'config';

$userDao = new UserDAOPgsql($pdo);
// $feed = $userDao->getHomeFeed($userInfo->id);

require './partials/header.php';
require './partials/menuLateral.php';

?>
<section class="feed mt-10">
    <h1>Configurações</h1>
    <?php if(!empty($_SESSION['flash'])):?>
        <?= $_SESSION['flash'];?>
        <?= $_SESSION['flash'] = '';?>
    <?php endif;?>
    <form class="config-form" method="POST" enctype="multipart/form-data" action="ConfiguracoesAction.php">
        <label>
            Novo Avatar:</br>
            <input type="file" name="avatar"></br>
            <img class="mini" src="<?=$base?>/media/avatars/<?=$userInfo->avatar?>">
        </label>

        <label>
            Novo Cover:</br>
            <input type="file" name="cover"></br>
            <img class="mini" src="<?=$base?>/media/covers/<?=$userInfo->cover?>">
        </label>

        </hr>
        <label>
            Nome completo:</br>
            <input type="text" name="name" value="<?=$userInfo->name?>">
        </label>
        <label>
            Novo Avatar:</br>
            <input type="email" name="email" value="<?=$userInfo->email?>">
        </label>
        <label>
            Data de Nascimento:</br>
            <input type="text" name="birthdate" id="birthdate" value="<?=date('d/m/Y', strtotime($userInfo->birthdate));?>">
        </label>

        <label>
            Cidade:</br>
            <input type="text" name="city" value="<?=$userInfo->city?>">
        </label>

        <label>
            Trabalho:</br>
            <input type="text" name="work" value="<?=$userInfo->work?>">
        </label>

        <hr>

        <label>
            Nova Senha:</br>
            <input type="password" name="password">
        </label>

        <label>
            Confirmar Nova Senha:</br>
            <input type="password" name="password_confirmed">
        </label>

        <button class="button">Salvar</button>
    </form>
</section>
<script src='https://unpkg.com/imask'></script>
<script>
    IMask(
        document.getElementById('birthdate'),
        {mask:'00/00/0000'}
    );
</script>
<?php
require './partials/footer.php';