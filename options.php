<?php

require_once __DIR__.'/vendor/joostd/tiqr-server/libTiqr/library/tiqr/Tiqr/AutoLoader.php';
//require_once __DIR__.'/vendor/SURFnet/tiqr-server-libphp/library/tiqr/Tiqr/AutoLoader.php';

$options = array(
    //"identifier"      => "pilot.stepup.coin.surf.net",
    "name"            => "Stepup Authentication Service",
    "auth.protocol"       => "tiqrauth",
    "enroll.protocol"     => "tiqrenroll",
    "ocra.suite"          => "OCRA-1:HOTP-SHA1-6:QH10-S",
    "logoUrl"         => "https://demo.tiqr.org/img/tiqrRGB.png",
    "infoUrl"         => "https://selfservice.dev.stepup.coin.surf.net",
    "tiqr.path"         => "../../vendor/joostd/tiqr-server/libTiqr/library/tiqr",
//    "tiqr.path"         => '../../vendor/SURFnet/tiqr-server-libphp/library/tiqr/',
    'phpqrcode.path' => '.',
    'zend.path' => '.',
    'statestorage'        => array("type" => "file"),
    'userstorage'         => array("type" => "file", "path" => "/tmp", "encryption" => array('type' => 'dummy')),
    "usersecretstorage" => array("type" => "file"),
);

// override options locally. TODO merge with config
if( file_exists(dirname(__FILE__) . "/local_options.php") ) {
    include(dirname(__FILE__) . "/local_options.php");
} else {
    error_log("no local options found");
}

$autoloader = Tiqr_AutoLoader::getInstance($options); // needs {tiqr,zend,phpqrcode}.path
$autoloader->setIncludePath();

$userStorage = Tiqr_UserStorage::getStorage($options['userstorage']['type'], $options['userstorage']);

function base() {
    $proto = "http";
    $hostname = $_SERVER['SERVER_NAME'];
    $port = $_SERVER['SERVER_PORT'];
    /** @var $baseUrl string */
    $baseUrl = "$proto://$hostname";
    if( is_numeric($port) ) $baseUrl .= ":$port";
    return $baseUrl;
}

function generate_id($length = 8) {
    $chars = "0123456789";
    $count = mb_strlen($chars);
    for ($i = 0, $result = ''; $i < $length; $i++) {
        $index = rand(0, $count - 1);
        $result .= mb_substr($chars, $index, 1);
    }
    return $result;
}