<?php
require(__DIR__."/../../include/fonctions.php");
getAutoloader();

$bddMng = new Manager();        
$bdd = $bddMng->dbConnect();
$footerMng = new FooterManager($bdd);

if(isset($_POST['text']) && isset($_POST['mode'])){
    $text = htmlspecialchars($_POST['text']);
    $mode = htmlspecialchars($_POST['mode']);
    
    $footerMng->updateFooter($text,$mode);    
    echo json_encode([$text,$mode]);
}else{
    echo "Error datas in submitting footer form";
}
