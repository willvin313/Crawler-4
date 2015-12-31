<?php

use LastCall\Crawler\Configuration\Configuration;
use LastCall\Crawler\Uri\Normalizations;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

include_once __DIR__ . '/../vendor/autoload.php';


// Create a new configuration, using our website as a base URL.
$config = new Configuration('https://lastcallmedia.com');

// Add some normalizers to clean up URLs.
$config['normalizers'] = [
    Normalizations::lowercaseHostname(),
    Normalizations::dropFragment()
];

// Add a logger.  Normally, we'd use something like Monolog.
// In this case, we'll just log directly to the console.
$config['logger'] = function() use ($config) {
    return new ConsoleLogger($config['output']);
};

// Add an event subscriber.
//$config->extend('subscribers', function($subscribers) {
//    $subscribers['mysubscriber'] = new MySubscriber();
//    return $subscribers;
//});

// Return the Configuration so the CLI runner can run it.
return $config;
