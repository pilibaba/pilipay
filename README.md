## What Is Pilipay?
Pilipay is short for Pilibaba's payment. This library provides pilipay's API (in PHP).


## API Reference
Firstly please take a quick look at the [HTTP API reference](http://api.pilibaba.com/doc/pilipay-http-api-reference.html) to get familiar with the basic bussiness logics.

As how to use in PHP, it's pretty simple:

### Submit an order
1. require the `autoload.php` in order to auto load the classes in pilipay.
2. create an order by `$order = new PilipayOrder()`.
3. fill essential fields in the order.
4. create a good by `$good = new PilipayGood()`.
5. fill essential fields in the good.
6. add good to the order by `$order->addGood($good);`.
7. if there are more goods, repeate 4, 5, and 6.
8. submit order by `echo $order->renderSubmitForm(); die;`

Sample code:
```php
// autoload
require 'path/to/pilipay/autoload.php';

// create an order
$order = new PilipayOrder();
$order->merchantNO = '1231312';  // a number for a merchant from pilibaba
$order->appSecret = 'abcdefg'; // the secret key from pilibaba
$order->currencyType = 'USD'; // indicates the unit of the following orderAmount, shipper, tax and price
$order->orderNo = '1231231231';
$order->orderAmount = '1.23';
$order->orderTime = '2015-11-12 13:14:15';
$order->sendTime = '2015-11-12 13:14:15';
$order->pageUrl = 'https://www.example-shop.com/path/to/some/product';
$order->serverUrl = 'https://www.example-shop.com/path/to/paid/callback';
$order->shipper = '1.23';
$order->tax = '1.23';

// create a good 
$good = new PilipayGood();
$good->name = 'Product Name';
$good->pictureUrl = 'https://www.example-shop.com/path/to/product/picture';
$good->price = '1.23';
$good->productUrl = 'https://www.example-shop.com/path/to/product';
$good->productId = '123123';
$good->quantity = 1;
$good->weight = 1.23;
$good->weightUnit = 'kg';

// add the good to order
$order->addGood($good);

// if there are more goods, please add...
//$good  = new PilipayGood();
//...
//$order->addGood($good);

// render submit form, which would auto submit
echo $order->renderSubmitForm();
die;
```

### Get the barcode
1. require the `autoload.php` in order to auto load the classes in pilipay.
2. create an order by `$order = new PilipayOrder();`.
3. fill essential fields into the order.
4. get the barcode's picture URL by `$barcodePicUrl = $order->getBarcodePicUrl();`.

Sample code:
```
// autoload
require 'path/to/pilipay/autoload.php';

// create an order
$order = new PilipayOrder();

// orderNo and merchantNo must be provided:
$order->orderNo = '123123';
$order->merchantNo = '123123';

// get the barcode's picture URL:
$barcodePicUrl = $order->getBarcodePicUrl();

// do whatever you want to with the barcode
```

### Update tracking number
1. require the `autoload.php` in order to auto load the classes in pilipay.
2. create an order by `$order = new PilipayOrder();`.
3. fill essential fields into the order.
4. invoke update by `$order->updateTrackNo($trackNo);`.

Sample code:
```php
// autoload
require 'path/to/pilipay/autoload.php';

// create an order
$order = new PilipayOrder();

// orderNo and merchantNo must be provided:
$order->orderNo = '123123';
$order->merchantNo = '123123';

// update
$order->updateTrackNo($trackNo); // $trackNo must be the same with the track number on the package when shipping.
```

### Deal the pay result
After the customer has paid, a request to `$order->serverUrl` would be sent. In order to properly deal this request, `PilipayPayResult` can be used. It's pretty simple. So just show the example code:

```php
// autoload
require 'path/to/pilipay/autoload.php';

// create an instance from the request
$payResult = PilipayPayResult::fromRequest();

// verify whether the request is valid:
if (!$payResult->verify($appSecret)){ // $appSecret is exactly the same with $order->appSecret
	// error handling...
	die('Invalid request');
}

// judge whether payment is successfully completed:
if (!$payResult->isSuccess()){
	// deal failure
} else {
	// deal success
}

```

### Handle errors
When setting fields of an order or a good, submiting the order, and updating track number, if an error is encountered, a `PilipayError` will be thrown.
So a `try ... catch` block should be used to deal errors.
Example code:
```php
try{
	// submit order, update track number...
} catch (PilipayError $e) {
	// deal the error
	// $e->getMessage() will be detailed reason.
}
```

### Record logs
`PilipayLogger` provides a extendable logging. `PilipayLogger::setHandler()` can be used to inject a logger handler. For example, logging to a file:
```php
PilipayLogger::instance()->setHandler(function($level, $msg){
	file_put_contents('path/to/pilipay/log/file', sprintf('%s %s: %s'.PHP_EOL, date('Y-m-d H:i:s'), $level, $msg));
});
```

## Support
1. Make an issue on github: <https://github.com/pilibaba/pilipay/issues/new>
2. Our official API site: <http://en.pilibaba.com/api>
3. Send an email: developers(AT)pilibaba.com
