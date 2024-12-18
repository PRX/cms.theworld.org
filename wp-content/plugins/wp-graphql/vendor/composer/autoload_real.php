<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit44de4e5f0aa4b88b3af8f8f2bf13a294
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        require __DIR__ . '/platform_check.php';

        spl_autoload_register(array('ComposerAutoloaderInit44de4e5f0aa4b88b3af8f8f2bf13a294', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit44de4e5f0aa4b88b3af8f8f2bf13a294', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit44de4e5f0aa4b88b3af8f8f2bf13a294::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
