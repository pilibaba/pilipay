<?php
/**
 * 提供一种能够自动加载的符合ps1规则的自动加载机制
 */
call_user_func(function(){
    $baseDir = dirname(__FILE__);
    $pilipay = 'pilipay';
    spl_autoload_register(function($class) use ($baseDir, $pilipay){
        $class = ltrim($class);
        if (strncmp($pilipay, $class, strlen($pilipay)) == 0){
            $classFile = $baseDir . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, substr($class, strlen($pilipay) + 1)). '.php';
            if (file_exists($classFile)){
                /** @noinspection PhpIncludeInspection */
                require($classFile);
            }
        }
    });
});

