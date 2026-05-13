<?php

class Database {

    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $db   = "car_showroom";

    public $conn;

    // ❌ لازم تكون public مش private
    public function __construct() {
        $this->conn = new mysqli(
            $this->host,
            $this->user,
            $this->pass,
            $this->db
        );

        if ($this->conn->connect_error) {
            die("Database connection failed: " . $this->conn->connect_error);
        }
    }

    public function connect() {
        return $this->conn;
    }
}