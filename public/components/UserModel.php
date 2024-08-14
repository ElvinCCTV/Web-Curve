<?php
require_once "DBModel.php";
class User extends Model
{
    public $table = 'users';

    public function handleSignup($data)
    {

        if (!$data['fullName'] || !$data['email'] || !$data['password']) {
            echo json_encode(["success" => false, "message" => "Please provide name, email and password"]);
            return;
        }

        if ($this->where("email", "=", $data['email'], false)->get()) {
            echo json_encode(["success" => false, "message" => "Email already exists"]);
            return;
        }
        if ($this->where("fullname", "=", $data['fullName'], false)->get()) {
            echo json_encode(["success" => false, "message" => "Fullname already exists"]);
            return;
        }



        $data['password'] = md5($data['password']);
        if ($this->create($data) == 1) {
            echo json_encode(["success" => true, "message" => "Registration successful!"]);

        } else {
            echo json_encode(["success" => false, "message" => "Registration failed!"]);
        }

    }
    public function handleLogin($data)
    {
        $email = isset($data['email']) ? $data['email'] : '';
        $password = isset($data['password']) ? $data['password'] : '';
        $encrypt_passw = md5($password);

        $user = $this->where("email", "=", $email, false)->get();

        if (!$user) {
            echo json_encode(["success" => false, "message" => "User not found"]);
            return;
        }
        $user = $user[0];

        if ($user && isset($user['password']) && $user['password'] === $encrypt_passw) {
            echo json_encode(["success" => true, "user_id" => $user['id']]);
            return;
        } else {
            echo json_encode(["success" => false, "message" => "Incorrect username or password"]);
            return;
        }
    }

    public function fetchUsers($data){
        $showAll = $data["showAll"];
        $usersAmount = $data["usersAmaunt"];
        $offset = $data["offset"];

        $rowsCount = $this->totalRows();
        $pages = $rowsCount[0]["COUNT(*)"] / $usersAmount;

        if($showAll){
            $users = $this->get();
            echo json_encode(["pages" => ceil($pages), "users" => $users]);
        } elseif (isset($usersAmount)) {


            $users = $this->paginate($usersAmount, ($usersAmount * $offset));
            echo json_encode(["pages" => ceil($pages), "users" => $users]);


        }else{

            echo json_encode(["pages" => ceil($pages)]);

        }
    }

    public function updateEmail($data){
        $id = $data["id"];
        $email = $data["newEmail"];

        if ($this->where("email", "=", $email, false)->where("id", "!=", $id, false)->get()) {
            echo json_encode(["success" => false, "message" => "Email already exists"]);
            return;
        }
        // $this->where("","","", true);
        $newEmail['email'] = $email;

        if ($this->where("id", "=", $id, true)->update($newEmail) == 1) {
            echo json_encode(["success" => true, "message" => "Email updated successfully!"]);

        } else {
            echo json_encode(["success" => false, "message" => "Email update failed!"]);
        }
    }

}


