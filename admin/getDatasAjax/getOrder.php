<?php
require(__DIR__."/../../include/fonctions.php");
getAutoloader();

$bddMng = new Manager();        
$bdd = $bddMng->dbConnect();
$orderMng = new CommandeManager($bdd);
$clientMng = new ClientManager($bdd);
$tempsMng = new PieceTempsManager($bdd);
$piecesMng = new PieceManager($bdd);
$piecesCommandeMng = new PieceCommandeManager($bdd);

if(isset($_GET['ref_tcs'])){
    $ref = htmlspecialchars($_GET['ref_tcs']);
    
    $order = $orderMng->getCommandeByRef_tcs($ref); 
    if($order){
        $client = $clientMng->getClient($order->getIdClient()); 
        
        $piecesClient = $piecesMng->getPiecesByClient($client->getId());
        $piecesClientArr = array();
        foreach($piecesClient as $pieceClient){
            $piecesClientArr[] = ["id"=>$pieceClient->getId(),
                                  "ref"=>$pieceClient->getRef(),
                                 ];
        }
        
        $orderInfos = [
            "infos_commande"=>[
                            "id"            => $order->getId(),
                            "ref_tcs"       => $order->getRefTcs(),
                            "ref_client"    => $order->getRefClient(),
                            "date_recept"   => $order->getDateRecept(),
                            "statut"        => $order->getStatut(),                
                            "date_livr"     => $order->getDateLivr()                
                            ],
            "infos_client"  => [
                            "id"        => $client->getId(),
                            "nom"       => $client->getNom(),
                            "mail"      => $client->getMail(),
                            "tel"       => $client->getTel(),
                            "adresse"   => $client->getAdresse(),
                            "type"      => $client->getType()
                            ],
            "pieces"        => getPiecesCommande($order->getPieces(),
                                                 $tempsMng),
            "piecesClient"  => $piecesClientArr
        ];
        echo json_encode($orderInfos);
    }else{
        echo json_encode(["no_order"=>"Commnde : Reference TCS introuvable !"]); 
    }
}elseif(isset($_GET['facturedOrderCheck']) and isset($_GET['pieceId'])){
    $idPiece = htmlspecialchars($_GET['pieceId']);
    $pieceOrder = $piecesCommandeMng->getPiece($idPiece);
    $order = $pieceOrder->getCommande();
    if($order){
        echo json_encode(["factured"    =>$order->getIsFacture(),
                          "refCommande" =>$order->getRefTcs(),
                         ]);
    } 
}elseif(isset($_GET['facturedOrderCheck']) and isset($_GET['idOrder'])){
    $idOrder = htmlspecialchars($_GET['idOrder']);    
    $order = $orderMng->getCommande($idOrder);
    if($order){
        echo json_encode(["factured"    =>$order->getIsFacture(),
                          "refCommande" =>$order->getRefTcs(),
                         ]);
    } 
}else{
    echo json_encode("La reference de commande TCS n'est pas renseigné!");
}


function getPiecesCommande($piecesOrder,$tempsMng){
    $pieces = array();
    foreach($piecesOrder as $piece){
        $timesForPiece = "";
        if($tempsMng->getTemps($piece->getId())){
            $timesForPiece = " times";
        }        
        
        $pieces[]=array(
            "id"            =>$piece->getId(),
            "idCommande"    =>$piece->getIdCommande(),
            "idPiece"       =>$piece->getIdPiece(),
            "ref"           =>$piece->getRef(),
            "qt"            =>$piece->getQt(),
            "infos"         =>$piece->getInfos(),
            "ral"           =>$piece->getRal(),
            "plan"          =>$piece->getPlan(),
            "urgente"       =>$piece->getUrgente(),
            "temps"         =>$timesForPiece
        );
    }
    return $pieces;
}