<?php
require(__DIR__."/../../include/fonctions.php");
getAutoloader();
$mailer = new Mailer();

if(isset($_POST["sendMail"]) && $_POST["sendMail"] == true
   && isset($_POST["allMailsSelected"]) 
   && isset($_POST["nomClient"])
   && isset($_POST["refClient"]) 
  ){
    
    $allMails       = array_map("htmlspecialchars", $_POST["allMailsSelected"]);
    $clientName     = htmlspecialchars($_POST["nomClient"]);
    $refOrderClient = htmlspecialchars($_POST["refClient"]);
    
    $mailSent = "";
    
    if(count($allMails)>0){
        foreach($allMails as $mail){
            $mailer->sendMail($clientName, $mail, $refOrderClient); 
            $mailSent .= $mail." / ";
        }
    }
    echo json_encode(["mailSendTo"=>$mailSent,"nom"=>$clientName,"refClient"=>$refOrderClient]);
    
}