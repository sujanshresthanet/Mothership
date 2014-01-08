<?php

/*
 * Composer for head partial
 */
View::composer(
    'mothership::theme.common.head',
    function ($view) {
        $view->with('app_name', Config::get('mothership::appTitle'));
        $view->with('app_style',  URL::asset(Config::get('mothership::appStyle')));

        if (!isset($view->html_class))
            $view->with('html_class', '');
        if (!isset($view->html_id))
            $view->with('html_id', '');
    }
);

/*
 * Composer for foot partial
 */
View::composer(
    'mothership::theme.common.foot',
    function ($view) {
        $view->with('app_script', URL::asset(Config::get('mothership::appScript')));
    }
);

/*
 * Composer for navbar partial
 */
View::composer(
    'mothership::theme.common.navbar',
    function ($view) {
        $view->with('app_name', Config::get('mothership::appTitle'));
        $view->with('navigation', Config::get('mothership::primaryNavigation'));
    }
);

/*
 * Composer for single layout view
 */
View::composer(
    'mothership::theme.layouts.single',
    function ($view) {
        $view->with('breadcrumbs', Stwt\Mothership\Crumbs::generate());
    }
);