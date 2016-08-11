<?php
/**
 * Created by PhpStorm.
 * User: Jake
 * Date: 10/08/2016
 * Time: 01:08
 */

namespace Config;

class Database
{

    public $config = [
        'driver' => 'sqlite',
        'database' => __DIR__ . '/../database.sqlite',
        'prefix' => 'nimda_',
        'charset' => 'utf-8',
        'collation' => 'utf8_unicode_ci',
    ];
}