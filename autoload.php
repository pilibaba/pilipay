<?php

if (defined('PHP_VERSION') && version_compare(PHP_VERSION, '5.3') >= 0){
    // use autoloader in higher versions
    function PilipaySplAutoloader($class){
        $pilipay = 'Pilipay';
        $class = ltrim($class, '\\');
        if (strncmp($class, $pilipay, strlen($pilipay)) === 0){
            $file = __DIR__ . DIRECTORY_SEPARATOR . $class . '.php';
            if (file_exists($file)){
                include($file);
            }
        }
    }

    spl_autoload_register('PilipaySplAutoloader');
} else {
    // require all Pilipay's files directly in lower version
    !class_exists('PilipayLogger', false) and require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PilipayLogger.php');
    !class_exists('PilipayModel', false) and require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PilipayModel.php');
    !class_exists('PilipayError', false) and require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PilipayError.php');
    !class_exists('PilipayCurl', false) and require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PilipayCurl.php');
    !class_exists('PilipayGood', false) and require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PilipayGood.php');
    !class_exists('PilipayOrder', false) and require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PilipayOrder.php');
    !class_exists('PilipayPayResult', false) and require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PilipayPayResult.php');
    !class_exists('PilipayConfig', false) and require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PilipayConfig.php');
    !class_exists('PilipayCurrency', false) and require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PilipayCurrency.php');
    !class_exists('PilipayWarehouseAddress', false) and require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PilipayWarehouseAddress.php');
}
