<?php


class DB {

    public $conn;

    public function __construct() {
        $host = 'localhost';
        $dbName = 'snake_of_pi';
        $user = 'mysql';
        $pass = 'mysql';

        $this->conn = new PDO('mysql:dbname='.$dbName.';host='.$host, $user, $pass);
    }

    // TODO :: вынести пользователя в отдельный модуль

    /*User*/
    // Получение пользователя
    public function getUsers() {
        $query = 'SELECT id, name, login FROM user ORDER BY id DESC';
        return $this->conn->query($query)->fetchAll(PDO::FETCH_CLASS);
    }
    //Получить пользователя по логину
    public function getUserByLogin($login) {
        $sql = 'SELECT name, login, token FROM user WHERE login = :login ORDER BY id DESC LIMIT 1';
        $stm = $this->conn->prepare($sql);
        $stm->bindValue(':login', $login, PDO::PARAM_STR);
        $stm->execute();
        return $stm->fetchObject('stdClass');
    }
    //Получить пользоваетеля по токену
    public function getUserByToken($token) {
        $sql = 'SELECT * FROM user WHERE token = :token ORDER BY id DESC LIMIT 1';
        $stm = $this->conn->prepare($sql);
        $stm->bindValue(':token', $token, PDO::PARAM_STR);
        $stm->execute();
        return $stm->fetchObject('stdClass');
    }
    //Получить пользователя по id
    public function getUserById($id) {
        $sql = 'SELECT id, login, name FROM user WHERE id = :id ORDER BY id DESC LIMIT 1';
        $stm = $this->conn->prepare($sql);
        $stm->bindValue(':id', $id, PDO::PARAM_INT);
        $stm->execute();
        return $stm->fetchObject('stdClass');
    }
    //Получить пользователя
    public function getUser($login, $password) {
        $sql = 'SELECT * FROM user WHERE login = :login and password = :password ORDER BY id DESC LIMIT 1';
        $stm = $this->conn->prepare($sql);
        $stm->bindValue(':login', $login, PDO::PARAM_STR);
        $stm->bindValue(':password', $password, PDO::PARAM_STR);
        $stm->execute();
        return $stm->fetchObject('stdClass');
    }
    // Изменить токен пользователя
    public function updateUserToken($id, $token) {
        $sql = "UPDATE user SET token = :token WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':token', $token, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $res = $stmt->execute();
        return $res;
    }
    //Создать пользователя
    public function saveUser($options) {
        $name = $options->name;
        $login = $options->login;
        $password = $options->password;

        $sql = "INSERT INTO user (name, login, password) VALUES (:name, :login, :password)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':login', $login, PDO::PARAM_STR);
        $stmt->bindValue(':password', $password, PDO::PARAM_STR);
        $res = $stmt->execute();
        return $res;
    }
    public function createUserToken($id, $token) {
        $expiredAt = time() + (7 * 24 * 60 * 60); // Week
        $options = array(
            'user_id' => $id,
            'token' => $token,
            'expiredAt' => $expiredAt,
        );
        $res = $this->createToken($options);
        if(!$res) return false;
        $res2 = $this->getTokenByToken($token);
        if(!$res2) return false;

        $res3 = $this->updateUserToken($id, $res2->id);
        if(!$res3) return false;

        return $res3;
    }


    /*User_access_token*/
    //Создать токен пользователя
    public function createToken($options) {
        if(!$options) return false;

        $user_id = $options->user_id;
        $token = $options->token;
        $expiredAt = $options->expiredAt;

        $sql = "INSERT INTO user_access_token (user_id, token, expiredAt) VALUES (:user_id, :token, :expiredAt)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
        $stmt->bindValue(':token', $token, PDO::PARAM_STR);
        $stmt->bindValue(':expiredAt', $expiredAt, PDO::PARAM_STR);
        $res = $stmt->execute();
        return $res;
    }
    //Получить токен пользователя по token
    public function getTokenByToken($token) {
        $sql = 'SELECT * FROM user_access_token WHERE token = :token ORDER BY id DESC LIMIT 1';
        $stm = $this->conn->prepare($sql);
        $stm->bindValue(':token', $token, PDO::PARAM_STR);
        $stm->execute();
        return $stm->fetchObject('stdClass');
    }
    //Получить токен пользователя по tokenId
    public function getTokenByTokenId($token) {
        $sql = 'SELECT * FROM user_access_token WHERE id = :token ORDER BY id DESC LIMIT 1';
        $stm = $this->conn->prepare($sql);
        $stm->bindValue(':token', $token, PDO::PARAM_INT);
        $stm->execute();
        return $stm->fetchObject('stdClass');
    }
    //Получить токен пользователя по id
    public function getTokenById($id) {
        $sql = 'SELECT * FROM user_access_token WHERE id = :id ORDER BY id DESC LIMIT 1';
        $stm = $this->conn->prepare($sql);
        $stm->bindValue(':id', $id, PDO::PARAM_INT);
        $stm->execute();
        return $stm->fetchObject('stdClass');
    }



    /*Snake*/
    // Получение питона
    public function getSnakes() {
        $query = 'SELECT * FROM snake';
        return $this->conn->query($query)->fetchAll(PDO::FETCH_CLASS);
    }
    // Получить питона пользователя
    public function getUserSnakes($user_id) {
        $sql = 'SELECT * FROM snake, user WHERE user.id = :user_id AND snake.user_id=user.id ORDER BY snake.id DESC';
        $stm = $this->conn->prepare($sql);
        $stm->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stm->execute();
        return $stm->fetchAll(PDO::FETCH_CLASS);
    }
    // Получить питона по id
    public function getSnakeById($id) {
        $sql = 'SELECT * FROM snake WHERE id = :id';
        $stm = $this->conn->prepare($sql);
        $stm->bindValue(':id', $id, PDO::PARAM_INT);
        $stm->execute();
        return $stm->fetchObject('stdClass');
    }
    // Создать питона
    public function createSnake($options) {
        $user_id = $options->user_id;
        $direction = $options->direction;

        $sql = "INSERT INTO snake (user_id, direction, body) VALUES (:user_id, :direction, :body)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':direction', $direction, PDO::PARAM_STR);
        $res = $stmt->execute();
        return $res;
    }
    // Изменить направление питона
    public function updateSnakeDirection($id, $direction) {
        $sql = "UPDATE snake SET direction = :direction WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':direction', $direction, PDO::PARAM_STR);
        $res = $stmt->execute();
        return $res;
    }
    // Изменить состояние еды питона
    public function updateSnakeEating($id, $eating) {
        $sql = "UPDATE snake SET eating = :eating WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':eating', $eating, PDO::PARAM_INT);
        $res = $stmt->execute();
        return $res;
    }
    // Удалить питона
    public function deleteSnake($id) {
        $sql = "DELETE FROM snake WHERE id =  :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $res = $stmt->execute();
        return $res;
    }
    // Удалить питона пользователя
    public function deleteUserSnakes($user_id) {
        $sql = "DELETE FROM snake WHERE user_id =  :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $res = $stmt->execute();
        return $res;
    }

    /*Snake_body*/
    public function getSnakesBody() {
        $query = 'SELECT * FROM snake_body ORDER BY id DESC';
        return $this->conn->query($query)->fetchAll(PDO::FETCH_CLASS);
    }
    // Получить тело питона
    public function getSnakeBody($id) {
        $sql = 'SELECT * FROM snake_body WHERE snake_id = :id ORDER BY id DESC';
        $stm = $this->conn->prepare($sql);
        $stm->bindValue(':id', $id, PDO::PARAM_INT);
        $stm->execute();
        return $stm->fetchAll(PDO::FETCH_CLASS);
    }
    // Создать тело питона
    public function createSnakeBody($options) {
        $snake_id = $options->snake_id;
        $x = $options->x;
        $y = $options->y;

        $sql = "INSERT INTO snake_body (snake_id, x, y) VALUES (:snake_id, :x, :y)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':snake_id', $snake_id, PDO::PARAM_INT);
        $stmt->bindParam(':x', $x, PDO::PARAM_INT);
        $stmt->bindParam(':y', $y, PDO::PARAM_INT);
        $res = $stmt->execute();
        return $res;
    }
    // Удалить тело питона
    public function deleteSnakeBody($id) {
        $sql = "DELETE FROM snake_body WHERE id =  :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $res = $stmt->execute();
        return $res;
    }
    // Удалить часть тела питона
    public function deleteSnakeBodyFromSnake($id) {
        $sql = "DELETE FROM snake_body WHERE snake_id =  :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $res = $stmt->execute();
        return $res;
    }




    /*Food*/
    // Создать еду
    public function createFood($options) {
        $ftype = $options->type;
        $fvalue = $options->value;
        $x = $options->x;
        $y = $options->y;

        $sql = "INSERT INTO snake_body (ftype, fvalue, x, y) VALUES (:ftype, :fvalue, :x, :y)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':ftype', $ftype, PDO::PARAM_INT);
        $stmt->bindParam(':fvalue', $fvalue, PDO::PARAM_INT);
        $stmt->bindParam(':x', $x, PDO::PARAM_INT);
        $stmt->bindParam(':y', $y, PDO::PARAM_INT);
        $res = $stmt->execute();
        return $res;
    }
    // Получить еду
    public function getFoods() {
        $query = 'SELECT * FROM food';
        return $this->conn->query($query)->fetchAll(PDO::FETCH_CLASS);
    }
    // Удалить еду
    public function deleteFood($id) {
        $sql = "DELETE FROM food WHERE id =  :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $res = $stmt->execute();
        return $res;
    }



    /*System*/
    public function getSystem() {
        $query = 'SELECT * FROM system';
        return $this->conn->query($query)->fetchAll(PDO::FETCH_CLASS);
    }
    // Получить систему по имени
    public function getSystemByName($name) {
        $sql = 'SELECT * FROM system WHERE name = :name';
        $stm = $this->conn->prepare($sql);
        $stm->bindValue(':name', $name, PDO::PARAM_STR);
        $stm->execute();
        return $stm->fetchObject('stdClass');
    }
    // Создать систему
    public function createSystem($name, $value) {
        $stmt = $this->conn->prepare("INSERT INTO system (name, value) VALUES (:name, :value)");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':value', $value, PDO::PARAM_STR);
        $res = $stmt->execute();
        return $res;
    }

    /*Map*/
    // Получение карты
    public function getMaps() {
        $query = 'SELECT * FROM map';
        return $this->conn->query($query)->fetchAll(PDO::FETCH_CLASS);
    }
    // Получить карту по id
    public function getMapById($id) {
        $sql = 'SELECT * FROM map WHERE id = :id';
        $stm = $this->conn->prepare($sql);
        $stm->bindValue(':id', $id, PDO::PARAM_INT);
        $stm->execute();
        return $stm->fetchObject('stdClass');
    }
    // Создать карту
    public function createMap($options) {
        $width = $options->width;
        $height = $options->height;

        $sql = "INSERT INTO map (width, height) VALUES (:width, :height)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':width', $width, PDO::PARAM_INT);
        $stmt->bindValue(':height', $height, PDO::PARAM_INT);
        $res = $stmt->execute();
        return $res;
    }
    // Изменить последнего времени обновления
    public function updateMapLastUpdated($id, $last_updated) {
        $sql = "UPDATE map SET last_updated = :last_updated WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':last_updated', $last_updated, PDO::PARAM_INT);
        $res = $stmt->execute();
        return $res;
    }



}