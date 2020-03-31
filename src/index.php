<?php
require_once 'vendor/autoload.php';
use ChrisHolland\Projector\ExponentialProjector;

if (isset($argv[1]) && isset($argv[2]) && isset($argv[3])) {
    $projector = ExponentialProjector::fromFile(
        $argv[1],
        (int)$argv[2],
        (int)$argv[3]
    );

    print (json_encode(
        $projector->getProjections(),
        JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT,
        512
    ));
}
