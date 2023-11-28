<?php

require 'config.php';
require 'models/Auth.php';

$auth = new Auth($pdo, $base);
$userInfo = $auth->checkToken();

require './partials/header.php';
require './partials/menuLateral.php';
?>

<?php
require './partials/body.php'; 
require './partials/footer.php';