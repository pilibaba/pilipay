<?php
/**
 * Class PilipayLogger
 * This class is used for customizing logging.
 *
 * For example:
 *
 *   // to record logs into a file:
 *   PilipayLogger::instance()->setHandler(function($level, $msg){
 *       file_put_contents('path/to/pilipay/log/file', sprintf('%s %s: %s'.PHP_EOL, date('Y-m-d H:i:s'), $level, $msg));
 *   });
 *
 */
class PilipayLogger
{
    /**
     * @param callable $handler function ($level, $msg)...
     */
    public function setHandler($handler){
        $this->handler = $handler;
    }

    /**
     * @param $level string error/info/debug...
     * @param $msg string
     */
    public function log($level, $msg){
        if (!is_null($this->handler)){
            call_user_func($this->handler, $level, $msg);
        }
    }

    /**
     * @return PilipayLogger
     */
    public static function instance(){
        if (!self::$instance){
            self::$instance = new PilipayLogger();
        }

        return self::$instance;
    }

    protected static $instance;
    private $handler = null;
}
