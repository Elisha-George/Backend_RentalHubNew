<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/CONFI/JwtHandler.php';

class Auth extends JwtHandler {
    
    protected $db;
    protected $headers;
    protected $token;

    public function __construct($db, $headers) {
        parent::__construct();
        $this->db = $db;
        $this->headers = $headers;
    }

    public function isValid() {
        // Check if the database connection is valid
        if ($this->db instanceof PDO && $this->db->getAttribute(PDO::ATTR_CONNECTION_STATUS)) {
            if (array_key_exists('Authorization', $this->headers) && preg_match('/Bearer\s(\S+)/', $this->headers['Authorization'], $matches)) {
                $data = $this->jwtDecodeData($matches[1]);
    
                var_dump($data); // Debug: Check the value of $data
    
                if (isset($data['data']->user_id) && $user = $this->fetchUser($data['data']->user_id)) {
                    return [
                        "success" => 1,
                        "user" => $user
                    ];
                } else {
                    $message = isset($data['message']) ? $data['message'] : "Unknown error occurred.";
                    return [
                        "success" => 0,
                        "message" => $message,
                    ];
                }
            } else {
                return [
                    "success" => 0,
                    "message" => "Token not found in request"
                ];
            }
        } else {
            return [
                "success" => 0,
                "message" => "Database connection not established"
            ];
        }
    }
    


    public function fetchUser($user_id) {
        try {
            $fetch_user_by_id = "SELECT `id`,`name`, `email` FROM `demo_user` WHERE `id`=:id";
            $query_stmt = $this->db->prepare($fetch_user_by_id);
            
            if ($query_stmt === false) {
                throw new Exception("Error preparing SQL statement.");
            }
    
            $query_stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
            $query_stmt->execute();
    
            if ($query_stmt->rowCount()) {
                return $query_stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                return false;
            }
        } catch (PDOException $e) {
            // Handle the exception (e.g., log the error, display an error message)
            echo "Error executing SQL statement: " . $e->getMessage();
            return null;
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    }
}

?>