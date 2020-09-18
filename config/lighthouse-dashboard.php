<?php

return [
    /**
     * Authenticated user attribute for identify the current client.
     * If there is no authenticated user a `anonymous_client` will be used.
     * 
     * Default is `Auth::user()->username`
     */

    'client_identifier' => 'username'
];
