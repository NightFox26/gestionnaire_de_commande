<?php

class Manager{
    
    private $host = "localhost";
    private $dbname = "tcs26";
    private $user = "root";
    private $pass = "";
    
    public function dbConnect()
    {
        $db = new PDO('mysql:host='.$this->host.';dbname='.$this->dbname.';charset=utf8', $this->user, $this->pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    }
}