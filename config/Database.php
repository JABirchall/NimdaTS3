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
    public static $config = [
        /*
        |--------------------------------------------------------------------------
        | Default Database Connection Name
        |--------------------------------------------------------------------------
        |
        | Here you may specify which of the database connections below you wish
        | to use as your default connection for all database work. Of course
        | you may use many connections at once using the Database library.
        |
        */
        'default' => 'sqlite',
        /*
        |--------------------------------------------------------------------------
        | Database Connections
        |--------------------------------------------------------------------------
        |
        | Here are each of the database connections setup for your application.
        | Of course, examples of configuring each database platform that is
        | supported by Nimda is shown below to make development simple.
        |
        |
        | All database work in Nimda is done through the PHP PDO facilities
        | so make sure you have the driver for your particular database of
        | choice installed on your machine before you begin development.
        |
        */
        'connections' => [
            'sqlite' => [
                'driver'   => 'sqlite',
                'database' => __DIR__ . '/../database.sqlite',
                'prefix'   => 'nimda_',
                'charset' => 'utf-8',
                'collation' => 'utf8_unicode_ci',
            ],
            'mysql' => [
                'driver'    => 'mysql',
                'host'      => '127.0.0.1',
                'database'  => 'nimda',
                'username'  => 'root',
                'password'  => 'secret',
                'charset'   => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix'    => 'nimda_',
                'strict'    => false,
            ],
            'pgsql' => [
                'driver'   => 'pgsql',
                'host'     => '127.0.0.1',
                'database' => 'nimda',
                'username' => 'DBA',
                'password' => '',
                'charset'  => 'utf8',
                'prefix'   => 'nimda_',
                'schema'   => 'public',
            ],
            'sqlsrv' => [
                'driver'   => 'sqlsrv',
                'host'     => '127.0.0.1',
                'database' => 'nimda',
                'username' => 'DBA',
                'password' => '',
                'charset'  => 'utf8',
                'prefix'   => 'nimda_',
            ],
        ],
    ];
}