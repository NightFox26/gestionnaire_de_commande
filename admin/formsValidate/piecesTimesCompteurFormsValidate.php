<?php
require(__DIR__."/../../include/fonctions.php");
getAutoloader();

$bddMng = new Manager();        
$bdd = $bddMng->dbConnect();
$counterMng = new PieceTempsCompteurManager($bdd);
$timeMng = new PieceTempsManager($bdd);


if(isset($_POST['startCounter']) and isset($_POST["user"]) and !empty($_POST["user"])
  and isset($_POST["piece_id"]) and $_POST["piece_id"]>0){
    $user       = htmlspecialchars($_POST['user']);    
    $idPiece    = htmlspecialchars($_POST['piece_id']);
    
    $counter = $counterMng->getCompteurForPieceAndUser($idPiece,$user);
    if($counter){
        echo json_encode(["error" => "Vous avez deja un compteur de temps sur cette piece !"]);
        return;
    }
    
    $counterMng->insertPieceTempsCompteur($user,$idPiece);    
    echo json_encode(["Started counter sur piece" => $idPiece,
                      "Utilsateur"=>$user,
                     ]);
}elseif(isset($_POST['stopCounter']) and isset($_POST["idCounter"]) and $_POST["idCounter"]>0){
    $idC        = htmlspecialchars($_POST['idCounter']);
    $user       = htmlspecialchars($_POST['user']);    
    $idPiece    = htmlspecialchars($_POST['piece_id']);
    
    $counter = $counterMng->getCompteur($idC);
    
    if(trim($counter->getUser()) == trim($user)){
        $counterMng->stopPieceTempsCompteur($idC);

        $counter = $counterMng->getCompteur($idC);
        $timeWork = $counter->getWorkingTime();
        $timeMng->insertPieceTemps($user,$timeWork,0,$idPiece);    
        echo json_encode(["Stoped counter id" => $idC,
                          "temps de travail"=>$timeWork,
                          "piece"=>$idPiece
                         ]);        
    }else{
        echo json_encode(["error" => "Cet utilisateur n'a pas initialisé de compteur de temps ! \nPour etre sur de la session utilisateur, veuillez rafraichir la page !"]);
    }
}else{
    echo json_encode(["error" => "Erreur dans la validation du controleur du compteur de temps !"]);
}
