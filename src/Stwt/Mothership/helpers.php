<?php

if (!function_exists('mo_create')) {
    function mo_create($action = null)
    {
        return  Stwt\Mothership\LinkFactory::single($action);
    }
}

if (!function_exists('mo_edit')) {
    function mo_edit($id, $action = null)
    {
        return  Stwt\Mothership\LinkFactory::resource($id, $action);
    }
}
