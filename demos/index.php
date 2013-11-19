<?php

function emptyFunc() {}

require_once __DIR__ . '/../vendor/autoload.php';

$numberOfListeners = 100;
$numberOfTriggers  = 1000;
$totalTimeStart    = microtime(true);


$eventManager  = new \Zend\EventManager\EventManager(array('myid'));

echo "Attach {$numberOfListeners} listeners:        ";
$memStart  = memory_get_usage();
$timeStart = microtime(true);
for ($i = 0; $i < $numberOfListeners; ++$i) {
    $eventManager->attach('event', 'emptyFunc', $i);
}
$timeEnd = microtime(true);
$memEnd  = memory_get_usage(true);
printf("time=%f, mem=%d<br>", $timeEnd - $timeStart, $memEnd - $memStart);


echo "Triggers {$numberOfTriggers} events:        ";
$memStart  = memory_get_usage();
$timeStart = microtime(true);
for ($i = 0; $i < $numberOfListeners; ++$i) {
    $eventManager->trigger('event');
}
$timeEnd = microtime(true);
$memEnd  = memory_get_usage(true);
printf("time=%f, mem=%d<br>", $timeEnd - $timeStart, $memEnd - $memStart);


$sharedManager = new \Zend\EventManager\SharedEventManager();
$eventManager->setSharedManager($sharedManager);

echo "Attach {$numberOfListeners} shared listeners: ";
$memStart  = memory_get_usage();
$timeStart = microtime(true);
for ($i = 0; $i < $numberOfListeners; ++$i) {
    $sharedManager->attach('myid', 'event', 'emptyFunc', $i);
}
$timeEnd = microtime(true);
$memEnd  = memory_get_usage(true);
printf("time=%f, mem=%d<br>", $timeEnd - $timeStart, $memEnd - $memStart);


echo "Triggers {$numberOfTriggers} events:        ";
$memStart  = memory_get_usage();
$timeStart = microtime(true);
for ($i = 0; $i < $numberOfListeners; ++$i) {
    $eventManager->trigger('event');
}
$timeEnd = microtime(true);
$memEnd  = memory_get_usage(true);
printf("time=%f, mem=%d<br>", $timeEnd - $timeStart, $memEnd - $memStart);


printf("<br>Total: time=%f, mem=%d\n", microtime(true) - $totalTimeStart, memory_get_peak_usage(true));
