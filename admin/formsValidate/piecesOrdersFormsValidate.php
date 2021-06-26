<?php
require(__DIR__."/../../include/fonctions.php");
getAutoloader();

$bddMng = new Manager();        
$bdd = $bddMng->dbConnect();
$pieceMng = new PieceManager($bdd);
$pieceOrdersMng = new PieceCommandeManager($bdd);
$OrdersMng = new CommandeManager($bdd);

if(isset($_POST['idPieceCom']) and isset($_POST['updateUrgencePlus'])){
    $idPieceOrder = htmlspecialchars($_POST['idPieceCom']);
    $urgencePlus =  htmlspecialchars($_POST['urgencePlus']);
      
    $pieceOrdersMng->updateUrgencePlusPieceCommande($idPieceOrder,$urgencePlus);
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}


if(isset($_POST['idPieceCom']) and isset($_POST['clotured'])){
    $idPieceOrder = htmlspecialchars($_POST['idPieceCom']);
    $cloture =  htmlspecialchars($_POST['clotured']);
    
    $pieceOrdersMng->updateCloturedPieceCommande($idPieceOrder,$cloture);
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}

if(isset($_POST['idPieceDel']) && $_POST['idPieceDel']>0 or
   isset($_POST['idPieceOrderDel']) && $_POST['idPieceOrderDel']>0
  ){ 
    $pass = htmlspecialchars($_POST['mdp']);
    
    if($_POST['needConfirm'] == 'false' or 
       ($_POST['needConfirm'] == 'true' and $pass == $mdpJs)){
        if(!empty($_POST['idPieceOrderDel'])){
            $idPieceCommande = htmlspecialchars($_POST['idPieceOrderDel']);
            $pieceOrdersMng->deletePieceCommande($idPieceCommande);
            echo json_encode(["piece order deleted" => $idPieceCommande]);
        }elseif(!empty($_POST['idPieceDel'])){
            $idPiece = htmlspecialchars($_POST['idPieceDel']);
            $pieceMng->deletePiece($idPiece);
            echo json_encode(["piece deleted" => $idPiece]);
        } 
    }else{
        echo json_encode(["error"=>"Mot de passe incorrect !"]);
        exit;
    }
}elseif(isset($_POST['addPiece'])){
    $idPiece = null;
    $pieceExistante = null;
    
    if(isset($_POST['piece_id'])){
        $idPiece = htmlspecialchars($_POST['piece_id']);        
    }elseif(isset($_POST['pieceExistante_id']) && $_POST['pieceExistante_id'] != "undefined"){
        $idPiece = htmlspecialchars($_POST['pieceExistante_id']);
        $pieceExistante = $pieceMng->getPiece($idPiece);
    }
    
    $urgence    = 0;
    if($pieceExistante){
        $idOrder    = htmlspecialchars($_POST['order_id']);
        $refPiece   = $pieceExistante->getRef();
        $qtPiece    = htmlspecialchars($_POST['piece_qt']);
        $ral        = $pieceExistante->getRal();
        $plan       = $pieceExistante->getPlan();
        $infos      = $pieceExistante->getInfos();
        $uniq       = "";
        if(isset($_POST['piece_urgente'])){
            $urgence    = htmlspecialchars($_POST['piece_urgente'])=="on"?1:0;            
        }
    }else{
        $idOrder    = htmlspecialchars($_POST['order_id']);
        $refPiece   = htmlspecialchars($_POST['piece_ref']);
        $qtPiece    = htmlspecialchars($_POST['piece_qt']);
        $ral        = htmlspecialchars($_POST['piece_ral']);
        $plan       = htmlspecialchars($_FILES['piece_plan']['name']);
        $infos      = htmlspecialchars($_POST['piece_infos']);
        $infosUnique= htmlspecialchars($_POST['piece_infos']);
        $uniq       = htmlspecialchars($_POST['piece_unique']);
        if(isset($_POST['piece_urgente'])){
            $urgence    = htmlspecialchars($_POST['piece_urgente'])=="on"?1:0;            
        }
    }
    
    $order = $OrdersMng->getCommande($idOrder);
    $idClient   = $order->getIdClient();
    
    verifNbRef($qtPiece,$refPiece);
    
    $newNameplan = null;
    if($uniq == "garder"){
         $idPiece = $pieceMng->insertPiece($refPiece,$ral,$plan,$infos,$idClient);
         if(!empty($plan)){
             $newNameplan = changeNamePlan($idPiece,$_FILES['piece_plan'],'garder');
             $pieceMng->updatePiece($idPiece,$refPiece,$ral,$newNameplan,$infos,$idClient);
         }
    }elseif($pieceExistante && !empty($pieceExistante->getPlan())){
        $newNameplan = $pieceExistante->getPlan();
    }
    $idPieceOrder = $pieceOrdersMng->insertPieceCommande($idOrder,$idPiece,
                                    $refPiece,$qtPiece,
                                    $ral,$infos,$newNameplan,$urgence);
    $OrdersMng->updateStatutOrder($idOrder,"en attente");
    
    if($uniq != "garder" && !$pieceExistante){
        $newNameplan = changeNamePlan($idPieceOrder,$_FILES['piece_plan'],'unique');
        $pieceOrdersMng->updatePlanPieceCommande($idPieceOrder,$newNameplan);
    }
    
    if(!$pieceExistante){
        uploadFile($_FILES['piece_plan'],$newNameplan);        
    }
    
    echo json_encode(["pieceInserted" => "piece inseré dans commande".$idOrder,
                     "idCommande"  =>$idOrder,
                     "id"       =>$idPieceOrder,
                     "idPiece"  =>$idPiece,
                     "ref"      =>$refPiece,
                     "qt"       =>$qtPiece,
                     "ral"      =>$ral,
                     "plan"     =>$newNameplan,
                     "infos"    =>$infos,                     
                     "uniq"     =>$uniq,                     
                     "urgente"  =>$urgence                    
                     ]);
    
}elseif(isset($_POST['changeStatut'])){
    $idPiece = htmlspecialchars($_POST['idPiece']);
    $statutPiece = htmlspecialchars($_POST['statutPiece']);
    $idOrder = htmlspecialchars($_POST['idOrder']);
    
    $pieceOrdersMng->updateStatutPieceCommande($idPiece,$statutPiece);
    
    $order = $OrdersMng->getCommande($idOrder);
    $statutOrder = $order->getAutoStatutByPieces();
    $OrdersMng->updateStatutOrder($idOrder,$statutOrder);
    $client = $order->getClientInfos();  
    $mailsClient = $client->extractMultipleMails();
        
    echo json_encode(["pieceUptdated"   => "piece mis a jour ".$idPiece,
                      "statut"          =>$statutPiece, 
                      "commandeUptdated"=> "commande mis a jour ".$idOrder,
                      "statutCommande"  =>$statutOrder,
                      "nomClient"       =>$client->getNom(),
                      "refClient"       =>$order->getRefClient(),
                      "mailsClient"     =>$mailsClient,
                     ]);
}elseif(isset($_POST['changeQt'])){
    if($_POST['qt']>0){
        $idPiece = htmlspecialchars($_POST['idPiece']);
        $qtPiece = htmlspecialchars($_POST['qt']);
        $idOrder = htmlspecialchars($_POST['idOrder']);

        $pieceOrdersMng->updateQantityPieceCommande($idPiece,$qtPiece);
        
        $OrdersMng->updateStatutOrder($idOrder,"en attente");
        echo json_encode(["piece_Qt_Uptdated" => "piece mis a jour ".$idPiece,
                          "New Qt"   =>$qtPiece
                         ]);        
    }else{
        echo json_encode(["error"=>"Une piece ne peut pas avoir une quantité inferieur a 1 !"]);
        exit;
    }
}elseif(isset($_POST['updatePiece'])){
    $idPiece        = htmlspecialchars($_POST['idPieceUp']);
    $idPieceOrder   = htmlspecialchars($_POST['idPieceOrderUp']);
    $ral            = htmlspecialchars($_POST['ral']);
    $infos          = htmlspecialchars($_POST['infos']);
    
    $infosUnique = "";
    if(isset($_POST['infosUnique'])){
        $infosUnique = htmlspecialchars($_POST['infosUnique']);
    }
    
    if($_POST['idPieceUp']>0){
        $piece = $pieceMng->getPiece($idPiece);
        if(!empty($_FILES['piece_plan']["name"])){
            $planName = changeNamePlan($idPiece,$_FILES['piece_plan'],"garder");
            uploadFile($_FILES['piece_plan'],$planName);
        }else{
            $planName = $piece->getPlan();
        }
        $pieceMng->updatePiece($idPiece,$piece->getRef(),$ral,$planName,$infos,$piece->getIdClient());
        $pieceOrdersMng->updatePieceCommande($idPiece,$infos,$infosUnique,$planName,$ral);
        echo json_encode(["pieceUptdated"=>"Piece mis a jour !"]);
    }elseif($_POST['idPieceOrderUp']>0){                
        $pieceOrder = $pieceOrdersMng->getPiece($idPieceOrder);
        $piece = $pieceMng->getPiece($pieceOrder->getIdPiece());        
        $infosFab = $pieceOrder->getInfosFab();
        if(!empty($_FILES['piece_plan']["name"])){
            if($piece){
                $planName = changeNamePlan($piece->getId(),$_FILES['piece_plan'],"garder");
            }else{
                $planName = changeNamePlan($idPieceOrder,$_FILES['piece_plan'],"unique"); 
            }
            uploadFile($_FILES['piece_plan'],$planName);
        }else{
            $planName = $pieceOrder->getPlan();
        }
        
        if($piece){
            $pieceMng->updatePiece($piece->getId(),$piece->getRef(),$ral,$planName,$infos,$piece->getIdClient());
        }
        $pieceOrdersMng->updatePieceCommandeById($idPieceOrder,$infos,$infosUnique,$planName,$infosFab,$ral);
        echo json_encode(["pieceOrderUptdated"=>"Piece sur commande mis a jour !"]);
    }
}else{
    echo json_encode(["error"=>"Erreur sur l'ajout ou supression de la piece sur la commande !"]);
}


function verifNbRef($qtPiece,$refPiece){
    if($qtPiece < 1){
        echo json_encode(["error"=>"Une piece ne peut pas avoir une quantité inferieur a 1 !"]);
        exit;
    }
    
    if(empty($refPiece)){
        echo json_encode(["error"=>"Une piece doit avoir une reference!"]);
        exit;
    }
}

function changeNamePlan($idPiece,$file,$planUnique){
    if(!empty($file['name'])){
        $infoFile = new SplFileInfo($file['name']);
        $extension = $infoFile->getExtension();
        if($planUnique == "garder"){
            return $idPiece.".".$extension;        
        }else{
            return "u_".$idPiece.".".$extension;
        }        
    }
}

function uploadFile($plan,$newNamePLan){
    $uploaddir = __DIR__.'/../../plans/';
    $uploadfile = $uploaddir . basename($newNamePLan);
    
    move_uploaded_file($plan['tmp_name'], $uploadfile);    
}



