<?php
/**
 * Class PilipayPayResult
 * @property $errorCode
 * @property $errorMsg
 * @property $merchantNO
 * @property $orderNo
 * @property $orderAmount
 * @property $signType
 * @property $signMsg
 * @property $sendTime
 * @property $dealId
 * @property $fee
 * @property $customerMail
 */
class PilipayPayResult
{
    protected $merchantNO;
    protected $orderNo;
    protected $orderAmount;
    protected $signType;
    protected $payResult;
    protected $signMsg;
    protected $sendTime;
    protected $dealId;
    protected $fee;
    protected $customerMail;

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

            return false;
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

    /**
     * @return mixed
     */
    public function getSignType()
    {
        return $this->signType;
    }

    /**
     * @return mixed
     */
    public function getSignMsg()
    {
        return $this->signMsg;
    }

    /**
     * @return mixed
     */
    public function getSendTime()
    {
        return $this->sendTime;
    }

    /**
     * @return mixed
     */
    public function getDealId()
    {
        return $this->dealId;
    }

    /**
     * @return mixed
     */
    public function getFee()
    {
        return $this->fee;
    }

    /**
     * @return mixed
     */
    public function getCustomerMail()
    {
        return $this->customerMail;
    }
}
