<?php

function smarty_modifier_z_strtotime($string)
{
    if ("9999-12-31" == $string){
        return -1;
    }
    
    $time = strtotime($string);
    if ($time == - 1 || $time === false) {
        // strtotime() was not able to parse $string, use "now":
        return 0;
    }

    return $time;
}
