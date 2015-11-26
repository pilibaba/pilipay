<?php
/**
 * Class PilipayLogger
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
