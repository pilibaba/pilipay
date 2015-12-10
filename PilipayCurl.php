<?php
class PilipayCurl
{
    private $additionalHeaders;
    private $responseHeaders;
    private $responseContent;

    public function __construct(){
    }

    public function setAdditionalHeaders($headers){
        $this->additionalHeaders = $headers;
    }

    public function post($url, $params, $timeout=30){
        return $this->request('POST', $url, $params, $timeout);
    }

    public function get($url, $params, $timeout=30){
        return $this->request('GET', $url, $params, $timeout);
    }

    public function request($method, $url, $params, $timeout=30){
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

    public function getResponseStatusCode(){
        return $this->getResponseHeader('statusCode');
    }

    public function getResponseStatusText(){
        return $this->getResponseHeader('statusText');
    }

    public function getResponseRedirectUrl(){
        $url = $this->getResponseHeader('redirect_url');
        if ($url){
            return $url;
        } else {
            return $this->getResponseHeader('Location');
        }
    }

    public function getResponseHeader($key){
        return $this->responseHeaders[$key];
    }

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
