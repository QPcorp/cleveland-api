<?php

/*
 *
 * Status Route Setup
 * Route: /
 * Level: 1 + 
 *
*/

$app->get("/", function() {
    echo '{"message":"Success - The API is up and running."}';
});