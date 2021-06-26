<?php
require(__DIR__."/fonctions.php");
getAutoloader();
require(__DIR__."/session.php");
verifIpAccesIntranet();
?>

<!DOCTYPE html>
<html lang="fr" data-base='<?= $root;?>'>

<head>
    <meta charset="UTF-8">
    <title>TCS Gestionnaire de commande</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="<?= $root.'/image/logo.png';?>" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <link rel="stylesheet" href='<?= $root.'styles.css';?>'>        
</head>

<?php      
    
/* chargement des managers */   
$userMng = new UserManager($bdd);
$taskMng = new TaskManager($bdd);
$pieceMng = new PieceManager($bdd);
$messageMng = new MessageManager($bdd);
$commandeMng = new CommandeManager($bdd);
$tempsMng = new PieceTempsManager($bdd);
$pieceCommandeMng = new PieceCommandeManager($bdd);
$counterMng = new PieceTempsCompteurManager($bdd);
$clientMng = new ClientManager($bdd);
$footerMng = new FooterManager($bdd);

  