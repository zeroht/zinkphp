<?php

function smarty_modifier_function($val, $fun)
{
    if (function_exists($fun)){
        return call_user_func($fun, $val);
    }

    return $val;

}
