<?php

function smarty_modifier_z_fen2yuan($money)
{
    return number_format($money/100, 2, '.', ',');
}
