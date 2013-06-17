<?php

if (!function_exists('mo_index')) {
    function mo_index($action = null)
    {
        return  Stwt\Mothership\LinkFactory::collection($action);
    }
}

if (!function_exists('mo_create')) {
    function mo_create($action = null)
    {
        return  Stwt\Mothership\LinkFactory::single($action);
    }
}

if (!function_exists('mo_edit')) {
    function mo_edit($id = null, $action = null)
    {
        return  Stwt\Mothership\LinkFactory::resource($id, $action);
    }
}
