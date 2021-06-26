<?php
session_start();
require(__DIR__."/../../include/fonctions.php");
getAutoloader();

$bddMng = new Manager();        
$bdd = $bddMng->dbConnect();
$clientMng = new ClientManager($bdd);

if(isset($_POST['idClient'])){
    $id         = htmlspecialchars($_POST['idClient']);
    $nom        = htmlspecialchars($_POST['nom']);
    $mail       = htmlspecialchars($_POST['mails']);
    $tel        = htmlspecialchars($_POST['tels']);
    $adresse    = htmlspecialchars($_POST['adresse']);
    $type       = htmlspecialchars($_POST['type']);
    
    $clientMng->updateClient($id,$nom,$mail,$tel,$adresse,$type);   
    echo json_encode("Modification du client effectuée !");
}else{
    echo "Error datas in submitting edit client form";
}
