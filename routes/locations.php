<?php

/*
 *
 * Locations Route Setup
 * Route: /locations
 * Level: 1 + 
 *
*/

$db = connect_db();

//Get a list of customers
$app->get('/locations', function () use ($db, $app) {
    $sth = $db->query('select * from locations;');
    echo json_encode($sth->fetchAll(PDO::FETCH_CLASS));
});

//Get data about a single customer
$app->get('/locations/:id', function ($id) use ($db, $app) {
    $sth = $db->prepare('SELECT * FROM locations WHERE id = '.$id.' LIMIT 1;');
    $sth->execute();
    echo json_encode($sth->fetchAll(PDO::FETCH_CLASS));
});

$app->get('/locations/deactivate/:id', function ($id) use ($db, $app) {
    $sth = $db->prepare('UPDATE locations SET active = 0 where id = '.$id.';');
    $sth->execute();
    echo '{"msg":"Location Deactivated"}';//json_encode($sth->fetchAll(PDO::FETCH_CLASS));
});

$app->get('/locations/company/:id', function ($id) use ($db, $app) {
    $sth = $db->prepare('SELECT * FROM locations WHERE company_id = '.$id.';');
    $sth->execute();
    echo json_encode($sth->fetchAll(PDO::FETCH_CLASS));
});

//Create a Customer
$app->post('/locations', function () use ($db, $app) {
    
    //Get Request
    $body = $app->request->getBody();
    $data = json_decode($body);
    
    //Values Setup
    $location_name = $data->name;
    $address = $data->address;
    $property_id = $data->property_id;
    $company_id = 'TEST';

    $sth = $db->exec("INSERT INTO locations(name, address, property_id, company_id) VALUES ('{$location_name}', '{$address}', '{$property_id}', '{$company_id}');");

    $msg = 'Location Created Successfully';
    returnResult('Location created'.$sth, $sth, $msg);
});

$app->post('/locations/company/:id', function ($id) use ($db, $app) {
    
    //Get Request
    $body = $app->request->getBody();
    $data = json_decode($body);
    
    //Values Setup
    $location_name = $data->name;
    $address = $data->address;
    $city = $data->city;
    $province = $data->province;
    $property_id = $data->nautical_property_id;

    $oth = $db->query('SELECT * FROM locations WHERE company_id = '.$id.' and nautical_property_id = "'.$property_id.'";');
    $oth = $oth->fetchAll(PDO::FETCH_CLASS);
    if(count($oth) > 0){
        echo '{"error":true,"msg":"Property Already exisits"}';
    } else {
        $sth = $db->exec("INSERT INTO locations(name, address, city, province, nautical_property_id, company_id) VALUES ('{$location_name}', '{$address}', '{$city}', '{$province}', '{$property_id}', '{$id}');");

        $msg = 'Location Created Successfully';
        returnResult('Location created'.$sth, $sth, $msg);
    }

    
});

//Delete a Session
$app->delete('/locations/:id', function ($id) use ($db) {
    $sth = $db->prepare('DELETE FROM locations WHERE id = ?;');
    $sth->execute(array(intval($id)));

    returnResult('delete', $sth->rowCount() == 1, $id);
});

