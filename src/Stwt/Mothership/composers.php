<?php

/*
 * Composer for head partial
 */
View::composer(
    'mothership::theme.common.head',
    function ($view) {
        $view->with('app_name', Config::get('mothership::appTitle'));
        $view->with('app_style',  URL::asset(Config::get('mothership::appStyle')));
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