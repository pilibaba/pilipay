<?php
/**
 * Class PilipayModel
 * -- provide a basic access of properties, whose name is case insensitive.
 */
class PilipayModel
{
    protected $_properties = array();

    /**
     * @param array $properties
     */
    public function __construct($properties=array()){
        if (!empty($properties)){
            $this->setProperties($properties);
        }
    }

    /**
     * @param $properties
     */
    public function setProperties($properties){
        foreach ($properties as $name => $value) {
            $this->{$name} = $value;
        }
    }

    /**
     * get a property
     * @param string $name property's name (case insensitive)
     * @return mixed property's value
     */
    public function __get($name){
        $getter = 'get' . $name;
        if (method_exists($this, $getter)){
            return $this->{$getter}();
        } else {
            $name = strtolower($name);
            return $this->_properties[$name];
        }
    }

    /**
     * set a property
     * @param string $name property's name (case insensitive)
     * @param mixed $value property's value
     */
    public function __set($name, $value){
        $setter = 'set' . $name;
        if (method_exists($this, $setter)){
            $this->{$setter}($value);
        } else {
            $name = strtolower($name);
            $this->_properties[$name] = $value;
        }
    }

    /**
     * verify whether all fields are OK
     * @throws PilipayError
     */
    public function verifyFields(){
        // check numeric fields
        foreach ($this->getNumericFieldNames() as $numericField) {
            $value = $this->{$numericField};
            // (null and '' equals 0)
            if (!is_numeric($value) && $value !== null && $value !== ''){
                throw new PilipayError(PilipayError::INVALID_ARGUMENT,
                    array('name' => $numericField, 'value' => $value));
            } else {
                $this->{$numericField} = ($value ? strval($value) : 0);
            }
        }

        // check required fields
        foreach ($this->getRequiredFieldNames() as $requiredField) {
            if ($this->{$requiredField} === null){
                throw new PilipayError(PilipayError::REQUIRED_ARGUMENT_NO_EXIST,
                    array('name' => $requiredField, 'value'=> $this->{$requiredField}));
            }
        }
    }

    /**
     * @return array numberic fields
     */
    public function getNumericFieldNames(){
        return array();
    }

    /**
     * @return array required fields
     */
    public function getRequiredFieldNames(){
        return array();
    }
}
