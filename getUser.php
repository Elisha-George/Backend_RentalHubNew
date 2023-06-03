<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require __DIR__.'/CONFI/connection.php';
require __DIR__.'/AuthMiddleware.php';



try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create an instance of the Auth class with the PDO connection
    $allHeaders = getallheaders(); // Assuming you have a function to get the request headers
    $auth = new Auth($pdo, $allHeaders);

    // Call the isValid() method
    echo json_encode($auth->isValid());

    


} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
}
?>







