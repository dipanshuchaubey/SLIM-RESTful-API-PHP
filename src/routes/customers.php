<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;


// Enable CORS
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', 'http://127.0.0.1:5500')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});


// GET ALL CUSTOMERS

$app->get('/api/customers', function( Request $request, Response $response) {

    $sql = "SELECT * FROM employee_data";

    try {
        // GET DB OBJECT
        $db = new db();
        //CONNECT
        $db = $db->connect();

        $stmt = $db->query($sql);
        $cutomers = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($cutomers, JSON_PRETTY_PRINT);

    } catch(PDOException $e) {
        echo "ERROR";
    }


});

// GET Single CUSTOMERS

$app->get('/api/customer/{id}', function( Request $request, Response $response) {

    $id = $request->getAttribute('id');
    $sql = "SELECT * FROM employee_data WHERE serialNumber = $id";

    try {
        // GET DB OBJECT
        $db = new db();
        //CONNECT
        $db = $db->connect();

        $stmt = $db->query($sql);
        $cutomer = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        echo json_encode($cutomer, JSON_PRETTY_PRINT);
        // echo json_encode($cutomer);

    } catch(PDOException $e) {
        echo "ERROR";
    }


});

// ADD CUSTOMERS

$app->post('/api/customer/add', function( Request $request, Response $response) {

    $name = $request->getParam('name');
    $email = $request->getParam('email');
    $city = $request->getParam('city');
    $state = $request->getParam('state');
    $amount = $request->getParam('amount');

    $sql = "INSERT INTO employee_data(name, email, city, state, amount) VALUES (:name, :email, :city, :state, :amount)";

    try {
        // GET DB OBJECT
        $db = new db();
        //CONNECT
        $db = $db->connect();

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':state', $state);
        $stmt->bindParam(':amount', $amount);

        $stmt->execute();
        echo '{"notice": {"text": "Customer Added"}}';


    } catch(PDOException $e) {
        echo "ERROR";
    }


});

// UPDATE CUSTOMERS

$app->put('/api/customer/update/{id}', function( Request $request, Response $response) {

    $id = $request->getAttribute('id');

    $name = $request->getParam('name');
    $email = $request->getParam('email');
    $city = $request->getParam('city');
    $state = $request->getParam('state');
    $amount = $request->getParam('amount');

    $sql = "UPDATE employee_data SET
                name    = :name,
                email   = :email,
                city    = :city,
                state   = :state,
                amount  = :amount
            WHERE serialNumber = $id";

    try {
        // GET DB OBJECT
        $db = new db();
        //CONNECT
        $db = $db->connect();

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':state', $state);
        $stmt->bindParam(':amount', $amount);

        $stmt->execute();
        echo '{"notice": {"text": "Customer Updated"}}';


    } catch(PDOException $e) {
        echo '{"error": {"text": '.$e->getMessage().'}';
    }


});

// DELETE CUSTOMERS

$app->delete('/api/customer/delete/{id}', function( Request $request, Response $response) {

    $id = $request->getAttribute('id');
    $sql = "DELETE FROM employee_data WHERE serialNumber = $id";

    try {
        // GET DB OBJECT
        $db = new db();
        //CONNECT
        $db = $db->connect();

        $stmt = $db->prepare($sql);
        $stmt->execute();
        $db = null;

        echo '{"notice": {"text": "Customer Deleted"}}';

    } catch(PDOException $e) {
        echo "ERROR";
    }


});

$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});
