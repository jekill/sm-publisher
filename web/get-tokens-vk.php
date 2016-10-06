<?php
/**
 * @author: Eugeny Fomin <info@jeka.ru>
 */

@session_start();

require_once __DIR__ . '/../vendor/autoload.php';

$config   = require __DIR__ . '/../config/config.php';
$configVK = $config['vk'];

$appId = $config['vk']['app_id'];

//$redirectTo = 'http://' . $_SERVER['HTTP_HOST']  . $_SERVER['SCRIPT_NAME'];
$redirectTo = 'https://oauth.vk.com/blank.html';
$auth       = \getjump\Vk\Auth::getInstance()
    ->setAppId($appId)
    ->setSecret($configVK['app_secret'])
    ->setRedirectUri($redirectTo)
    ->setScope('messages,photos,status,wall,offline');

$token = $auth->startCallback();
$vk = getjump\Vk\Core::getInstance()->apiVersion('5.5');



printf("<a href='%s' target='_top'>LINK</a>", $auth->getUrl());

var_dump($token);

if($token) {
    $vk->setToken($token);
    $vk->request('users.get', ['user_ids' => range(1, 100)])->each(function($i, $v) {
            if($v->last_name == '') return;
            print $v->last_name . '<br>';
        });
}



