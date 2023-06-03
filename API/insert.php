
<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);



include('../CONFI/connection.php');


// Connection is successfully made

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Origin: http://localhost:3000");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
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

        // 1. Check that the image data is being sent correctly from the front-end to the back-end
        var_dump($user->image);

        // 2. Check that the database connection is working correctly
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
// -----------------------------------------

   
// ---------------------------------------------
$userId = $user->userId;
        // Insert the property data into the 'property' table
        $property_sql = "INSERT INTO property ( user_id,type, title, date, beds, bathrooms, parking, floors, floorNumber, size, price, description, address, city, area, electricity, gas, water, features) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $property_stmt = $conn->prepare($property_sql);
       
        if (!$property_stmt) {
            echo $conn->error;
            exit;
        }
       
       
        

        $property_stmt->bind_param("isssiiiisiissssiiis",$userId, $user->type, $user->title, $user->date, $user->beds, $user->bathrooms, $user->parking, $user->floors, $user->floorNumber, $user->size, $user->price, $user->description, $user->address, $user->city, $user->area, $user->electricity, $user->gas, $user->water, $user->features);

        if (!$property_stmt->execute()) {
            $response = ['status' => 0, 'message' => 'Failed to create record: ' . $property_stmt->error];
            echo json_encode($response);
            exit;
        }

      
        try {
           
            // Get the ID of the last inserted property
            $property_id = $conn->insert_id;

            var_dump($property_id);
        
            // Insert the image data into the 'property_image' table
            if (isset($user->image)) {
                foreach ($user->image as $image) {
                    // Decode the base64-encoded image data
                    $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $image));
                    var_dump($imageData);
                    // Generate a unique filename for the image
                    $filename = uniqid() . '.png';
                    var_dump($filename);
                    // Write the image data to a file on the server
                    if (!file_put_contents('uploads/' . $filename, $imageData)) {
                        throw new Exception('Failed to save image file');
                    }
        
                    // Insert the image filename into the database
                    $imageSql = "INSERT INTO image (property_id, image) VALUES (?, ?)";
                    
                    $imageStmt = $conn->prepare($imageSql);
                    $imageStmt->bind_param("is", $property_id, $filename);
                    if (!$imageStmt->execute()) {
                        throw new Exception('Failed to insert image data into database');
                    }
                }
            }
            else {
            throw new Exception('No images were provided');
        }
        
            $response = ['status' => 1, 'message' => 'Record created successfully'];
            echo json_encode($response);
        } catch (Exception $e) {
            $response = ['status' => 0, 'message' => $e->getMessage()];
            echo json_encode($response);
        }
        
               
        

        $response = ['status' => 1, 'message' => 'Record created successfully'];
        echo json_encode($response);

        break;
}
?>
