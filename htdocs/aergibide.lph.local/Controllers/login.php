<?php

use Utils\AuthUtils;

require dirname(__FILE__) . '/../config.inc.php';
session_start();
if(!AuthUtils::checkAuth())
    require dirname(__FILE__) . '/../Views/login.php';
else
    header("Location: /");