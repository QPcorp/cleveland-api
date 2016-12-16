<?php

/*
 *
 * Customer Route Setup
 * Route: /employees
 * Level: 1 + 
 *
*/

require_once 'services/mysql-connect.php';
$db = connect_db();

//Get a list of employees
$app->get('/employees', function () use ($db, $app) {
    $sth = $db->query('select * from employees;');
    echo json_encode($sth->fetchAll(PDO::FETCH_CLASS));
});

//Get data about a single customer
$app->get('/employees/:id', function ($id) use ($db, $app) {
    $sth = $db->prepare('SELECT * FROM employees WHERE id = '.$id.' LIMIT 1;');
    $sth->execute();
    echo json_encode($sth->fetchAll(PDO::FETCH_CLASS));
});

$app->get('/employees/filter/by', function () use ($db, $app) {

    $params = $app->request()->params();
    $test = json_encode($params);
    $str = '';

    for($i = 0; $i < count($params); $i ++){
        $keys = array_keys($params);
        $key = $keys[$i];
        $value = $params[$key];
        if($key == 'lpn'){
            $sth = $db->query('SELECT * FROM license_plates where lpn = "'.$value.'";');
            $user_data = $sth->fetchAll(PDO::FETCH_CLASS);
            $user_id = $user_data->customer_id;
            $str .= ' and ' . $key.' = "'. $user_id.'"';
        } else {
            $str .= ' and ' . $key.' = "'. $value.'"';
        }
    }

    if(count($params) < 1){
        echo '{"error":true, "msg":"Filters are required"}';
    } else {
        $sth = $db->query('SELECT * FROM employees where active = 1 '.$str.';');
        $data = json_encode($sth->fetchAll(PDO::FETCH_CLASS));
        echo $data;
    }
    
});



//Get data about employees from a particular company
$app->get('/employees/company/:id', function ($id) use ($db, $app) {
    $sth = $db->query('SELECT employees.id, employees.first_name, employees.last_name,employees.email,employees.create_time,employees.address,employees.city,employees.zipcode,employees.points,employees.active from employees inner JOIN company_memberships ON company_memberships.customer_id = employees.id and company_memberships.company_id = '.$id.';');
    //$customer_info = $sth->fetchAll(PDO::FETCH_CLASS);
    $customer_info = json_encode($sth->fetchAll(PDO::FETCH_CLASS));
    echo $customer_info;
});

//Get data about employees from a particular company with LPN
$app->get('/employees/company/:id/dashboard', function ($id) use ($db, $app) {
    $sth = $db->query('SELECT employees.id, employees.first_name, employees.last_name,employees.email,employees.create_time,employees.address,employees.city,employees.zipcode,employees.points,employees.active from employees inner JOIN company_memberships ON company_memberships.customer_id = employees.id and company_memberships.company_id = '.$id.';');
    //$customer_info = $sth->fetchAll(PDO::FETCH_CLASS);
    $customer_array = $sth->fetchAll(PDO::FETCH_CLASS);

    for($i = 0; $i < count($customer_array); $i ++){
        $customer = $customer_array[$i];
        $customer_id = $customer->id;
        $lpn = $db->query('select * from license_plates where customer_id = '. $customer_id .';');
        $lpns = $lpn->fetchAll(PDO::FETCH_CLASS);
        $lpns_collection = array();
        for($z = 0; $z < count($lpns); $z++){
            array_push($lpns_collection, $lpns[$z]->lpn);
        }
        $customer->lpns = $lpns_collection;
    }
    $data = json_encode($customer_array);

    echo $data;
});

//Create a Customer
$app->post('/employees', function () use ($db, $app) {
    
    //Get Request
    $body = $app->request->getBody();
    $data = json_decode($body);
    
    //Values Setup
    $badge_encode_number = $data->badge_encode_number;
    $email = $data->email;
    $job_title = $data->job_title;
    $name_suffix = $data->name_suffix;
    $first_name = $data->first_name;
    $middle_name = $data->middle_name;
    $last_name = $data->last_name;
    $location_code = $data->location_code;
    $location_description = $data->location_description;
    $ccf_mail_code = $data->ccf_mail_code;
    $division = $data->division;
    $department_name = $data->department_name;
    $supervisor_firt_name = $data->supervisor_firt_name;
    $supervisor_last_name = $data->supervisor_last_name;
    $hire_date = $data->hire_date;
    $termination_date = $data->termination_date;
    $tag_number = $data->tag_number;

    try{
        $sth = $db->exec("INSERT INTO employees(badge_encode_number, email, job_title, name_suffix, first_name, middle_name, last_name, location_code, location_description, ccf_mail_code, division, department_name, supervisor_firt_name, supervisor_last_name, hire_date, termination_date, tag_number) VALUES ('{$email}', '{$first_name}', '{$last_name}', '{$address}', '{$city}', '{$zip}', 1);");
        $msg = 'User Created Successfully: '.$user_id;

    } catch (\PDOException $e) {
            $msg = "Duplicate User";
            $sth = false;
            $app->halt(400, '{"msg":"Duplicate User"}');
    }


    //'{"msg":"Duplicate User"}';
    if($sth != 1){
        
    };

    $current = $db->query("select * from employees where email = '{$email}';");
    $datax = $current->fetchAll(PDO::FETCH_CLASS);
    $user_id = $datax[0]->id;

    for($i = 0; $i < count($lpn); $i ++){
        $license = $lpn[$i]->lpn;
        $make = $lpn[$i]->make;
        $model = $lpn[$i]->model;
        $year = $lpn[$i]->year;

        $mth = $db->exec("INSERT INTO license_plates(user_id, lpn, make, model, year) VALUES ('{$user_id}', '{$license}', '{$make}', '{$model}', '{$year}');");
    }
    
    echo json_encode($datax);
    //returnResult('User: '.$email, $sth, $msg);
});

//Create a Customer
$app->post('/employees/company/:id', function ($id) use ($db, $app) {
    
    //Get Request
    $body = $app->request->getBody();
    $data = json_decode($body);
    
    //Values Setup
    $email = $data->email;
    $first_name = $data->first_name;
    $last_name = $data->last_name;
    $address = $data->address;
    $city = $data->city;
    $zip = $data->zipcode;
    $lpn = $data->lpns;

    try{
        $sth = $db->exec("INSERT INTO employees(email, first_name, last_name, address, city, zipcode, active, points) VALUES ('{$email}', '{$first_name}', '{$last_name}', '{$address}', '{$city}', '{$zip}', 1, 0);");
        $msg = 'User Created Successfully: '.$user_id;

    } catch (\PDOException $e) {
        $msg = "Duplicate User";
        $sth = false;

        $current = $db->query("select * from employees where email = '{$email}';");
        $datax = $current->fetchAll(PDO::FETCH_CLASS);
        $customer_id = $datax[0]->id;

        $app->halt(400, '{"error":true, "msg":"Duplicate User '.$email.'", "id":'.$customer_id.'}');
    }

    if($sth != 1){
        
    };

    $current = $db->query("select * from employees where email = '{$email}';");
    $datax = $current->fetchAll(PDO::FETCH_CLASS);
    $customer_id = $datax[0]->id;

    $sth = $db->exec("INSERT INTO company_memberships(company_id, customer_id) VALUES ('{$id}', '{$customer_id}');");

    for($i = 0; $i < count($lpn); $i ++){
        $license = $lpn[$i]->lpn;
        $make = $lpn[$i]->make;
        $model = $lpn[$i]->model;
        $year = $lpn[$i]->year;

        $mth = $db->exec("INSERT INTO license_plates(customer_id, lpn, make, model, year) VALUES ('{$customer_id}', '{$license}', '{$make}', '{$model}', '{$year}');");
    }
    
    echo json_encode($datax);
    //returnResult('User: '.$email, $sth, $msg);
});

//Update a Customer
$app->put('/employees/:id', function ($id) use ($db, $app) {
    
    //Get Request
    $body = $app->request->getBody();
    $data = json_decode($body);
    
    //Values Setup
    $email = $data->email;
    $first_name = $data->first_name;
    $last_name = $data->last_name;
    $address = $data->address;
    $city = $data->city;
    $zip = $data->zipcode;
    $active = $data->active;
    //$updateTime = date('Y-m-d H:i:s');

    $sth = $db->exec("UPDATE employees SET email = '{$email}', first_name = '{$first_name}', last_name = '{$last_name}', address = '{$address}', city = '{$city}', zipcode = '{$zip}', active = '{$active}' WHERE id = '{$id}';");

    //returnResult('Updated User: '.$id, $sth, $id);
    $oth = $db->prepare('SELECT * FROM employees WHERE id = '.$id.' LIMIT 1;');
    $oth->execute();
    echo json_encode($oth->fetchAll(PDO::FETCH_CLASS));

});

//Delete a Customer
$app->delete('/employees/:id', function ($id) use ($db) {
    $sth = $db->prepare('DELETE FROM employees WHERE id = ?;');
    $sth->execute(array(intval($id)));

    returnResult('delete', $sth->rowCount() == 1, $id);
});

