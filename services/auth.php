<?php

//Auth Service

use \Slim\Middleware\HttpBasicAuthentication\PdoAuthenticator;

if($resourceUri == '/admin'){
    //Basic Auth for Admin Path
    $app->add(new \Slim\Middleware\HttpBasicAuthentication(array(
        "path" => "/admin",
        "realm" => "Protected",
        "secure" => false,
        "users" => array(
            "qpadmin" => "locomobi2015"
        ),
        "error" => function ($arguments) use ($app) {
            $response["status"] = "error";
            $response["message"] = $arguments["message"];
            $app->response->write(json_encode($response, JSON_UNESCAPED_SLASHES));
        }
    )));
} else if($resourceUri == '/status'){
    $app->get("/status", function() {
        echo '{"message":"Success - The API is up and running."}';
    });
} else {
    //Basic Authorization For all Application Paths
    //$pdo2 = new PDO("sqlite:db/users.db");
    $db = connect_db();
    $app->add(new \Slim\Middleware\HttpBasicAuthentication(array(
        "path" => '/*',
        "realm" => "Protected",
        "secure" => false,
        "authenticator" => new PdoAuthenticator(array(
            "pdo" => $db,
            "table" => "users",
            "user" => "user",
            "hash" => "hash"
        )),
        "error" => function ($arguments) use ($app) {
            $response["status"] = "error";
            $response["message"] = $arguments["message"];
            $app->response->write(json_encode($response, JSON_UNESCAPED_SLASHES));
        }
    )));
}







