<?php

/*
 *
 * Login Route Setup
 * Route: /login
 * Level: 1 + 
 *
*/

//Set SQLite Database
$db = connect_db();

//Get a list of current users for company
$app->get('/login', function () use ($db, $app) {
    $req = $app->request();
    $user = $req->headers('PHP_AUTH_USER');
    $sth = $db->query('select * from users where user ="'.$user.'" and active = 1;');

    $userJson = json_encode($sth->fetchAll(PDO::FETCH_CLASS));
    $app->setCookie('locomobi_profile','"'.$userJson.'"', '2 days','/','api.locomobi.dev');
    $userInfo = json_decode($userJson, true);


    $userProfile = json_decode($userJson);
    $company = $userProfile[0]->company_id;
    $app->setCookie('company', $company, '2 days');
    echo $userJson;
    //echo '{"message":"Logged In As '.$userProfile[0]->first_name.' '.$userInfo[0]['last_name'].' ('.$userInfo[0]['email'].')'.'"}';
    //json_encode($sth->fetchAll(PDO::FETCH_CLASS));
});