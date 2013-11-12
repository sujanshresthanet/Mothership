<?php

/*
|--------------------------------------------------------------------------
| Authentication Filter
|--------------------------------------------------------------------------
|
| The following filter is used to verify that the user of the current
| session is logged into the mothership application.
|
*/

Route::filter(
    'mothership',
    function () {
        if (Sentry::check()) {
            $user = Sentry::getUser();
            if ($user->hasAccess('admin')) {
                return;
            } else {
                return Redirect::to('/');
            }
        }
        return Redirect::to('admin/login');
        
    }
);
