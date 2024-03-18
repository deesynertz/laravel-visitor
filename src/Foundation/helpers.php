<?php

use Deesynertz\Visitor\Services\VisitorService;


/*
|------------------------------------------------------------------------------
| Session Helpers
|------------------------------------------------------------------------------
| in Laravel, you can have multiple session configurations with different
| expiration times. Laravel provides flexibility in configuring session drivers
| and options, allowing you to define separate session configurations as needed
| for your application.
|
*/

if (!function_exists('getCommonReasonHelper')) {
    function getCommonReasonHelper()
    {
       return (new VisitorService)->getCommonReasons();
    }
}


if (!function_exists('getMainReasons')) {
    function getMainReasons()
    {
       return (new VisitorService)->getCommonReasons('parent');
    }
}

if (!function_exists('getChildsReasons')) {
    function getChildsReasons()
    {
       return (new VisitorService)->getCommonReasons('childs');
    }
}
