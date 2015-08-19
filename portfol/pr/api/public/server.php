<?php

include dirname(__FILE__).'/settings.php';
include 'Routines.php';

$apiKeyManager = new Prodom_Api_Keys();
$apiKeyManager->readFromIniFile(dirname(__FILE__) . '/../keys.ini');

$server = new Prodom_Api_Server();
$server->setServicesDirectory(dirname(__FILE__).'/..');
$server->setKeysManager($apiKeyManager);

if($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Справочник по шлюзу
    $server->printHelp();
} else {
    // Обработка вызова метода из сервиса
    $server->request();
}
