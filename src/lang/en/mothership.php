<?php

return array(
    'alert' => [
        'create' => [
            'success' => ':singular <b>:resource</b> added succesfully.',
            'error'   => 'There was a problem saving the :singular <b>:resource</b>.
                          Please correct errors in the form.',
        ],
        'delete' => [
            'success' => ':singular succesfully <b>deleted</b>.',
            'error'   => 'There was a problem <b>deleting</b> the :singular <b>:resource</b>.
                          Please confirm you really wish to delete this :singular.',
        ],
        'massDelete' => [
            'success' => 'Selected :plural succesfully <b>deleted</b>.',
            'empty'   => 'No <b>:plural</b> were selected for deletion.
                          Tick the items you wish to delete and try again.',
            'error'   => 'There was a problem <b>deleting</b> selected :plural.
                          Please try again.',
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

        'rcreate' => [
            'success' => ':singular <b>:resource</b> added succesfully to :rresource.',
            'error'   => 'There was a problem saving the :singular <b>:resource</b>.
                          Please correct errors in the form.',
        ],
        'rdelete' => [
            'success' => ':singular succesfully <b>deleted</b>.',
            'error'   => 'There was a problem <b>deleting</b> the :singular <b>:resource</b>.
                          Please confirm you really wish to delete this :singular.',
        ],
        'rmassDelete' => [
            'success' => 'Selected :plural succesfully <b>deleted</b>.',
            'empty'   => 'No <b>:plural</b> were selected for deletion.
                          Tick the items you wish to delete and try again.',
            'error'   => 'There was a problem <b>deleting</b> selected :plural.
                          Please try again.',
        ],
        'redit' => [
            'success' => ':singular <b>:resource</b> updated succesfully.',
            'error'   => 'There was a problem saving the :singular <b>:resource</b>.
                          Please correct errors in the form.',
        ],
    ],
    'title' => [
        'index'    => 'All :plural',
        'create'   => 'Create a new :singular.',
        'show'     => 'View :singular: :resource',
        'edit'     => 'Edit :singular: :resource',
        'hasOne'   => 'Update :plural :rsingular: :resource',
        // related titles
        'rindex'   => 'All :rplural :plural',
        'rcreate'   => 'Create a new :singular for :rresource.',
        'rshow'     => 'View :rresource’s :singular: :resource',
        'redit'     => 'Edit :rresource’s :singular: :resource',
    ],
    'caption' => [
        'index' => 'Displaying all :plural',
        'rindex' => 'Displaying all of :rresource’s :plural',
    ]
);
