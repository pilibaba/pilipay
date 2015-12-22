<?php

/**
 * Class PilipayCurl
 * This class provide an easier access to CURL.
 * 这个类使CURL用起来更方便.
 */
class PilipayCurl
{
    private $additionalHeaders;
    private $responseHeaders;
    private $responseContent;

    /**
     * Nothing to do, just creat the object
     */
    public function __construct(){
    }

    /**
     * Set additional headers if you want to.
     * Normally it's not necessary
     * @param array $headers  in format: header key =>  header value
     */
    public function setAdditionalHeaders($headers){
        $this->additionalHeaders = $headers;
    }

    /**
     * Make a POST request
     * @param string $url               - the URL
     * @param array|string|null $params - if it's a string, it will passed as it is; if it's an array, http_build_query will be used to convert it to a string
     * @param int $timeout              - request timeout in seconds
     * @return string                   - the response content (without headers)
     */
    public function post($url, $params=null, $timeout=30){
        return $this->request('POST', $url, $params, $timeout);
    }

    /**
     * Make a GET request
     * @param string $url               - the URL
     * @param array|string|null $params - if it's a string, it will passed as it is; if it's an array, http_build_query will be used to convert it to a string
     * @param int $timeout              - request timeout in seconds
     * @return string                   - the response content (without headers)
     */
    public function get($url, $params=null, $timeout=30){
        return $this->request('GET', $url, $params, $timeout);
    }

    /**
     * Make a $method request
     * @param string $method            - GET/POST/...
     * @param string $url               - the URL
     * @param array|string|null $params - if it's a string, it will passed as it is; if it's an array, http_build_query will be used to convert it to a string
     * @param int $timeout              - request timeout in seconds
     * @return string                   - the response content (without headers)
     */
    public function request($method, $url, $params=null, $timeout=30){
        $options = array(
            CURLOPT_HTTPGET => false,
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_MAXREDIRS => 0,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_USERAGENT => 'curl',
            CURLOPT_CONNECTTIMEOUT => $timeout,
            CURLOPT_TIMEOUT => $timeout
        );

        switch (strtoupper($method)){
            case 'GET':
                if (!empty($params)){
                    $url .= '?' . (is_array($params) ? http_build_query($params) : strval($params));
                }
                $ch = curl_init($url);
                $options[CURLOPT_HTTPGET] = true;
                break;
            default: // post...
                $ch = curl_init($url);
                $options[CURLOPT_CUSTOMREQUEST] = $method;
                if (!empty($params)){
                    $options[CURLOPT_POSTFIELDS] = (is_array($params) ? http_build_query($params) : strval($params));
                    $this->additionalHeaders['Content-Type'] = 'application/x-www-form-urlencoded; charset=utf-8';
                }
                break;
        }

        $headers = array();
        if (!empty($this->additionalHeaders)){
            foreach ($this->additionalHeaders as $key => $value){
                $headers[] = $key . ': ' . $value;
            }

            $options[CURLOPT_HTTPHEADER] = $headers;
        }

        foreach ($options as $optKey => $optVal) {
            curl_setopt($ch, $optKey, $optVal);
        }

        $response = curl_exec($ch);
        $curlInfo = curl_getinfo($ch);
        $errCode = curl_errno($ch);
        $errMsg = curl_error($ch);
        curl_close($ch);

        PilipayLogger::instance()->log('debug', "CURL: ".print_r(array(
                'request' => array(
                    'method' => $method,
                    'url' => $url,
                    'params' => $params,
                    'headers' => $headers
                ),
                'response' => array(
                    'errno' => $errCode,
                    'error' => $errMsg,
                    'content' => $response,
                )
            ), true));

        $headerSize = $curlInfo['header_size'];
        $this->responseHeaders = self::parseResponseHeader(substr($response, 0, $headerSize));
        $this->responseHeaders['redirect_url'] = $curlInfo['redirect_url'];
        $this->responseContent = substr($response, $headerSize);
        return $this->responseContent;
    }

    /**
     * parse the response headers, convert into key => value formatted array
     * @param string $headerText
     * @return array
     */
    public static function parseResponseHeader($headerText){
        $headers = array();

        foreach (explode("\n", $headerText) as $header) {
            if (preg_match('/^HTTP\/(?<version>\d+\.\d+)\s+(?<statusCode>\d+)\s+(?<statusText>.*)$/', $header, $matches)){
                $headers['version'] = $matches['version'];
                $headers['statusCode'] = $matches['statusCode'];
                $headers['statusText'] = $matches['statusText'];
                continue;
            }

            $delimeterPos = strpos($header, ':');
            if ($delimeterPos !== false){
                $key = trim(substr($header, 0, $delimeterPos));
                $headers[$key] = trim(substr($header, $delimeterPos + 1));
            } else {
                // ignore unknown headers...
            }
        }

        return $headers;
    }

    /**
     * @return string the response's status code, i.e: 200, 301, 400, 500...
     */
    public function getResponseStatusCode(){
        return $this->getResponseHeader('statusCode');
    }

    /**
     * @return string the response's status text, i.e: OK, Found...
     */
    public function getResponseStatusText(){
        return $this->getResponseHeader('statusText');
    }

    /**
     * @return string the URL for redirecting, normally when the status code is 30x
     */
    public function getResponseRedirectUrl(){
        $url = $this->getResponseHeader('redirect_url');
        if ($url){
            return $url;
        } else {
            return $this->getResponseHeader('Location');
        }
    }

    /**
     * @param string $key  - the header key
     * @return string|null - the header value
     */
    public function getResponseHeader($key){
        return $this->responseHeaders[$key];
    }

    /**
     * @return string|null - the response content (without headers)
     */
    public function getResponseContent(){
        return $this->responseContent;
    }

    /**
     * @return PilipayCurl
     */
    public static function instance(){
        static $instance = null;

        if (!$instance){
            $instance = new PilipayCurl();
        }

        return $instance;
    }
}
