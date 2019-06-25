<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Composer\Autoload\ClassLoader;

/**
 * @var ClassLoader $loader
 */
$loader = require __DIR__.'/../vendor/autoload.php';

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));
$loader->add('PHPExcel', dirname(dirname(__FILE__)) . '/src/AppBundle/Utilities/PHPExcel/Classes');

return $loader;
