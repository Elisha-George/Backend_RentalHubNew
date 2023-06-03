<?php
require __DIR__.'/CONFI/connection.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-with");

class User {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function registerUser($cnic, $name, $email, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM demo_user WHERE cnic = ? OR email = ?");
        $stmt->bind_param("ss", $cnic, $email);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            echo json_encode(['message' => 'Email Or CNIC Already Registered']);
            exit; // Terminate the script execution after sending the response
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $insert_query = "INSERT INTO demo_user (cnic, name, email, password) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($insert_query);
            $stmt->bind_param("ssss", $cnic, $name, $email, $hashed_password);
            $result = $stmt->execute();

            if ($result) {
                $response['data'] = array(
                    'status' => 'valid'
                );
                echo json_encode($response);
                exit; // Terminate the script execution after sending the response
            } else {
                $response['data'] = array(
                    'status' => 'invalid'
                );
                echo json_encode($response);
                exit; // Terminate the script execution after sending the response
            }
        }
    }
}

$user = new User($conn);
$data = json_decode(file_get_contents("php://input"));

if (isset($data)) {
    $id = $data->cnic;
    $email = $data->email;
    $name = $data->username;
    $password = $data->password;

    $user->registerUser($id, $name, $email, $password);
}
else {
    echo json_encode(['status' => 'error', 'message' => 'Incomplete registration data']);
    exit; // Terminate the script execution after sending the response
}
?>
