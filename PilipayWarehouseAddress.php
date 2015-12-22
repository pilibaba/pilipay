<?php

/**
 * Class PilipayWarehouseAddress
 * This class helps to query all warehouse addresses of pilibaba
 *
 * @property string $country        - the country of the warehouse
 * @property string $firstName      - the first name of the receiver
 * @property string $lastName       - the last name of the receiver
 * @property string $address        - the address of the warehouse
 * @property string $city           - the city name of the warehouse
 * @property string $state          - the state name of the warehosue
 * @property string $zipcode        - the zipcode/postcode of the warehouse
 * @property string $tel            - the telephone number of the receiver
 */
class PilipayWarehouseAddress extends PilipayModel
{
    /**
     * query all warehouse addresses
     * @params $resultFormat string objectList or arrayList
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
            return false;
        }

        if ($resultFormat !== 'objectList'){
            return $json;
        }

        $addressList = array();
        foreach($json as $item){
            $addressList[] = new PilipayWarehouseAddress($item);
        }

        return $addressList;
    }
}