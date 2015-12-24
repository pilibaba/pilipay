<?php


class PilipayConfig
{
    // Whether use HTTPS
    // 是否使用HTTPS
    private static $useHttps = true;

    // Whether it use production env.
    // 是否是生产环境
    private static $useProductionEnv = true;

    // The domain of pilibaba
    // 霹雳爸爸的域名
    const PILIBABA_DOMAIN_PRODUCTION = 'www.pilibaba.com';
    const PILIBABA_DOMAIN_TEST = 'pre.pilibaba.com';

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
     * Get whether to use HTTPS
     * 获取是否使用HTTPS
     * @return bool
     */
    public static function useHttps(){
        return self::$useHttps;
    }

    /**
     * Set whether to use HTTPS
     * 设置是否使用HTTPS
     * @param bool|true $useHttps
     */
    public static function setUseHttps($useHttps=true){
        self::$useHttps = $useHttps;
    }

    /**
     * Get whether to use production env.
     * 获取是否使用生产环境
     * @return bool
     */
    public static function useProductionEnv(){
        return self::$useProductionEnv;
    }

    /**
     * Set whether to use production env.
     * 设置是否使用生产环境
     * @param bool|true $isProduction
     */
    public static function setUseProductionEnv($isProduction=true){
        self::$useProductionEnv = $isProduction;
    }

    /**
     * The host (including the protocol) of pilibaba
     * @return string
     */
    public static function getPilibabaHost(){
        if (self::$useProductionEnv){
            return (self::$useHttps ? 'https' : 'http') . '://' . self::PILIBABA_DOMAIN_PRODUCTION;
        } else {
            return 'http://' . self::PILIBABA_DOMAIN_TEST;
        }
    }

    /**
     * The interface URL for submit order
     * @return string
     */
    public static function getSubmitOrderUrl(){
        return self::getPilibabaHost() . self::SUBMIT_ORDER_PATH;
    }

    /**
     * The interface URL for updating tracking number
     * @return string
     */
    public static function getUpdateTrackNoUrl(){
        return self::getPilibabaHost() . self::UPDATE_TRACK_NO_PATH;
    }

    /**
     * The interface URL for barcode
     * @return string
     */
    public static function getBarcodeUrl(){
        return self::getPilibabaHost() . self::BARCODE_PATH;
    }

    /**
     * The interface path for warehouse address list
     * @return string
     */
    public static function getWarehouseAddressListUrl(){
        return self::getPilibabaHost() . self::WAREHOUSE_ADDRESS_PATH;
    }

    /**
     * The interface URL for get supported currencies
     * @return string
     */
    public static function getSupportedCurrenciesUrl(){
        return self::getPilibabaHost() . self::SUPPORTED_CURRENCIES_PATH;
    }
}