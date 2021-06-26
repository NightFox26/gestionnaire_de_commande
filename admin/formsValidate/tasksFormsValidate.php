<?php
session_start();
require(__DIR__."/../../include/fonctions.php");
getAutoloader();

$bddMng = new Manager();        
$bdd = $bddMng->dbConnect();
$taskMng = new TaskManager($bdd);

if(isset($_POST['for_user']) && isset($_POST['tache']) && !empty($_POST['tache'])){    
    $from = $_SESSION['user'];
    $for = htmlspecialchars($_POST['for_user']);
    $tache = htmlspecialchars($_POST['tache']);
        
    $taskMng->insertTask($from,$for,$tache);    
    echo json_encode(["from"=>$from,"for"=>$for,"tache"=>$tache]);
}elseif(isset($_POST["statut"]) && isset($_POST["id"]) && !empty($_POST["id"])){
    $id = htmlspecialchars($_POST["id"]);
    $statut = htmlspecialchars($_POST["statut"]);
    
    if($statut == "done" or $statut == "validate"){
        $taskMng->updateTask($id,$statut);        
    }elseif($statut == "delete"){
        $taskMng->deleteTask($id);        
    }
    echo json_encode(["id"=>$id,"statut"=>$statut]);
}else{
    echo "Error datas in submitting task form";
}
