<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Composer\Autoload\ClassLoader;

/**
 * @var ClassLoader $loader
 */
$loader = require __DIR__.'/../vendor/autoload.php';

$loader->add('Stripe', __DIR__.'/../vendor/stripe/stripe-php');

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

return $loader;
