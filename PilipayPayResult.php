<?php
/**
 * NOTICE OF LICENSE
 * Class PilipayPayResult
 * This class helps to deal the callback payment result.
 * Note: directly `new` operation is not supported. You should always use `PilipayPayResult::fromRequest()` to create an instance.
 *
 * For example:
 *
 *     // create an instance from the request
 *     $payResult = PilipayPayResult::fromRequest();
 *
 *     // verify whether the request is valid:
 *     if (!$payResult->verify($appSecret)){ // $appSecret is exactly the same with $order->appSecret
 *         // error handling...
 *         die('Invalid request');
 *     }
 *
 *     // judge whether payment is successfully completed:
 *     if (!$payResult->isSuccess()){
 *         // deal failure
 *     } else {
 *         // deal success
 *     }
 *
 *
 * @property $merchantNO    string  the merchant number.
 * @property $orderNo       string  the order number. It's been passed to pilibaba via PilipayOrder.
 * @property $orderAmount   number  the total amount of the order. Its unit is the currencyType in the submitted PilipayOrder.
 * @property $signType      string  "MD5"
 * @property $signMsg       string  it's used for verify the request. Please use `PilipayPayResult::verify()` to verify it.
 * @property $sendTime      string  the time when the order was sent. Its format is like "2011-12-13 14:15:16".
 * @property $dealId        string  the transaction ID in Pilibaba.
 * @property $fee           number  the fee for Pilibaba
 * @property $customerMail  string  the customer's email address.
 * @property $errorCode     string  used for recording errors. If you want to check whether the payment is successfully completed, please use `isSuccess()` instead
 * @property $errorMsg      string  used for recording errors. If you want to check whether the payment is successfully completed, please use `isSuccess()` instead
 */
class PilipayPayResult
{
    protected $_merchantNO;
    protected $_orderNo;
    protected $_orderAmount;
    protected $_signType;
    protected $_payResult;
    protected $_signMsg;
    protected $_sendTime;
    protected $_dealId;
    protected $_fee;
    protected $_customerMail;

    /**
     * @param array $request
     * @return PilipayPayResult
     */
    public static function fromRequest($request = null)
    {
        return new PilipayPayResult($request ? $request : $_REQUEST);
    }

    protected function __construct($request)
    {
        if (!empty($request)) {
            foreach ($request as $field => $value) {
                $field = '_' . $field;
                $this->{$field} = $value;
            }
        }
    }

    /**
     * @param $appSecret
     * @param bool $throws whether throws exception when fails
     * @return bool whether is valid request
     * @throws PilipayError
     */
    public function verify($appSecret, $throws = false)
    {
        $calcedSignMsg = md5($this->_merchantNO . $this->_orderNo . intval($this->_orderAmount) . $this->_sendTime . $appSecret);

        if ($calcedSignMsg != $this->_signMsg){
            PilipayLogger::instance()->log("error", "Invalid signMsg: " . $this->_signMsg . " with secret: " . $appSecret . " with data: " . json_encode(get_object_vars($this)));

            if ($throws) {
                throw new PilipayError(PilipayError::INVALID_SIGN, $this->_signMsg);
            }

            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->_payResult == 10; // 10:pay success 11:pay fail
    }

    /**
     * @param $name
     * @return mixed
     * @throws PilipayError
     */
    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->{$getter}();
        } else {
            throw new PilipayError(PilipayError::PROPERTY_NOT_EXIST, array($name));
        }
    }

    // setter using the default

    /**
     * @param $result
     * @param $message
     * @param $redirectUrl
     * @param $andDie bool
     * @return null
     */
    public function returnDealResultToPilibaba($result, $message, $redirectUrl, $andDie=true){
        echo "<result>$result</result><redirecturl>$redirectUrl</redirecturl><message>$message</message>";

        if ($andDie){
            die;
        }

        return null;
    }

    /**
     * @return mixed
     * @property $errorCode     string  used for recording errors. If you want to check whether the payment is successfully completed, please use `isSuccess()` instead
     */
    public function getErrorCode()
    {
        return $this->_payResult;
    }

    /**
     * @return mixed
     * @property $errorMsg      string  used for recording errors. If you want to check whether the payment is successfully completed, please use `isSuccess()` instead
     */
    public function getErrorMsg()
    {
        return $this->_errorCode;
    }

    /**
     * @return mixed
     * @property $merchantNO    string  the merchant number.
     */
    public function getMerchantNO()
    {
        return $this->_merchantNO;
    }

    /**
     * @return mixed
     * @property $orderNo       string  the order number. It's been passed to pilibaba via PilipayOrder.
     */
    public function getOrderNo()
    {
        return $this->_orderNo;
    }

    /**
     * @return mixed
     * @property $orderAmount   number  the total amount of the order. Its unit is the currencyType in the submitted PilipayOrder.
     */
    public function getOrderAmount()
    {
        return $this->_orderAmount / 100; // divide it by 100 -- as it's in cents over the HTTP API.
    }

    /**
     * @return mixed
     * @property $signType      string  "MD5"
     */
    public function getSignType()
    {
        return $this->_signType;
    }

    /**
     * @return mixed
     * @property $signMsg       string  it's used for verify the request. Please use `PilipayPayResult::verify()` to verify it.
     */
    public function getSignMsg()
    {
        return $this->_signMsg;
    }

    /**
     * @return mixed
     * @property $sendTime      string  the time when the order was sent. Its format is like "2011-12-13 14:15:16".
     */
    public function getSendTime()
    {
        return $this->_sendTime;
    }

    /**
     * @return mixed
     * @property $dealId        string  the transaction ID in Pilibaba.
     */
    public function getDealId()
    {
        return $this->_dealId;
    }

    /**
     * @return mixed
     * @property $fee           number  the fee for Pilibaba
     */
    public function getFee()
    {
        return $this->_fee;
    }

    /**
     * @return mixed
     * @property $customerMail  string  the customer's email address.
     */
    public function getCustomerMail()
    {
        return $this->_customerMail;
    }
}
