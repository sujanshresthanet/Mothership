<?php

if (!function_exists('mo_edit')) {
    function mo_edit($id, $action = null)
    {
        return  Stwt\Mothership\LinkFactory::resource($id, $action);
    }
}
