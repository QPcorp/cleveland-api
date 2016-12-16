<?php

/*
 *
 * License Plates Route Setup
 * Route: /lpn
 * Level: 1 + 
 *
*/

require_once 'services/mysql-connect.php';
$db = connect_db();

//Get a list of license plates
$app->get('/lpn/customer/:id', function ($id) use ($db, $app) {
    $sth = $db->query('select * from license_plates where customer_id = '. $id .';');
    echo json_encode($sth->fetchAll(PDO::FETCH_CLASS));
});

//Create a License Plate
$app->post('/lpn/:id', function ($id) use ($db, $app) {
    
    //Get Request
    $body = $app->request->getBody();
    $data = json_decode($body);
    
    //Values Setup
    $lpn = $data->lpn;
    $make = $data->make;
    $model = $data->model;
    $year = $data->year;

    try{
        $sth = $db->exec("INSERT INTO license_plates(customer_id, lpn, make, model, year) VALUES ('{$id}', '{$lpn}', '{$make}', '{$model}', '{$year}');");
        $msg = 'LPN Added Successfully: '.$id;

    } catch (\PDOException $e) {
            $msg = "Cannot add LPN at this time.";
            $sth = false;
            $app->halt(400, '{"msg":'.$msg.'}');
    }

    
    returnResult('LPN: '.$email, $sth, $msg);
});

//Update a Customer
$app->put('/lpn/:id', function ($id) use ($db, $app) {
    
    //Get Request
    $body = $app->request->getBody();
    $data = json_decode($body);
    
    //Values Setup
    $id = $data->id;
    $email = $data->email;
    $group_name = $data->group_name;
    $discount_amount = $data->discount_amount;

    $sth = $db->exec("UPDATE groups SET lpn = '{$lpn}', make = '{$make}', model = '{$model}',year = '{year}'WHERE id = '{$id}';");

    returnResult('Updated Group: '.$id, $sth, $id);
});

//Delete a Customer
$app->delete('/lpn/:id', function ($id) use ($db) {
    $sth = $db->prepare('DELETE FROM license_plates WHERE id = ?;');
    $sth->execute(array(intval($id)));

    returnResult('delete', $sth->rowCount() == 1, $id);
});

