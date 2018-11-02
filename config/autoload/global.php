<?php

use Zend\Db;
use Zend\Cache;
use Zend\Session;

return [
    'db'              => [
        'driver'         => 'Pdo',
        'driver_options' => [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES UTF8"],
        'dsn'            => ($_SERVER['SERVER_NAME'] == 'usiz.local') ?
            'mysql:dbname=usiz;host=localhost' :
            'mysql:dbname=xtishka_usizdev;host=xtishka.mysql.ukraine.com.ua',
    ],
    'session_config'  => [
        'cookie_lifetime' => 604800,    // Срок действия cookie сессии истечет через 7 дней
        'gc_maxlifetime'  => 1209600,   // Данные сессии будут храниться на сервере до 2 недель
    ],
    'session_storage' => [
        'type' => Session\Storage\SessionArrayStorage::class,
    ],
    'service_manager' => [
        'factories' => [
            Db\Adapter\Adapter::class   => Db\Adapter\AdapterServiceFactory::class,
            Cache\StorageFactory::class => function () {
                return Cache\StorageFactory::factory([
                    'adapter' => [
                        'name'    => 'filesystem',
                        'options' => [
                            'dirLevel'           => 0,
                            'cacheDir'           => 'data/cache',
                            'dirPermission'      => 0755,
                            'filePermission'     => 0666,
                            'namespaceSeparator' => '-db-',
                            'ttl'                => 302400,
                        ],
                    ],
                    'plugins' => ['serializer'],
                ]);
            },
        ],
    ],
];