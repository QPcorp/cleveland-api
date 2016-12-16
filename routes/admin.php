<?php

/*
 *
 * Admin Route Setup
 * Route: /user
 * Level: N/A 
 *
*/

//Set SQLite Database
require_once 'services/mysql-connect.php';
$db = connect_db();

//Get a list of current users for company
$app->get('/admin', function () use ($db, $app) {
    $sth = $db->query('select * from users where level = 5');
    echo json_encode($sth->fetchAll(PDO::FETCH_CLASS));
});

//Create a new user
$app->post('/admin', function () use ($db, $app) {
    $body = $app->request->getBody();
    $data = json_decode($body);
    //$pdo = new PDO("sqlite:db/users.db");

    $user = $data->user;
    $hash = password_hash($data->password, PASSWORD_DEFAULT);
    $email = $data->email;
    $first_name = $data->first_name;
    $last_name = $data->last_name;
    $phone = $data->phone;
    $company_id = $data->company_id;
    $level = 5;

    $query = $db->exec("INSERT INTO users (user, hash, email, first_name, last_name, phone, company_id, level, active) VALUES ('{$user}', '{$hash}', '{$email}','{$first_name}', '{$last_name}','{$phone}', '{$company_id}','{$level}', 1)");

    $msg = 'User Created Successfully';
    returnResult('add', $user, $msg);
});