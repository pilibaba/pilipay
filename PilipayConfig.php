<?php


class PilipayConfig
{
    // Whether use HTTPS
    // 是否使用HTTPS
    const USE_HTTPS = true;

    // The domain of pilibaba
    // 霹雳爸爸的域名
    const PILIBABA_DOMAIN = 'www.pilibaba.com';

    // The interface PATH for submit order
    // 提交订单的接口地址
    const SUBMIT_ORDER_PATH = '/pilipay/payreq';

    // The interface PATH for update tracking number
    // 更新运单号的接口地址
    const UPDATE_TRACK_NO_PATH = '/pilipay/updateTrackNo';

    // The interface PATH for barcode
    // 二维码的接口地址
    const BARCODE_PATH = '/pilipay/barCode';

    // The interface PATH for get warehouse address list
    // 中转仓地址列表的接口地址
    const WAREHOUSE_ADDRESS_PATH = '/pilipay/getAddressList';

    // The interface PATH for get supported currencies
    // 所支持的货币的接口地址
    const SUPPORTED_CURRENCIES_PATH = '/pilipay/currencies';

    /**
     * The interface URL for submit order
     * @return string
     */
    public static function getSubmitOrderUrl(){
        return (self::USE_HTTPS ? 'https' : 'http') . '://' . self::PILIBABA_DOMAIN . self::SUBMIT_ORDER_PATH;
    }

    /**
     * The interface URL for updating tracking number
     * @return string
     */
    public static function getUpdateTrackNoUrl(){
        return (self::USE_HTTPS ? 'https' : 'http') . '://' . self::PILIBABA_DOMAIN . self::UPDATE_TRACK_NO_PATH;
    }

    /**
     * The interface URL for barcode
     * @return string
     */
    public static function getBarcodeUrl(){
        return (self::USE_HTTPS ? 'https' : 'http') . '://' . self::PILIBABA_DOMAIN . self::BARCODE_PATH;
    }

    /**
     * The interface path for warehouse address list
     * @return string
     */
    public static function getWarehouseAddressListUrl(){
        return (self::USE_HTTPS ? 'https' : 'http') . '://' . self::PILIBABA_DOMAIN . self::WAREHOUSE_ADDRESS_PATH;
    }

    /**
     * The interface URL for get supported currencies
     * @return string
     */
    public static function getSupportedCurrenciesUrl(){
        return (self::USE_HTTPS ? 'https' : 'http') . '://' . self::PILIBABA_DOMAIN . self::SUPPORTED_CURRENCIES_PATH;
    }
}