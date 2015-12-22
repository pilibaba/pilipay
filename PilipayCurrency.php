<?php

/**
 * Class PilipayCurrency
 * This class helps to query all currencies Pilibaba supported.
 *
 * @property string $code   - the currency code of this currency (see ISO_4217)
 *
 */
class PilipayCurrency extends PilipayModel
{
    /**
     * query all available currencies from pilibaba
     * @param string $resultFormat objectList | stringList
     * @return array
     */
    public static function queryAll($resultFormat='objectList'){
        $curl = PilipayCurl::instance();
        $result = $curl->get(PilipayConfig::getWarehouseAddressListUrl());
        if (empty($result)){
            return array();
        }

        $json = json_decode($result, true);
        if (empty($json)){
            return array();
        }

        if ($resultFormat !== 'objectList'){
            return $json;
        }

        $currencies = array();
        foreach ($json as $currencyCode) {
            $currencies[] = new PilipayCurrency(array(
                'code' => $currencyCode
            ));
        }

        return $currencies;
    }
}