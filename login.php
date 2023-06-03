<?php
require __DIR__.'/CONFI/connection.php';

header("Access-Control-Allow-Origin: http://localhost:3000");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header("Access-Control-Allow-Headers: access");
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-with");

class User {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function loginUser($email, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM demo_user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $hashed_password = $row['password'];
            if (password_verify($password, $hashed_password)) {
                // Authentication successful
                $token = generateToken($email);
                $this->storeToken($email, $token);
                $response['status'] = 'valid';
                $response['token'] = $token;
                echo json_encode($response);
                exit;
            }
        }

        // Authentication failed
        echo json_encode(['message' => 'Invalid credentials']);
        exit;
    }

    private function storeToken($email, $token) {
        // Store the token in the session or any other storage mechanism of your choice
        // Here, we're storing it in the session
        $stmt = $this->conn->prepare("UPDATE demo_user SET token = ? WHERE email = ?");
        $stmt->bind_param("ss", $token, $email);
        $stmt->execute();
        session_start();
        $_SESSION['token'] = $token;
    }
}

function generateToken($email) {
    // Generate a unique token using email and current timestamp
    $token = md5($email . time());
    return $token;
}

$user = new User($conn);
$data = json_decode(file_get_contents("php://input"));

if (isset($data)) {
    $email = $data->email;
    $password = $data->password;

    $user->loginUser($email, $password);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Incomplete login data']);
    exit;
}
?>
