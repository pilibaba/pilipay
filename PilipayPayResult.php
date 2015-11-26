<?php


namespace pilipay;

/**
 * Class PilipayPayResult
 * @package pilipay
 * @property $errorCode
 * @property $errorMsg
 * @property $merchantNO
 * @property $orderNo
 * @property $orderAmount
 */
class PilipayPayResult
{
    private $merchantNO;
    private $orderNo;
    private $orderAmount;
    private $signType;
    private $payResult;
    private $signMsg;
    private $sendTime;
    private $dealId;
    private $fee;
    private $customerMail;

    /**
     * @param array $request
     * @return PilipayPayResult
     */
    public static function fromRequest($request = null)
    {
        return new PilipayPayResult($request ? $request : $_REQUEST);
    }


    private function __construct($request)
    {
        if (!empty($request)) {
            foreach ($request as $field => $value) {
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
        $calcedSignMsg = md5($this->merchantNO . $this->orderNo . intval($this->orderAmount) . $this->sendTime . $appSecret);

        if ($calcedSignMsg != $this->signMsg){
            PilipayLogger::instance()->log("error", "Invalid signMsg: " . $this->signMsg . " with secret: " . $appSecret . " with data: " . json_encode(get_object_vars($this)));

            if ($throws) {
                throw new PilipayError(PilipayError::INVALID_SIGN, $this->signMsg);
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->payResult == 10; // 10:pay success 11:pay fail
    }

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
     * @return mixed
     */
    public function getErrorCode()
    {
        return $this->payResult;
    }

    /**
     * @return mixed
     */
    public function getErrorMsg()
    {
        return $this->errorCode;
    }

    /**
     * @return mixed
     */
    public function getMerchantNO()
    {
        return $this->merchantNO;
    }

    /**
     * @return mixed
     */
    public function getOrderNo()
    {
        return $this->orderNo;
    }

    /**
     * @return mixed
     */
    public function getOrderAmount()
    {
        return $this->orderAmount;
    }
}