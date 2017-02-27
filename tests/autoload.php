<?php

// Try a range of paths, depending on the environment used to test this.
// This is really clumsy, but is needed to avoid assuming any parituclar
// development and testing structure.
//
// Command:
//  phpunit --bootstrap {path to autoload.php} {path to package or specific src file}
//

for($i = 1; $i <= 4; $i++) {
    $path = __DIR__ . str_repeat('/..', $i) . '/vendor/autoload.php';
    if (file_exists($path)) {
        include_once $path;
        break;
    }
}

$classLoader = new \Composer\Autoload\ClassLoader();
$classLoader->addPsr4('Academe\\SerializeParser\\', __DIR__ . '/../src', true);
$classLoader->register();
