<?php
require __DIR__.'/CONFI/connection.php';

header("Access-Control-Allow-Origin: http://localhost:3000");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header("Access-Control-Allow-Headers: access");
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-with");

class Property {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function addProperty($userId, $propertyData) {
        $stmt = $this->conn->prepare("INSERT INTO property (user_id, type, title, date, beds, bathrooms, parking, floors, floorNumber, size, price, description, image, address, city, area, electricity, gas, water) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param(
            "isssiiiiiiisssssii",
            $userId,
            $propertyData->type,
            $propertyData->title,
            $propertyData->date,
            $propertyData->beds,
            $propertyData->bathrooms,
            $propertyData->parking,
            $propertyData->floors,
            $propertyData->floorNumber,
            $propertyData->size,
            $propertyData->price,
            $propertyData->description,
            $propertyData->image,
            $propertyData->address,
            $propertyData->city,
            $propertyData->area,
            $propertyData->electricity,
            $propertyData->gas,
            $propertyData->water
        );

        if ($stmt->execute()) {
            echo json_encode(['message' => 'Property added successfully']);
            exit;
        } else {
            echo json_encode(['message' => 'Failed to add property']);
            exit;
        }
    }
}

$property = new Property($conn);
$data = json_decode(file_get_contents("php://input"));

if (isset($data)) {
    $userId = $data->userId;
    $propertyData = $data->propertyData;

    $property->addProperty($userId, $propertyData);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Incomplete data']);
    exit;
}
?>
