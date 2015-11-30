<?php
class PilipayError extends Exception
{
    const INVALID_ARGUMENT = 411;
    const REQUIRED_ARGUMENT_NO_EXIST = 412;
    const INVALID_SIGN = 413;
    const PROPERTY_NOT_EXIST = 414;
    const INVALID_CURL_PARAMS_FORMAT = 511;

    /**
     * @param int $errorCode
     * @param array|string $errorData
     * @param Exception|null $previous
     */
    public function __construct($errorCode, $errorData, $previous=null){
        $msg = $this->buildErrorMessage($errorCode, $errorData);
        parent::__construct($msg, $errorCode, $previous);
    }

    /**
     * @param int $errorCode
     * @param array|string $errorData
     * @return string
     */
    protected function buildErrorMessage($errorCode, $errorData){
        if (is_array($errorData)){
            $params = array();
            foreach ($errorData as $key => $val){
                $params['{' . $key .'}'] = $val;
            }
        } else {
            $params = array('{}' => $errorData, '{0}' => $errorData);
        }

        return strtr(self::$errorCodeToMessageMap[$errorCode], $params);
    }

    protected static $errorCodeToMessageMap = array(
        self::INVALID_ARGUMENT => 'Invalid {name}: {value}',
        self::REQUIRED_ARGUMENT_NO_EXIST => 'The required {name} is empty: {value}',
        self::INVALID_SIGN => 'Invalid sign: {}',
        self::PROPERTY_NOT_EXIST => 'Property not exist: {}',
        self::INVALID_CURL_PARAMS_FORMAT => 'Invalid CURL params\' format: {}',
    );
}
