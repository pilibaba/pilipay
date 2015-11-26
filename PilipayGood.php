<?php


namespace pilipay;


/**
 * Class PilipayGood
 * @package pilipay
 *
 * - required fields:
 * @property $name          string
 * @property $pictureUrl    string
 * @property $price         number       in order.currencyType
 * @property $productUrl    string
 * @property $productId     string
 * @property $quantity      int
 * @property $weight        number
 * @property $weightUnit    string       g/kg/lb/oz
 *
 * - optional fields:
 * @property $attr          string      the good's attributes, like: color, size...
 * @property $category      string      the good's category when taxing
 * @property $height        string      ?? and unit??
 * @property $length        string      ?? and unit??
 * @property $width         string      ?? and unit??
 */
class PilipayGood extends PilipayModel
{
    /**
     * 转换为API中的那种array表示形式
     * @return array
     */
    public function toApiArray(){
        parent::verifyFields();

        return array_map('strval', array(
            // required:
            'name' => $this->name,
            'pictureURL' => $this->pictureUrl,
            'price' => intval($this->price * 100), // API: need a price in cent (in order.currencyType)
            'productURL' => $this->productUrl,
            'productId' => $this->productId,
            'quantity' => intval($this->quantity),
            'weight' => intval(self::convertWeightToGram($this->weight, $this->weightUnit)),

            // optional:
            'attr' => $this->attr,
            'category' => $this->category,
            'height' => $this->height,
            'length' => $this->length,
            'width' => $this->width,
        ));
    }

    /**
     * 将重量转换为以克为单位的数值
     * @param $amount
     * @param $unit
     * @return mixed
     * @throws PilipayError
     */
    public static function convertWeightToGram($amount, $unit){
        switch (strtolower($unit)){
            case 'g':
                return $amount;
            case 'kg':
                return $amount * 1000;
            case 'oz':
                return $amount * 28.3495231; // 1盎司(oz)=28.3495231克(g)
            case 'lb':
                return $amount * 453.59237; // 1磅(lb)=453.59237克(g)
            default:
                throw new PilipayError(PilipayError::INVALID_ARGUMENT, array('name' => 'weightUnit', 'value' => $unit));
        }
    }

    public function getRequiredFieldNames(){
        return array('name', 'pictureUrl', 'price', 'productUrl', 'productId', 'quantity', 'weight', 'weightUnit');
    }

    public function getNumericFieldNames(){
        return array('price', 'weight', 'height', 'length', 'width');
    }
}