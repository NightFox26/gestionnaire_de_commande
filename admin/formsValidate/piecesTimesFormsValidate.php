<?php
require(__DIR__."/../../include/fonctions.php");
getAutoloader();

$bddMng = new Manager();        
$bdd = $bddMng->dbConnect();
$pieceOrdersMng = new PieceCommandeManager($bdd);
$timeMng = new PieceTempsManager($bdd);

$infoAjax = array();

if(isset($_POST['infosPiece']) and $_POST["piece_id"] > 0){
    $idPiece = htmlspecialchars($_POST['piece_id']);
    $newInfosFab = htmlspecialchars($_POST['infosPiece']);
    $piece = $pieceOrdersMng->getPiece($idPiece);
    
    preg_match("/alert/",$newInfosFab,$matches);
    if($matches){
        $newInfosFab = "<span class='textRed textAlert'>".str_replace("alert","",$newInfosFab)."</span>";
    }
    
    $infosFab = $piece->getInfosFab()."\n".$newInfosFab;
    
    $pieceOrdersMng->updatePieceCommandeById($idPiece,
                                             $piece->getInfos(),
                                             $piece->getInfosUnique(),
                                             $piece->getPlan(),
                                             $infosFab,
                                             $piece->getRal());
    
    array_push($infoAjax,["added infos fab sur piece" => $idPiece,
                "infosFab"=>$infosFab,
               ]);
}

if(isset($_POST['addTime']) and is_numeric($_POST["addTime"]) 
   and $_POST["piece_id"] > 0 and !empty($_POST["user"])){
    $time = htmlspecialchars($_POST['addTime']);
    $idPiece = htmlspecialchars($_POST['piece_id']);
    $user = htmlspecialchars($_POST['user']);
    
    $heuresSup = 0;
    if(isset($_POST['heuresSup'])){
        if(htmlspecialchars($_POST['heuresSup']) == "on"){
            $heuresSup = 1;
        }
    }
    
    $timeMng->insertPieceTemps($user,$time,$heuresSup,$idPiece);    
    array_push($infoAjax,["added time sur piece" => $idPiece,
                "user"=>$user,
                "time"=>$time,
                ]);   
}

echo json_encode($infoAjax);