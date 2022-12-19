<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit5a6209c308e5bc6b93b4265b73db7a14
{
    public static $prefixLengthsPsr4 = array (
        'm' => 
        array (
            'microsoft_start\\tests\\' => 22,
            'microsoft_start\\' => 16,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'microsoft_start\\tests\\' => 
        array (
            0 => __DIR__ . '/../..' . '/../microsoft-start-tests',
        ),
        'microsoft_start\\' => 
        array (
            0 => __DIR__ . '/../..' . '/',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit5a6209c308e5bc6b93b4265b73db7a14::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit5a6209c308e5bc6b93b4265b73db7a14::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit5a6209c308e5bc6b93b4265b73db7a14::$classMap;

        }, null, ClassLoader::class);
    }
}