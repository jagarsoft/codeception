<?php

require __DIR__ . '/../vendor/autoload.php';

use ProyectoTAU\TAU\Module\Administration\User\Application\UserService;
use ProyectoTAU\TAU\Module\Administration\Group\Application\GroupService;
use ProyectoTAU\TAU\Module\Administration\Role\Application\RoleService;
use ProyectoTAU\TAU\Module\Administration\Module\Application\ModuleService;

function stderr($msg){
static $f = null;

    if( $f == null ){
        $f = fopen('php://stderr', 'w');
    }

    fwrite($f, $msg);
    fwrite($f, PHP_EOL);
}

function is_plural($entity){
    return strtolower(substr(trim($entity), -1)) === 's';
}

function mapEntityToClassName($entity){
    if( is_plural($entity) )
        $entity = substr($entity , 0, -1); // all except trailing s
    
    return ucfirst(strtolower($entity));
}

function mapVerbToMethod($verb, $entity){
    $methods = [
        'POST' => 'create',
        'GET' => 'read',
        'PUT' => 'update',
        'DELETE' => 'delete'
    ];
    
   if( $verb == 'GET' && is_plural($entity) )
        return 'readAll';
    
    return $methods[$verb];
}

function mapVerbToRelation($verb, $entityX, $entityY){
    $methods = [
        'POST' => 'addYToX',
        'GET' => 'getYFromX', // Ys
        'DELETE' => 'removeYFromX'
    ];
    
    $method = $methods[$verb];
    $method = str_replace("Y", ucfirst($entityY), $method);
    $method = str_replace("X", ucfirst($entityX), $method);
    
    return $method;
}

$uri = $_SERVER['REQUEST_URI'];
stderr($uri);

$path = parse_url($uri, PHP_URL_PATH);
stderr($path);

// $context = '/api/v1/admin';

$components = explode( '/', $path );
stderr(var_export($components,true));

$entityX = mapEntityToClassName($components[4]);
stderr($entityX);

$x_id = null;
if (isset($components[5])) {
    $x_id = (int) $components[5];
}

$service = $entityX . 'Service';

if (isset($components[6])) { // relations
    $entityY = strtolower($components[6]);
    $y_id = null;
    if (isset($components[7])) {
        $y_id = (int) $components[7];
    }
    $method = mapVerbToRelation($_SERVER["REQUEST_METHOD"], $entityX, $entityY);
    
    stderr($service.'::'.$method.'('.($y_id != null ? $y_id.', ' : '').$x_id.')');
    call_user_func_array(array($service, $method), array($y_id, $x_id));
} else {
    $method = mapVerbToMethod($_SERVER["REQUEST_METHOD"], $components[4]);
    
    stderr($service.'::'.$method.'('.($x_id != null ? $x_id : '').')');
    call_user_func_array(array($service, $method), array($x_id));
}
