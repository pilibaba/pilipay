<?php

// require all Pilipay's files
!class_exists('PilipayLogger', false) and require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PilipayLogger.php');
!class_exists('PilipayModel', false) and require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PilipayModel.php');
!class_exists('PilipayError', false) and require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PilipayError.php');
!class_exists('PilipayCurl', false) and require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PilipayCurl.php');
!class_exists('PilipayGood', false) and require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PilipayGood.php');
!class_exists('PilipayOrder', false) and require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PilipayOrder.php');
!class_exists('PilipayPayResult', false) and require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PilipayPayResult.php');

