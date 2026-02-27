<?php

return [
    'index' => [
        'title' => 'Manage users',
        'add_user' => 'Add user',
        'search' => 'Search...',
        'no_users_found' => 'No user found with :search in the name.',
        'edit' => 'Edit user',
        'delete' => 'Delete user',
        'confirm_delete' => 'Are you sure you want to delete this user?',
    ],
    'create' => [
        'title' => 'Add user',
        'name' => 'Name',
        'email' => 'Email address',
        'save' => 'Save',
    ],
    'edit' => [
        'title' => 'Edit :name',
        'title_self' => 'Edit your profile',
        'name' => 'Name',
        'email' => 'Email address',
        'save' => 'Save',
        'delete' => 'Delete',
        'confirm_delete' => 'Are you sure you want to delete this user?',
    ],
    'password' => [
        'title' => 'Change your password',
        'current_password' => 'Current password',
        'new_password' => 'New password',
        'confirm_password' => 'Confirm your new password',
        'save' => 'Save',
    ],
    'flash' => [
        'created_warning' => 'User “<i>:name</i>” was successfully added, but we could not send an email with instructions to create a password. The following message was returned: <em>:status</em>',
        'created_success' => 'User “<i>:name</i>” was successfully added and an email was sent with instructions to create a password.',
        'updated' => 'User “<i>:name</i>” was successfully updated!',
        'deleted' => 'User “<i>:name</i>” was successfully deleted!',
        'password_updated' => 'Your password was successfully updated!',
    ],
];
