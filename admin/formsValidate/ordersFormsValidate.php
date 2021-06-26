<?php
require(__DIR__."/../../include/fonctions.php");
getAutoloader();

$bddMng = new Manager();        
$bdd = $bddMng->dbConnect();
$ordersMng = new CommandeManager($bdd);
$pieceOrdersMng = new PieceCommandeManager($bdd);
$clientMng = new ClientManager($bdd);
$piecesMng = new PieceManager($bdd);

// suppression de la commande
if(isset($_POST['idOrderDelete']) and $_POST['idOrderDelete']>0 ){
    $pass = htmlspecialchars($_POST['mdp']);
    $id = htmlspecialchars($_POST['idOrderDelete']);
    verifIfOrderFactured($ordersMng,$id);
        
    if($_POST['needConfirm'] == 'false' or 
       ($_POST['needConfirm'] == 'true' and $pass == $mdpJs)){
        
        $pieceOrdersMng->deleteAllPiecesOfOrder($id);
        $ordersMng->deleteCommande($id);
        echo json_encode(["ok"=>"Commande supprimée !"]);
        exit;
    }else{
        echo json_encode(["error"=>"Mot de passe incorrect !"]);
        exit;
    }
}

//changement de statut sur les "Bl"
if(isset($_POST['checkBlFact']) and isset($_POST['blChecked']) and isset($_POST['idOrder'])){
    $idOrder = htmlspecialchars($_POST['idOrder']);
    $statutBox = $_POST['blChecked'] == 'true'? 1:0;
    $ordersMng->updateCommandeBlStatut($idOrder,$statutBox);
    echo json_encode("statut Bl changé !");
    exit;
}

//changement de statut sur les "factures"
if(isset($_POST['checkBlFact']) and isset($_POST['factureChecked']) and isset($_POST['idOrder'])){
    $idOrder = htmlspecialchars($_POST['idOrder']);
    $statutBox = $_POST['factureChecked'] == "true"? 1:0;
    $ordersMng->updateCommandeFactureStatut($idOrder,$statutBox);
    echo json_encode("statut facture changé !");
    exit;
}

//changement de statut sur "envoye"
if(isset($_POST['checkBlFact']) and isset($_POST['envoyeChecked']) and isset($_POST['idOrder'])){
    $idOrder = htmlspecialchars($_POST['idOrder']);
    $statutBox = $_POST['envoyeChecked'] == "true"? 1:0;
    $ordersMng->updateCommandeEnvoyeStatut($idOrder,$statutBox);
    echo json_encode("statut envoye changé !");
    exit;
}

//cloture de toutes les pieces de la commande
if(isset($_POST['idOrder']) and isset($_POST["clotureAllPieces"])){
    $idOrder = htmlspecialchars($_POST['idOrder']);
    $cloture = htmlspecialchars($_POST['clotureAllPieces']);
    $pieceOrdersMng->updateClotureAllPiecesCommande($idOrder,$cloture);
    echo json_encode("toutes les pieces ont été cloturé !");
    exit;
}

// Creation de la commande
if((isset($_POST['ref_tcs']) and !empty($_POST['ref_tcs']) and $_POST['cree_par'] == "bureau") or 
  (isset($_POST['ref_client']) and !empty($_POST['ref_client']) and $_POST['cree_par'] == "atelier")
  ){    
    verifClientChoice($_POST);
    
    $id             = htmlspecialchars($_POST['id']);
    $statut         = htmlspecialchars($_POST['statut']);
    $refTcs         = htmlspecialchars($_POST['ref_tcs']);
    $refClient      = htmlspecialchars($_POST['ref_client']);
    $creePar        = htmlspecialchars($_POST['cree_par']);
    
    $client = [
        "idPro"     => htmlspecialchars($_POST['id_client_pro']),
        "idPart"    => htmlspecialchars($_POST['id_client_part']),        
        "nomPro"    => trim(htmlspecialchars($_POST['client_nom_pro'])),
        "nomPart"   => trim(htmlspecialchars($_POST['client_nom_part'])),
        "mail"      => htmlspecialchars($_POST['client_mail']),
        "tel"       => htmlspecialchars($_POST['client_tel']),
        "adresse"   => htmlspecialchars($_POST['client_adresse'])
    ];
    
    $typeClient = !empty($client['nomPro'])?"pro":"particulier";
    
    $dateRecept = date('d/m/Y',strtotime($_POST['date_recept']));
    if(empty($_POST["date_recept"])){
        $dateRecept = date('d/m/Y');        
    }
    
    $dateLivr = date('d/m/Y',strtotime($_POST['date_livr']));  
    if(empty($_POST["date_livr"])){
        $dateLivr = date('d/m/Y');
    }
    
    checkDateValidation($dateRecept,$dateLivr);
    
    
    $idClient = createUpdateClientBdd($clientMng,$client);
    
    if($id){
        verifIfOrderFactured($ordersMng,$id);        
    }
    
    verifUpdateCommande($ordersMng,$clientMng,
                        $piecesMng,$id,
                        $refTcs,$refClient,
                        $dateRecept,$dateLivr,
                        $statut,$idClient);    
        
    $idOrder = $ordersMng->insertCommande($refTcs,$refClient,
                               $dateRecept,$dateLivr,
                               $creePar,$idClient);
    
    $piecesClient = $piecesMng->getPiecesByClient($idClient);
    $piecesClientArr = array();
    foreach($piecesClient as $pieceClient){
        $piecesClientArr[] = ["id"=>$pieceClient->getId(),
                              "ref"=>$pieceClient->getRef(),
                             ];
    }
    
    if($_POST['cree_par'] == 'atelier'){
        $refTcs = "atelier-".$idOrder;
        $ordersMng->updateCommandeRefAtelier($idOrder,$refTcs);
    }
    
    echo json_encode(["inserted" => $idOrder,
                      "id"=>$idOrder,
                      "refTcs"=>$refTcs, 
                      "refClient"=>$refClient,
                      "dateRecept"=>$dateRecept, 
                      "dateLivr"=>$dateLivr,
                      "idClientPro"=>$client["idPro"], 
                      "nomClientPro"=>$client["nomPro"], 
                      "idClientPart"=>$client["idPart"],
                      "nomClientPart"=>$client["nomPart"],
                      "typeClient"=>$typeClient,
                      "clientId"=>$idClient,
                      "piecesClient"=>$piecesClientArr  
                    ]);
}else{
    echo json_encode(["error"=>"Une nouvelle commande doit avoir OBLIGATOIREMENT une reference TCS ou une reference Client si créé par l'atelier!"]);
}

function checkDateValidation($dateRecept, $dateLivr){ 
    $date1 = str_replace("/","-",$dateRecept);
    $date2 = str_replace("/","-",$dateLivr);
    
    $dateLivr = DateTime::createFromFormat('d-m-Y', $date2);
    $dateRecept = DateTime::createFromFormat('d-m-Y', $date1);    
    
    if($dateRecept>$dateLivr){
        echo json_encode(["error"=>"Une commande ne peut pas avoir une date de livraison inferieur a la date de réception !"]);
        exit;
    }
}

function verifUpdateCommande($ordersMng,$clientMng,
                             $piecesMng,$id,
                             $refTcs,$refClient,
                             $dateRecept,$dateLivr,
                             $statut,$idClient){
    $order = $ordersMng->getCommande($id);
    $client = $clientMng->getClient($idClient);
    if($order){ 
        if(empty($refTcs)){
           $refTcs = $order->getRefTcs();
        }
        $ordersMng->updateCommande($id,$refTcs,
                                   $refClient,$dateRecept,
                                   $dateLivr,$statut,$idClient);        
        
        $piecesClient = $piecesMng->getPiecesByClient($idClient);
        $piecesClientArr = array();
        foreach($piecesClient as $pieceClient){
            $piecesClientArr[] = ["id"=>$pieceClient->getId(),
                                  "ref"=>$pieceClient->getRef(),
                                 ];
        }
                       
        echo json_encode(["updated"=>$id,
                          "id"=>$id,
                          "refTcs"=>$refTcs,
                          "refClient"=>$refClient,
                          "dateRecept"=>$dateRecept,
                          "dateLivr"=>$dateLivr,
                          "statut"=>$statut,
                          "typeClient"=>$client->getType(),
                          "nomClient"=>$client->getNom(),
                          "idclient"=>$idClient,
                          "piecesClient"=>$piecesClientArr
                         ]);
        exit;
    }
}


function verifClientChoice($dataOrder){
    if($dataOrder["id_client_pro"] == "null" && $dataOrder["id_client_part"] == "null"){
        echo json_encode(["error"=>"Une commande ne peut pas avoir aucun client !"]);
        exit;
    }
    
    if($dataOrder["id_client_pro"] == "newClientPro" && $dataOrder["id_client_part"] == "newClientPart"){
        echo json_encode(["error"=>"Une commande ne peut pas appartenir un nouveau client pro et particulier a la fois !"]);
        exit;
    }
    
    if(($dataOrder["id_client_pro"] == "newClientPro" && $dataOrder["id_client_part"] > 0) or 
       ($dataOrder["id_client_pro"] > 0 && $dataOrder["id_client_part"] == "newClientPart") or 
       ($dataOrder["id_client_pro"] > 0 && $dataOrder["id_client_part"] > 0)){
        echo json_encode(["error"=>"Une commande ne peut pas appartenir a 2 clients a la fois !"]);
        exit;
    }
}

function createUpdateClientBdd($clientMng, $client){    
    if($client["nomPro"] and $client["idPro"] == "undefined"){
        return $clientMng->insertClient($client["nomPro"],$client["mail"],$client["tel"],$client["adresse"],"pro");
    }elseif($client["nomPart"] and $client["idPart"] == "undefined"){
        return $clientMng->insertClient($client["nomPart"],$client["mail"],$client["tel"],$client["adresse"],"particulier");
    }elseif($client["idPro"] > 0){
        $clientMng->updateClient($client["idPro"],$client["nomPro"],
                                 $client["mail"],$client["tel"],
                                 $client["adresse"],"pro");
        return $client["idPro"];
    }elseif($client["idPart"] > 0){
        $clientMng->updateClient($client["idPart"],$client["nomPart"],
                                        $client["mail"],$client["tel"],
                                        $client["adresse"],"particulier");
        return $client["idPart"];
    }
}

function verifIfOrderFactured($ordersMng, $idOrder){
    $order = $ordersMng->getCommande($idOrder);
    if($order->getIsFacture() == 1){
        echo json_encode(["modalAlert"=>"Commande deja facturé"]);
        exit;
    }
}




