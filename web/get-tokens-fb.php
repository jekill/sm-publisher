<?php
/**
 * @author: Eugeny Fomin <info@jeka.ru>
 */

@session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use Facebook\FacebookRequest;
use Facebook\FacebookSession;

$config   = require __DIR__ . '/../config/config.php';
$configFb = $config['fb'];

FacebookSession::setDefaultApplication($configFb['app_id'], $configFb['app_secret']);

$callbackUrl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];


$h = new \Facebook\FacebookRedirectLoginHelper($callbackUrl, $configFb['app_id'], $configFb['app_secret']);


$session = null;
try {
    $session = $h->getSessionFromRedirect();
} catch (\Exception $e) {
}

$loginUrl    = '';
$loginOutUrl = '';

if (!$session) {
    $loginUrl = $h->getLoginUrl(['public_profile', 'manage_pages', 'publish_actions']);
}


?>
<html>
<head>
    <meta http-equiv="content-type" charset="utf-8" content="text/html">
</head>
<body>
<a href="<?= $callbackUrl ?>">&laquo;</a>



<?php

if ($session) {
    $session     = $session->getLongLivedSession();
    $response    = (new FacebookRequest($session, 'GET', '/me/accounts'))->execute()->getResponse();
    $loginOutUrl = $h->getLogoutUrl($session, $callbackUrl);
    var_dump($session->getAccessToken());
    var_dump($response);
}
?>

<?php if ($loginUrl): ?>
    <a href="<?= $loginUrl ?>">Получить токен для Facebook</a>
<?php endif ?>


</body>
</html>
