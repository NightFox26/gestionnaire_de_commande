<?php
require(__DIR__."/../../include/fonctions.php");
getAutoloader();

$bddMng = new Manager();        
$bdd = $bddMng->dbConnect();
$clientMng = new ClientManager($bdd);

if(isset($_GET['idClient'])){
    $id = htmlspecialchars($_GET['idClient']);
    $client = $clientMng->getClient($id); 
    if($client){
        $clientInfos = [
            "id"        => $client->getId(),
            "nom"       => $client->getNom(),
            "mail"      => $client->getMail(),
            "tel"       => $client->getTel(),
            "adresse"   => $client->getAdresse(),
            "type"      => $client->getType()
        ];
    }  
    
    echo json_encode($clientInfos);
}