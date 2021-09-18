<?php
$baseDir = dirname(dirname(__FILE__));
return [
    'plugins' => [
        'Api0' => $baseDir . '/plugins/Api0/',
        'Api1' => $baseDir . '/plugins/Api1/',
        'Bake' => $baseDir . '/vendor/cakephp/bake/',
        'DebugKit' => $baseDir . '/vendor/cakephp/debug_kit/',
        'Migrations' => $baseDir . '/vendor/cakephp/migrations/',
        'Site' => $baseDir . '/plugins/Site/',
        'WyriHaximus/TwigView' => $baseDir . '/vendor/wyrihaximus/twig-view/'
    ]
];