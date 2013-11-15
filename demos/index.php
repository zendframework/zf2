<?php

require_once __DIR__ . '/../vendor/autoload.php';

$nb_occur=1000;

$time_start = microtime(true);

$eventManager  = new \Zend\EventManager\EventManager(array('myid'));
$sharedManager = new \Zend\EventManager\SharedEventManager();

$eventManager->setSharedManager($sharedManager);
//$eventManager->attach('event', function() { echo 'hello'; }, 5);
//$eventManager->attach('event', function() { echo 'world'; }, 50);
//$sharedManager->attach('myid', 'event', function() { echo 'world'; });
//$eventManager->attach('event', function() { echo 'hello'; }, 3);

$eventManager->trigger('event');


for ($i = 0 ; $i != 50 ; ++$i) {
    $eventManager->attach('event', function() {}, $i);
    $sharedManager->attach('myid', 'event', function() {}, $i);
}

//$eventManager->attach('event', function() {}, 2);

//$eventManager->attach('event', function(){}, 1);
for ($i=0 ; $i<$nb_occur; $i++)
{
    $eventManager->trigger('event');
}

$time_end = microtime(true);
$time = $time_end - $time_start;

echo 'Durée : '.$time.' secondes<br/>';
echo 'Memoire' . memory_get_peak_usage();
