<?php

require __DIR__ . '/../vendor/autoload.php';

use ProyectoTAU\TAU\Module\Administration\User\Application\UserService;

$s = 'ProyectoTAU\\TAU\Module\\Administration\\User\\Application\\UserService';
$m = 'readAll';
call_user_func_array(array($s, $m), array(null));


//UserService::readAll();
