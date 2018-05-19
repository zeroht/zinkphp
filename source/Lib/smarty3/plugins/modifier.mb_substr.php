<?php

function smarty_modifier_mb_substr($originStr, $startPos, $length)
{
    if (!$originStr) {
        return false;
    }

    return mb_substr($originStr, $startPos, $length);
}