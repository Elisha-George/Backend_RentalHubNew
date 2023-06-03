
<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);



require __DIR__.'/CONFI/connection.php';


// Connection is successfully made

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Origin: http://localhost:3000");
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-with");


$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case "POST":
        $user = json_decode(file_get_contents('php://input')); 
        if ($user === null) {
            
            $response = ['status' => 0, 'message' => 'Invalid JSON data'];
            echo json_encode($response);
            exit;
        }
        
        //  Check that the database connection is working correctly
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $userId = $user->userId;
        $property_id = $user->property_id;

        // Insert the property data into the 'property' table
        $bid_sql = "INSERT INTO bidding ( user_id, property_id, name, email, amount, phone, time) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
        $bid_stmt = $conn->prepare($bid_sql);
       
        if (!$bid_stmt) {
            echo $conn->error;
            exit;
        }

        $bid_stmt->bind_param("iississ",$userId,$property_id, $user->name, $user->email, $user->amount, $user->phone, $user->time);

        if (!$bid_stmt->execute()) {
            $response = ['status' => 0, 'message' => 'Failed to create record: ' . $bid_stmt->error];
            echo json_encode($response);
            exit;
        }

        $response = ['status' => 1, 'message' => 'Record created successfully'];
        echo json_encode($response);

        break;
}
?>
