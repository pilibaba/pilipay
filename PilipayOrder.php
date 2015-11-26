<?php

namespace pilipay;
/**
 * Class PilipayOrder
 * @package pilipay
 *
 * required:
 * @property $version      string  API version. currently v2.0.1
 * @property $merchantNO   string  merchant number in account info page after signed up in pilibaba.com
 * @property $appSecret    string  app secret key in account info page
 * @property $currencyType string  USD/EUR/GBP/AUD/CAD/JPY...
 * @property $orderNo      string  order number in your site, which identifies an order
 * @property $orderAmount  number  total order amount in currencyType
 * @property $orderTime    string  the time when the order was created, in format of 2001-12-13 14:15:16
 * @property $sendTime     string  the time when the order was sent, in format of 2001-12-13 14:15:16
 * @property $pageUrl      string  the order's checkout page
 * @property $serverUrl    string  the return URL after payment is completed successfully
 * @property $shipper      number  ship fee (it's to houseware's fee, not the international ship fee) (in currencyType)
 * @property $tax          number  sales tax (in currencyType)
 *
 * @property $signType     string  "MD5" (fixed)
 * @property $signMsg      string  = MD5(merchantNO+orderNo+orderAmount+sendTime+appSecrect) (auto calculated)
 *
 * goods -- should use addGood() to add goods to the order
 *
 */
class PilipayOrder extends PilipayModel
{
    // 提交订单的接口地址
    const SUBMIT_TARGET_URL = 'http://www.pilibaba.com/pilipay/payreq';
    // 更新运单号的接口地址
    const UPDATE_TRACK_URL = 'http://www.pilibaba.com/pilipay/updateTrackNo';

    private $_goodsList = array();

    public function __construct($properties=array()){
        $this->version = '1.0.0';
        $this->signType = 'MD5';

        parent::__construct($properties);
    }

    /**
     * @return array order data in API form
     * @throws PilipayError
     */
    public function toApiArray(){
        // sign
        if ($this->signType == 'MD5'){
            // sign using MD5
            // not: orderAmount should be in cents
            $this->signMsg = md5($this->merchantNO .$this->orderNo . intval($this->orderAmount * 100) .$this->sendTime .$this->appSecret);
        } else {
            throw new PilipayError(PilipayError::INVALID_ARGUMENT, array('name' => 'signType', 'value' => $this->signType));
        }

        // check goods list
        if (empty($this->_goodsList)){
            throw new PilipayError(PilipayError::REQUIRED_ARGUMENT_NO_EXIST, array('name' => 'goodsList', 'value' => json_encode($this->_goodsList)));
        }

        // verify
        parent::verifyFields();

        return array_map('strval', array(
            'version' => $this->version,
            'merchantNO' => $this->merchantNO,
            'currencyType' => $this->currencyType,
            'orderNo' => $this->orderNo,
            'orderAmount' => intval($this->orderAmount * 100), // API: need to be in cent
            'orderTime' => $this->orderTime,
            'sendTime' => $this->sendTime,
            'pageUrl' => $this->pageUrl,
            'serverUrl' => $this->serverUrl,
            'shipper' => intval($this->shipper * 100), // API: need to be in cent
            'tax' => intval($this->tax * 100), // API: need to be in cent
            'signType' => $this->signType,
            'signMsg' => $this->signMsg,
            'goodsList' => urlencode(json_encode($this->_goodsList))
        ));
    }

    /**
     * 提交订单
     * @return array
     * @throws PilipayError
     */
    public function submit(){
        $orderData = $this->toApiArray();

        PilipayLogger::instance()->log('info', 'Submit order begin: '.json_encode($orderData));

        // submit
        $curl = new PilipayCurl();
        $curl->post(self::SUBMIT_TARGET_URL, $orderData);
        $responseStatusCode = $curl->getResponseStatusCode();
        $nextUrl = $curl->getResponseHeader('Location');

        PilipayLogger::instance()->log('info', 'Submit order end: '. print_r(array(
                'request' => $orderData,
                'response' => array(
                    'statusCode' => $curl->getResponseStatusCode(),
                    'statusText' => $curl->getResponseStatusText(),
                    'nextUrl' => $nextUrl,
                    'content' => $curl->getResponseContent(),
                )
            ), true));

        return array(
            'success' => $responseStatusCode == '200' && !empty($nextUrl),
            'errorCode' => $responseStatusCode,
            'message' => $curl->getResponseContent(),
            'nextUrl' => $nextUrl
        );
    }

    /**
     * @param string $method
     * @return string
     */
    public function renderSubmitForm($method="POST"){
        $action = self::SUBMIT_TARGET_URL;

        $orderData = $this->toApiArray();

        PilipayLogger::instance()->log('info', "Submit order (using {$method} form): ".json_encode($orderData));

        $fields = '';
        foreach ($orderData as $name => $value) {
            $fields .= sprintf('<input type="hidden" name="%s" value="%s" />', $name, htmlspecialchars($value));
        }

        $html = <<<HTML_CODE
<form id="pilipaysubmit" name="pilipaysubmit" action="{$action}" method="{$method}" >
    {$fields}
    <input type="submit" value="submit" style="display: none;" />
</form>
<script type="text/javascript">
    document.forms['pilipaysubmit'].submit();
</script>
HTML_CODE;

        return $html;
    }

    /**
     * Update track number (logistics number)
     * @param $logisticsNo
     */
    public function updateTrackNo($logisticsNo){
        $params = array(
            'orderNo' => $this->orderNo,
            'merchantNo' => $this->merchantNO,
            'logisticsNo' => $logisticsNo,
        );

        PilipayLogger::instance()->log('info', "Update track NO: ".json_encode($params));

        $curl = new PilipayCurl();
        $curl->get(self::UPDATE_TRACK_URL, $params);

        PilipayLogger::instance()->log('info', 'Update track NO result: '. print_r(array(
                'request' => $params,
                'response' => array(
                    'statusCode' => $curl->getResponseStatusCode(),
                    'statusText' => $curl->getResponseStatusText(),
                    'content' => $curl->getResponseContent()
                )
            ), true));
    }

    /**
     * 添加商品信息
     * Add goods info
     * @param PilipayGood $good 商品信息
     */
    public function addGood(PilipayGood $good){
        $this->_goodsList[] = $good->toApiArray();
    }

    public function getNumericFieldNames(){
        return array('orderAmount', 'shipper', 'tax');
    }

    public function getRequiredFieldNames(){
        return array('version', 'merchantNO', 'appSecret', 'currencyType', 'orderNo', 'orderAmount',
                     'orderTime', 'sendTime', 'pageUrl', 'serverUrl', 'shipper', 'tax', 'signType', 'signMsg');
    }

}