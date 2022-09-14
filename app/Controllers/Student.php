<?php

namespace Project\Controllers;

use Exception;
use PDO;

class Student
{
    public $id;
    public $name;
    public $conn;

    private $dbUserName = 'root';
    private $dbPassword = '';
    private $dbName = 'productphp';

    public function __construct()
    {
        session_start();
        try {
            $this->conn = new PDO('mysql:host=localhost;dbname=' . $this->dbName, $this->dbUserName, $this->dbPassword);
        } catch (Exception $ex) {
            echo 'Database connection failed. Error: ' . $ex->getMessage();
            die();
        }
    }

    public function index()
    {
        // select query
        $statement = $this->conn->query("SELECT * FROM students ORDER BY id desc");
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $data;
    }

    public function store(array $data)
    {
        $uploaddir = './../../assets/uploads/';
        $originalImageName = $_FILES['picture']['name'];
        $newNameInamge = date('y-m-d') . time() . $originalImageName;
        $uploadfile = $uploaddir . $newNameInamge;

        move_uploaded_file($_FILES['picture']['tmp_name'], $uploadfile);
        $file_extension = pathinfo($originalImageName, PATHINFO_EXTENSION);

        $extension_arr = array('jpg', 'jpeg', 'png', 'gif');
        if (!in_array($file_extension, $extension_arr)) {

            $_SESSION['errors']['picture'] = "only gif,png,jpg and jpeg are allowed";
        }   //is_string($a)
        elseif ($_FILES["picture"]["size"] > 5000000) {
            $_SESSION['errors']['picture'] = "file too large";
        } elseif (file_exists($uploadfile)) {
            $_SESSION['errors']['picture'] = "Sorry, file already exists.";
        }


        // $fileinfo = @getimagesize($_FILES["picture"]["tmp_name"]);
        // $width = $fileinfo[0];
        // $height = $fileinfo[1];

        // $allowed_image_extension = array(
        //     "png",
        //     "jpg",
        //     "jpeg"
        // );

        // // Get image file extension
        // $file_extension = pathinfo($_FILES["picture"]["name"], PATHINFO_EXTENSION);

        // // Validate file input to check if is not empty
        // if (! file_exists($_FILES["picture"]["tmp_name"])) {
        //     $_SESSION['errors']['image'] = "Choose image file to upload."
        //     ;
        // }    // Validate file input to check if is with valid extension
        // else if (! in_array($file_extension, $allowed_image_extension)) {
        //     $_SESSION['errors']['image'] = "Upload valid images. Only PNG and JPEG are allowed."
        //     ;
        // }    // Validate image file size
        // else if (($_FILES["picture"]["size"] > 2000000)) {
        //     $_SESSION['errors']['image'] = "Image size exceeds 2MB"
        //     ;
        // }    // Validate image file dimension
        // else if ($width > "300" || $height > "200") {
        //     $_SESSION['errors']['image'] = "Image dimension should be within 300X200"
        //     ;
        // } else {
        //     $target = "image/" . basename($_FILES["picture"]["name"]);
        //     if (move_uploaded_file($_FILES["picture"]["tmp_name"], $target)) {
        //         $_SESSION['errors']['image'] = "Problem in uploading image files.";
        //         "Image uploaded successfully."
        //         ;
        //     } else {
        //         $_SESSION['errors']['image'] = "Problem in uploading image files.";
        //     }
        // }

        // echo '<pre>';
        // if(move_uploaded_file($_FILES['picture']['tmp_name'],$uploadfile)){
        //     echo "file is valid, and was successfully uploaded .\n";
        // }else{
        //     echo "possible file upload attack!|n";
        // }
        // echo "<pre>";
        // print_r($_FILES);
        // die();
        try {
            $_SESSION['old'] = $data;

            if (empty($data['student_id'])) {
                $_SESSION['errors']['student_id'] = 'Required';
            } elseif (!is_numeric($data['student_id'])) {
                $_SESSION['errors']['student_id'] = 'Must be an integer';
            }

            if (empty($data['name'])) {
                $_SESSION['errors']['name'] = 'Required';
            }

            if (isset($_SESSION['errors'])) {
                return false;
            }

            // todo database insert
            $statement = $this->conn->prepare("INSERT INTO students (name, student_id, picture) VALUES (:s_name, :s_id, :P_picture)");

            $statement->execute([
                's_name' => $data['name'],
                's_id' => $data['student_id'],
                'P_picture' => $newNameInamge
            ]);

            unset($_SESSION['old']);
            $_SESSION['message'] = 'Successfully Created';
            return true;
        } catch (Exception $th) {
            $_SESSION['errors']['sqlError'] = $th->getMessage();
        }
    }

    public function details(int $id)
    {
        // select query
        $statement = $this->conn->query("SELECT * FROM students where id=$id");
        $data = $statement->fetch(PDO::FETCH_ASSOC);
        return $data;
    }

    public function update(array $data, int $id)
    {
        $uploaddir = './../../assets/uploads/';
        $originalImageName = $_FILES['picture']['name'];
        $newNameInamge = date('y-m-d') . time() . $originalImageName;
        $uploadfile = $uploaddir . $newNameInamge;

        move_uploaded_file($_FILES['picture']['tmp_name'], $uploadfile);
        $extension_arr = array('jpg', 'jpeg', 'png', 'gif');

        // todo database insert
        $statement = $this->conn->prepare("UPDATE students set name=:s_name, student_id=:s_id, picture=:P_picture WHERE id=:r_id");

        $statement->execute([
            'r_id' => $id,
            's_name' => $data['name'],
            's_id' => $data['student_id'],
            'P_picture' => $newNameInamge
        ]);

        $_SESSION['message'] = 'Successfully Updated';
    }

    public function destroy(int $id)
    {
        $statement = $this->conn->prepare("DELETE FROM  students where id=:s_id");
        $statement->execute([
            's_id' => $id
        ]);

        $_SESSION['message'] = 'Successfully Deleted';
    }
}
