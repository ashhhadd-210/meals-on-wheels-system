<?php
class database
{
    private $link;
    public $error;

    private function sendError($msg, $code)
    {
        // $_SESSION['systemError'] = $msg;
        // $_SESSION['errorCode'] = $code;
        // header("location: /systemError");
        echo $msg;
        exit();
    }

    public function __construct()
    {
        $this->connect();
    }

    private function connect()
    {
        // --- Local dev override (original prod values kept for reference) ---
        // Original: Server="<client server>", username="sa", password="<removed from repo>"
        $serverName = "localhost\\SQLEXPRESS";
        $database = "MealsOnWheels";

        try {
            // Windows authentication (trusted connection); TrustServerCertificate
            // needed because ODBC Driver 18 encrypts by default.
            $this->link = new PDO("sqlsrv:Server=$serverName;Database=$database;TrustServerCertificate=true;LoginTimeout=15");
            $this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $this->sendError('Failed to connect to database: ' . $e->getMessage(), 0);
            die();
        }
    }



    public function create($sql)
    {
        try {
            $stmt = $this->link->prepare($sql);
            $stmt->execute();
            return true;
        } catch (PDOException $err) {
            $this->error = $err->getMessage();
            return false;
        }
    }

    public function insert($sql, $params = [])
    {
        try {
            $stmt = $this->link->prepare($sql);
            $stmt->execute($params);
            return $this->link->lastInsertId();
        } catch (PDOException $err) {
            $this->error = $err->getMessage();
            return false;
        }
    }

    public function select($sql, $params = [])
    {
        try {
            $stmt = $this->link->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $err) {
            $this->error = $err->getMessage();
            return false;
        }
    }

    public function update($sql, $params = [])
    {
        try {
            $stmt = $this->link->prepare($sql);
            $stmt->execute($params);
            return true;
        } catch (PDOException $err) {
            $this->error = $err->getMessage();
            return false;
        }
    }

    public function delete($sql, $params = [])
    {
        try {
            $stmt = $this->link->prepare($sql);
            $stmt->execute($params);
            return true;
        } catch (PDOException $err) {
            $this->error = $err->getMessage();
            return false;
        }
    }
}
