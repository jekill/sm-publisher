<?php
/**
 * @author: Eugeny Fomin <info@jeka.ru>
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Jeka\Publisher\Command\PublisherCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$lockerFile  = __DIR__ . '/../db/locker.serialize';
if (!file_exists($lockerFile)) {
    touch($lockerFile);
}
$config  = require __DIR__ . '/../config/config.php';
$command = new PublisherCommand(new \Jeka\Publisher\Locker\FilePublishLocker($lockerFile), $config);
$application->add($command);
$application->run();