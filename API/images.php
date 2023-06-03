<?php
include('../CONFI/connection.php');

header("Access-Control-Allow-Origin: http://localhost:3000");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header("Access-Control-Allow-Headers: access");
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-with");

// Retrieve the property ID from the URL parameter
if (isset($_GET['property_id']) && is_numeric($_GET['property_id'])) {
    $propertyId = $_GET['property_id'];

    try {
      $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Query the database to fetch the house details with the given ID
        $stmt = $pdo->prepare('SELECT *
          FROM image WHERE i.property_id = :property_id');
        $stmt->bindParam(':property_id', $propertyId);
        $stmt->execute();
        $houses = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode($houses);
exit();

        if ($houses && isset($houses['property_id'])) {
            echo json_encode($houses);
        } else {
            echo json_encode(array('error' => 'House not found'));
        }
    } catch(PDOException $e) {
        echo json_encode(array('error' => 'Error connecting to database: ' . $e->getMessage()));
    }
} else {
    echo json_encode(array('error' => 'Invalid ID'));
}



    ?>