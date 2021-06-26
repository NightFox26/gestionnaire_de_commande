<?php
require(__DIR__."/../../include/fonctions.php");
getAutoloader();

$bddMng = new Manager();        
$bdd = $bddMng->dbConnect();
$pieceMng = new PieceManager($bdd);
$pieceOrderMng = new PieceCommandeManager($bdd);

if(isset($_GET['ref']) && isset($_GET['idClient'])){
    $ref = htmlspecialchars($_GET['ref']);
    $idClient = htmlspecialchars($_GET['idClient']);
    $pieces = $pieceMng->getPieceByRefAndClient($ref,$idClient); 
    
    $piecesInfos = array();    
    if($pieces){
        foreach($pieces as $piece){
            $piecesInfos[] = [
                "id"            => $piece->getId(),
                "ref"           => $piece->getRef(),
                "ral"           => $piece->getRal(),
                "plan"          => $piece->getPlan(),
                "id_client"     => $piece->getIdClient()            
            ];            
        }
        echo json_encode($piecesInfos);
    }else{
        echo json_encode(['error'=>"aucune piece ne porte cette reference pour ce client!"]);
    }    
}elseif(isset($_GET['idPiece'])){   
    $idPiece = htmlspecialchars($_GET['idPiece']);
    $piece = $pieceOrderMng->getPiece($idPiece);
    $piecesInfos = array(); 
        
    if($piece){
        echo json_encode([
            "id"            => $piece->getId(),
            "ref"           => $piece->getRef(),
            "ral"           => $piece->getRal(),
            "plan"          => $piece->getPlan(),           
            "infos_fab"     => $piece->getInfosFab(),
            "clotured"      => $piece->getIsCloture()
        ]);        
    }    
}
