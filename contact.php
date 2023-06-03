<?php
include('./classes/Database.php');


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-with");

$data = json_decode(file_get_contents("php://input"));

$user=$data->user;
$email = $data->email;
$subject = $data->subject;
$message = $data->message;

if(isset($data))
{
    $db = new Database();
    $conn = $db->dbConnection();
    
    try {
        $sql = "INSERT INTO `contactinfo`(`name`, `email`, `subject`, `message`) 
                VALUES (:name, :email, :subject, :message)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':name', $user);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':subject', $subject);
        $stmt->bindValue(':message', $message);
        
        $result = $stmt->execute();
        
        if($result){
            $response['data'] = array(
                'status' => 'valid'
            );
            echo json_encode($response);
        }else{
            $response['data'] = array(
                'status' => 'invalid'
            );
            echo json_encode($response);
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

?>