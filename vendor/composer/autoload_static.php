<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit644b1b6f7d2f133b04ab6b6be5780110
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/App',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit644b1b6f7d2f133b04ab6b6be5780110::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit644b1b6f7d2f133b04ab6b6be5780110::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit644b1b6f7d2f133b04ab6b6be5780110::$classMap;

        }, null, ClassLoader::class);
    }
}
