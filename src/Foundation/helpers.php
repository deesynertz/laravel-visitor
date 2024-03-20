<?php

use Deesynertz\Visitor\Services\VisitorService;
use SimpleSoftwareIO\QrCode\Facades\QrCode as FacadesQrCode;



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
    function getCommonReasonHelper($id = null)
    {
       return (new VisitorService)->getCommonReasons(null, $id);
    }
}


if (!function_exists('getMainReasons')) {
    function getMainReasons($id = null)
    {
       return (new VisitorService)->getCommonReasons('parent', $id);
    }
}

if (!function_exists('getChildsReasons')) {
    function getChildsReasons($id = null)
    {
       return (new VisitorService)->getCommonReasons('childs', $id);
    }
}


if (!function_exists('responseBatch')) {
    function responseBatch($resultQuery, $params = null, $perPage = null)
    {
        if (!is_null($perPage)) {
            return $resultQuery->paginate($perPage)->appends(arr_only($params));
        }
        
        return isset($params->query) ? $resultQuery:$resultQuery->get(); ;
    }
}

if (!function_exists('httpResponseAttr')) {
    function httpResponseAttr($status = false, $code = 404, $message = null, $content = null)
    {
        return (object)[
            'status' => $status,
            'code' => $code,
            'message' => $message ?? 'Something went wrong!',
            'content' => $content
        ];
    }
}

if (!function_exists('QRCodeGeneratorHelper')) {
    function QRCodeGeneratorHelper($data, $size = 350)
    {
        return FacadesQrCode::size($size)->generate($data);
    }
}




