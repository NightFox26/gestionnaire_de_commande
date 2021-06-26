<?php
require(__DIR__."/../../include/fonctions.php");
getAutoloader();

$bddMng = new Manager();        
$bdd = $bddMng->dbConnect();
$messageMng = new MessageManager($bdd);

if(isset($_GET['all'])){
    $messages = $messageMng->getMessages(); 
    if($messages){
        $mesgArray = array();
        foreach($messages as $message){ 
            $id = $message->getId();
            $nom = trim($message->getNom());
            $text = $message->getText();
            $date = date('d/m/Y à H:i',strtotime($message->getDateM()));
            
            array_push($mesgArray, ["id"=>$id, "nom"=>$nom, "text"=>$text, "date"=>$date]);
        }        
        echo json_encode($mesgArray);        
    }else{
        echo json_encode(["no_msg"=>"Aucun messages dans le tchat..."]);        
    }
}