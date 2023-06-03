 <?php
include('../CONFI/connection.php');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Origin: http://localhost:3000");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header("Access-Control-Allow-Headers: access");
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE');
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-with");


try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Error connecting to database: " . $e->getMessage());
}
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare("SELECT p.*, i.*
    FROM property p
    INNER JOIN image i ON p.id = i.property_id
    ");
    $stmt->execute();
    $houses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Send CORS headers
// header('Content-Type: application/json');
// header('Access-Control-Allow-Origin: *'); // Allow requests from any origin (not recommended for production)
// header('Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE'); // Allow specific HTTP methods
// header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With'); // Allow specific HTTP headers


    // Return the data as JSON
    echo json_encode($houses);
    exit(); // Make sure to exit to prevent any additional output
} 
    elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
     
    // Handle DELETE request to delete a property
    $propertyId = $_GET['id'];

    // Delete the property from the database
    $query = "DELETE p.*, i.*
    FROM property p
    INNER JOIN image i ON p.id = i.property_id WHERE p.id = :id";
  $statement = $pdo->prepare($query);
  $statement->bindParam(':id', $propertyId, PDO::PARAM_INT);
  $result = $statement->execute();




  if ($result) {
    // Property deleted successfully
    $response = array('status' => 'success', 'message' => 'Property deleted successfully');
    echo json_encode($response);
  } else {
    // Error deleting the property
    $response = array('status' => 'error', 'message' => 'Failed to delete property');
    echo json_encode($response);
  }
} 
else {
  // Invalid request method
  $response = array('status' => 'error', 'message' => 'Invalid request method');
  echo json_encode($response);
}



?> 

