<?php

return [
    /**
     * Authenticated user attribute for identify the current client.
     * 
     * If there is no authenticated user a `anonymous` will be used.
     * Default is `Auth::user()->username`
     */

    'client_identifier' => 'username',

    /**
     * Database connection name for the dashboard.
     * 
     * By default it uses different connection. You must create it.
     * Or set it to `null` if want to use same connection from target app.
     */

    'connection' => 'dashboard',
];
