<?php

return array(
    'alerts' => [
        'create' => [
            'success' => ':singular <b>:resource</b> updated succesfully.',
            'error'   => 'There was a problem saving the :singular <b>:resource</b>.
                          Please correct errors in the form.',
        ],
        'delete' => [
            'success' => ':singular succesfully <b>deleted</b>.',
            'error'   => 'There was a problem <b>deleting</b> the :singular <b>:resource</b>.
                          Please correct errors in the form.',
        ],
        'edit' => [
            'success' => ':singular <b>:resource</b> updated succesfully.',
            'error'   => 'There was a problem saving the :singular <b>:resource</b>.
                          Please correct errors in the form.',
        ],
        'password' => [
            'success' => ':singular <b>:resource</b> password has been changed succesfully.',
            'error'   => 'There was a changing the password of :singular <b>:resource</b>.
                          Please correct errors in the form.',
        ],
    ],
    'titles' => [
        'index'    => 'All :plural',
        'show'     => 'View :singular: :resource',
        'edit'     => 'Edit :singular: :resource',
        'password' => 'Update password: :resource',
    ],
);
