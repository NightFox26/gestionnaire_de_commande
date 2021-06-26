<?php
session_start();
require(__DIR__."/../../include/fonctions.php");
getAutoloader();

$bddMng = new Manager();        
$bdd = $bddMng->dbConnect();
$messageMng = new MessageManager($bdd);

if(isset($_POST['message']) && !empty($_POST['message'])){
    $nom = $_SESSION["user"];
    $text = htmlspecialchars($_POST['message']);
    $date = date('d/m/Y à H:i');
    
    $idMsg = $messageMng->insertMessage($nom, $text);   
    echo json_encode(["id"=>$idMsg, "nom"=>$nom, "text"=>$text, "date"=>$date]);
}else{
    echo "Error datas in submitting users form";
}
