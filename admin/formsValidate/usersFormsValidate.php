<?php
session_start();
require(__DIR__."/../../include/fonctions.php");
getAutoloader();

$bddMng = new Manager();        
$bdd = $bddMng->dbConnect();
$userMng = new UserManager($bdd);

if(isset($_POST['id']) && !isset($_POST['delete'])){    
    $id = htmlspecialchars($_POST['id']);
    $nom = htmlspecialchars($_POST['nom']);
    $statut = htmlspecialchars($_POST['statut']);
    
    $userMng->updateUser($id,$nom,$statut);    
    echo json_encode([$id,$nom,$statut]);
}elseif(isset($_POST['id']) && isset($_POST['delete'])){    
    $id = htmlspecialchars($_POST['id']);    
            
    $userMng->deleteUser($id);    
    echo json_encode([$id]);
}elseif(isset($_POST['ajout'])){    
    $nom = htmlspecialchars($_POST['nom']);    
    $statut = htmlspecialchars($_POST['statut']);    
            
    $id = $userMng->insertUser($nom,$statut);       
    
    echo json_encode(["id"=>$id,"nom"=>$nom,"statut"=>$statut]);
}elseif(isset($_GET['idUser'])){
    $idUser = htmlspecialchars($_GET['idUser']);
    $user = $userMng->getUser($idUser);
    
    $_SESSION["user"] =  trim(htmlspecialchars($user->getNom()));          
    $_SESSION["idUser"] = htmlspecialchars($idUser); 
    $_SESSION["statutUser"] = trim(htmlspecialchars($user->getStatut())); 
    
    echo json_encode(["id"=>$idUser,"nom"=>$user->getNom()]);
}else{
    echo "Error datas in submitting users form";
}
