<?php 
    session_start();    
    
    if(!empty($_SESSION["user"])){
        $bddMng = new Manager();        
        $bdd = $bddMng->dbConnect();        
    }else{         
        header("location:".$root."login.php");
    }   