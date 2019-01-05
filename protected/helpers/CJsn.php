<?php

class CJsn {

    /**
     * Requires PHP versions 5.3 or more!
     * @param $string
     * @return bool
     */
    public static function isJson($string)
    {
        return (!empty($string)
            && is_string($string)
            && is_array(json_decode($string, true))
            && (json_last_error() == JSON_ERROR_NONE))
            ? true
            : false;
    }
} 