<?php

require_once 'BaseModel.php';

class UserModel extends BaseModel {

    public function findUserById($id) {
        $stmt = self::$_connection->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function findUser($keyword) {
        $like = "%{$keyword}%";
        $stmt = self::$_connection->prepare("SELECT * FROM users WHERE user_name LIKE ? OR user_email LIKE ?");
        $stmt->bind_param("ss", $like, $like);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Authentication user
     * @param $userName
     * @param $password
     * @return array
     */
    public function auth($userName, $password) {
        $md5Password = md5($password);
        $stmt = self::$_connection->prepare("SELECT * FROM users WHERE name = ? AND password = ?");
        $stmt->bind_param("ss", $userName, $md5Password);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Delete user by id
     * @param $id
     * @return mixed
     */
    public function deleteUserById($id) {
        $stmt = self::$_connection->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Update user
     * @param $input
     * @return mixed
     */
    public function updateUser($input) {
        $stmt = self::$_connection->prepare("UPDATE users SET name = ?, password = ? WHERE id = ?");
        $hashedPass = md5($input['password']);
        $stmt->bind_param("ssi", $input['name'], $hashedPass, $input['id']);
        return $stmt->execute();
    }

    /**
     * Insert user
     * @param $input
     * @return mixed
     */
    public function insertUser($input) {
        $stmt = self::$_connection->prepare("INSERT INTO users (name, fullname, email, password) VALUES (?, ?, ?, ?)");
        $hashedPass = md5($input['password']);
        $stmt->bind_param("ssss", $input['name'], $input['fullname'], $input['email'], $hashedPass);
        return $stmt->execute();
    }

     /**
     * Lấy danh sách user. Nếu truyền $keyword thì tìm theo user_name/fullname/email.
     * @param string|null $keyword
     * @return array
     */
    public function getUsers($keyword = null) {
        if (!empty($keyword)) {
            $like = '%' . $keyword . '%';
            $stmt = self::$_connection->prepare(
                "SELECT * FROM users 
                 WHERE user_name LIKE ? OR fullname LIKE ? OR user_email LIKE ? 
                 ORDER BY id"
            );
            $stmt->bind_param("sss", $like, $like, $like);
        } else {
            $stmt = self::$_connection->prepare("SELECT * FROM users ORDER BY id");
        }

        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

}
