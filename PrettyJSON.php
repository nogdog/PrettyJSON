<?php

/**
 * Make a JSON string look nice (multiple lines with indents)
 * Sample usage:
 *   echo "<pre>".PrettyJSON::format($json_string)."</pre>";
 */
class PrettyJSON
{
    public static function format($json)
    {
        $object = json_decode($json);
        if (is_null($object)) {
            throw new Exception('Could not parse input as JSON');
        }
        if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
            return json_encode($object, JSON_PRETTY_PRINT);
        }
        unset($object);
        // 4 spaces so same as above:
        return self::jsonpp($json, '    ');
    }

    /**
     * From https://github.com/ryanuber/projects/blob/master/PHP/JSON/jsonpp.php
     */
    private static function jsonpp($json, $istr='  ')
    {
        $result = '';
        for ($p=$q=$i=0; isset($json[$p]); $p++) {
            $json[$p] == '"' && ($p>0?$json[$p-1]:'') != '\\' && $q=!$q;
            if (!$q && strchr(" \t\n\r", $json[$p])) {
                continue;
            }
            if (strchr('}]', $json[$p]) && !$q && $i--) {
                strchr('{[', $json[$p-1]) || $result .= "\n".str_repeat($istr, $i);
            }
            $result .= $json[$p];
            if (strchr(',{[', $json[$p]) && !$q) {
                $i += strchr('{[', $json[$p])===false?0:1;
                strchr('}]', $json[$p+1]) || $result .= "\n".str_repeat($istr, $i);
            }
        }
        return $result;
    }
}
