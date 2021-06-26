<?php

class CommandeManager{
    
    private $db;    
    private $notTerminate = '"terminé","livre","terminé (en peinture)","terminé (a l\'acide)", "terminé (pour peinture)"';
    private $terminate = '"terminé","livre"';
    
    public function __construct($bdd){
        $this->db = $bdd;
    }
    
    public function getCommande($id){         
        $commande = $this->db->query('SELECT * FROM commandes WHERE id ="'.$id.'" ');        
        return $commande->fetchObject('Commande');
    }
    
    public function getCommandes(){             
        $commandes = $this->db->query('SELECT * FROM commandes ORDER BY date_livr ASC');        
        $commandes = $commandes->fetchAll(PDO::FETCH_CLASS, 'Commande');
        return $commandes;        
    }
    
    public function getCommandesByClient($idClient){             
        $commandes = $this->db->prepare('SELECT * FROM commandes
                                      WHERE id_client = :id_client
                                      ORDER BY date_recept ASC'); 
        $commandes->execute(array(
               'id_client'=> $idClient,
        ));
        $commandes = $commandes->fetchAll(PDO::FETCH_CLASS, 'Commande');
        return $commandes;        
    }
    
    public function getCommandesNotTermined($orderBy=null){ 
        $order = "ORDER BY date_livr ASC";
        if($orderBy){
            $order = "ORDER BY ".$orderBy.", cl.nom ASC";
        }
        $commandes = $this->db->query('SELECT com.id, com.ref_tcs, com.ref_client, com.date_recept, com.date_livr, com.statut, com.cree_par, com.id_client FROM commandes AS com
                                       INNER JOIN client AS cl 
                                       ON com.id_client = cl.id 
                                       WHERE statut NOT IN ('.$this->notTerminate.') 
                                       '.$order.'');        
        $commandes = $commandes->fetchAll(PDO::FETCH_CLASS, 'Commande');
        return $commandes;        
    }
    
    public function getCommandesSearch($word){             
        $commandes = $this->db->prepare("SELECT * FROM commandes 
                                       WHERE ref_tcs LIKE :word
                                       OR ref_client LIKE :word
                                       ORDER BY date_livr DESC");    
        $commandes->execute(array(
                   'word'    =>'%'.$word.'%'                  
        ));
        
        $commandes = $commandes->fetchAll(PDO::FETCH_CLASS, 'Commande');
        return $commandes;        
    }
    
    public function getCommandeWithPiece($idPiece){
        $commandeMng = new CommandeManager($this->db);
        $commandes = array();
        
        $commandesPiece = $this->db->prepare('SELECT * FROM commandes_pieces
                                         WHERE id_piece = :id_piece');  
        $commandesPiece->execute(array(
                   'id_piece' => $idPiece
        ));
        
        $commandesPiece = $commandesPiece->fetchAll(PDO::FETCH_CLASS, 'PieceCommande');
        
        foreach($commandesPiece as $orderPiece){
            $commandes[] = $commandeMng->getCommande($orderPiece->getIdCommande());
        }
        return $commandes; 
    }
    
    public function getCommandeWithPieceToPaint(){
        $commandeMng = new CommandeManager($this->db);
        $commandes = array();
        
        $commandesPieceReq = $this->db->query('SELECT * FROM commandes_pieces
                                         WHERE statut = "Pour peinture"');  
                
        $commandesPiece = $commandesPieceReq->fetchAll(PDO::FETCH_CLASS, 'PieceCommande');
        
        foreach($commandesPiece as $orderPiece){
            $commandes[] = $commandeMng->getCommande($orderPiece->getIdCommande());
        }
        return $commandes; 
    }
    
    public function getCommandeWithPieceInAcid(){
        $commandeMng = new CommandeManager($this->db);
        $commandes = array();
        
        $commandesPieceReq = $this->db->query('SELECT * FROM commandes_pieces
                                         WHERE statut = "A l\'acide"');  
                
        $commandesPiece = $commandesPieceReq->fetchAll(PDO::FETCH_CLASS, 'PieceCommande');
        
        foreach($commandesPiece as $orderPiece){
            $commandes[] = $commandeMng->getCommande($orderPiece->getIdCommande());
        }
        return $commandes; 
    }
    
    public function getCommandeToLivr(){        
        $commandes = $this->db->query('SELECT com.id, com.ref_tcs, com.ref_client, com.date_recept, com.date_livr, com.statut, com.cree_par, com.id_client FROM commandes AS com
                                       INNER JOIN client AS cl 
                                       ON com.id_client = cl.id 
                                       WHERE statut = "terminé"
                                       or statut = "terminé (en peinture)"
                                       or statut = "terminé (pour peinture)"
                                       or statut = "terminé (a l\'acide)"
                                       ORDER BY cl.nom');
                
        $commandes = $commandes->fetchAll(PDO::FETCH_CLASS, 'Commande');
        return $commandes;        
    }
    
    public function getCommandeWithPieceNotClotured(){
        $commandeMng = new CommandeManager($this->db);
        $commandes = array();
        
        $commandesPieceReq = $this->db->query('SELECT * FROM commandes_pieces
                                         WHERE cloture = 0
                                         AND statut IN ('.$this->notTerminate.')
                                         ORDER BY statut ASC, id_commande ASC');  
                
        $commandesPiece = $commandesPieceReq->fetchAll(PDO::FETCH_CLASS, 'PieceCommande');
        
        foreach($commandesPiece as $orderPiece){
            $commandes[] = $commandeMng->getCommande($orderPiece->getIdCommande());
        }
        return $this->removeDoublesOrder($commandes); 
    }
    
    public function getCommandesByParams($month,$year,$filter,$paramFilter){
                        
        if($filter){
            if($filter == "statut"){
                $filterParm ="AND statut LIKE :paramFilter 
                              ORDER BY cl.nom ASC ";
                if($paramFilter == "pourPeinture"){
                    $paramFilter ='%(pour peinture)%'; 
                }else{
                    $paramFilter ='%'.$paramFilter.'%';
                }
            }elseif($filter == "dateRetard"){
                $filterParm ="AND date_livr <= :paramFilter 
                              AND statut != 'livré' 
                              AND statut != 'terminé' 
                              ORDER BY cl.nom ASC ";            
            }elseif($filter == "orderBy"){
                $filterParm ="AND com.id >= :paramFilter 
                ORDER BY ".$paramFilter.", cl.nom ASC ";
                $paramFilter = 1;
            }
            
            $commandes = $this->db->prepare('SELECT com.id, com.ref_tcs, com.ref_client, com.date_recept, com.date_livr, com.statut, com.cree_par, com.id_client FROM commandes AS com
                                         INNER JOIN client AS cl 
                                         ON com.id_client = cl.id
                                         WHERE date_livr >= :date_debut
                                         AND date_livr <= :date_fin
                                         '.$filterParm.'
                                         ');
            
            $commandes->execute(array(            
                    'date_debut'       =>$year.'-'.$month.'-01',
                    'date_fin'         =>date("Y-m-t", strtotime($year.'-'.$month.'-01')),
                    'paramFilter'      =>$paramFilter
            ));
        }else{
            $commandes = $this->db->prepare('SELECT * FROM commandes 
                                           WHERE date_livr >= :date_debut
                                           AND date_livr <= :date_fin
                                           ORDER BY date_livr ASC'); 
            $commandes->execute(array(            
                    'date_debut'       =>$year.'-'.$month.'-01',
                    'date_fin'         =>date("Y-m-t", strtotime($year.'-'.$month.'-01'))                   
            ));
        }
        $commandes = $commandes->fetchAll(PDO::FETCH_CLASS, 'Commande');
        
        return $commandes;  
    }
    
    public function getCommandesOrderByClients($month,$year){
        $commandes = $this->db->prepare('SELECT com.id, com.ref_tcs, com.ref_client, com.date_recept, com.date_livr, com.statut, com.cree_par, com.id_client FROM commandes AS com
                                         INNER JOIN client AS cl 
                                         ON com.id_client = cl.id
                                         WHERE date_livr >= :date_debut
                                         AND date_livr <= :date_fin
                                         ORDER BY cl.nom ASC'); 
        $commandes->execute(array(            
                'date_debut'       =>$year.'-'.$month.'-01',
                'date_fin'         =>date("Y-m-t", strtotime($year.'-'.$month.'-01'))                   
        ));
        
        return $commandes->fetchAll(PDO::FETCH_CLASS, 'Commande');        
    }
    
    public function getCommandesOrderByClientsNotTerminate(){
        $commandes = $this->db->query('SELECT com.id, com.ref_tcs, com.ref_client, com.date_recept, com.date_livr, com.statut, com.cree_par, com.id_client FROM commandes AS com
                                         INNER JOIN client AS cl 
                                         ON com.id_client = cl.id
                                         WHERE statut NOT IN ('.$this->notTerminate.') 
                                         ORDER BY cl.nom ASC');        
        return $commandes->fetchAll(PDO::FETCH_CLASS, 'Commande');       
    }
    
    public function getCommandesOrderByClientsTerminateThisMonth($month,$year){
        $commandes = $this->db->prepare('SELECT com.id, com.ref_tcs, com.ref_client, com.date_recept, com.date_livr, com.statut, com.cree_par, com.id_client, com.bon_livraison, com.facture, com.envoye
                                         FROM commandes AS com
                                         INNER JOIN client AS cl 
                                         ON com.id_client = cl.id
                                         WHERE statut IN ('.$this->terminate.') 
                                         AND date_livr >= :date_debut
                                         AND date_livr <= :date_fin
                                         ORDER BY cl.nom ASC');
        $commandes->execute(array(            
                'date_debut'       =>$year.'-'.$month.'-01',
                'date_fin'         =>date("Y-m-t", strtotime($year.'-'.$month.'-01'))                   
        ));
        return $commandes->fetchAll(PDO::FETCH_CLASS, 'Commande');       
    }
    
    public function getCommandeByRef_tcs($ref){         
        $commande = $this->db->query('SELECT * FROM commandes WHERE ref_tcs ="'.$ref.'" ');        
        return $commande->fetchObject('Commande');
    }
    
    public function getCommandesRefAtelier(){         
        $commande = $this->db->query('SELECT * FROM commandes 
                                      WHERE ref_tcs LIKE "atelier%" ');        
        return $commande->fetchAll(PDO::FETCH_CLASS, 'Commande');
    }
    
    public function getCommandesNotFactured(){         
        $commande = $this->db->query('SELECT * FROM commandes 
                                      WHERE facture = "0" 
                                      AND statut IN ('.$this->terminate.')');        
        return $commande->fetchAll(PDO::FETCH_CLASS, 'Commande');
    }
    
    public function getCommandesNotDelivred(){         
        $commande = $this->db->query('SELECT * FROM commandes 
                                      WHERE statut = "terminé" 
                                      OR statut = "terminé (en peinture)"');        
        return $commande->fetchAll(PDO::FETCH_CLASS, 'Commande');
    }
            
    public function insertCommande($refTcs,$refClient,
                                   $dateRecept,$dateLivr,
                                   $creePar,$idClient){         
        $commandeInsert = $this->db->prepare('INSERT INTO commandes(
                                                ref_tcs,
                                                ref_client,
                                                date_recept,
                                                date_livr,
                                                statut,
                                                cree_par,
                                                id_client)
                                            VALUES(
                                                :ref_tcs,
                                                :ref_client,
                                                :date_recept,
                                                :date_livr,
                                                :statut,
                                                :cree_par,
                                                :id_client                                            
                                            )');
        
        try{
            $commandeInsert->execute(array(            
                'ref_tcs'       =>$refTcs,
                'ref_client'    =>$refClient,
                'date_recept'   =>date("Y-m-d",strtotime(str_replace('/','-',$dateRecept))),
                'date_livr'     =>date("Y-m-d",strtotime(str_replace('/','-',$dateLivr))),
                'statut'        =>"en attente",
                'cree_par'      =>$creePar,
                'id_client'     =>$idClient
            ));  
            return $this->db->lastInsertId();
        }catch(PDOException $e){    
            $errorInfo = $e->errorInfo;
            if($errorInfo[1] == 1062) {
                echo json_encode(["error"=>"Cette reference de commande existe deja !!!"]);
                exit();
            }        
        }        
    }
    
    public function updateCommande($id,$refTcs,$refClient,
                                   $dateRecept,$dateLivr,
                                   $statut,$idClient){         
        $commandeUpdate = $this->db->prepare('UPDATE commandes SET 
                                             ref_tcs    = :ref_tcs,
                                             ref_client = :ref_client,
                                             date_recept= :date_recept,
                                             date_livr  = :date_livr,
                                             statut     = :statut, 
                                             id_client  = :id_client 
                                             WHERE id = :id');
        try{
            $commandeUpdate->execute(array(
                'id'            =>$id,            
                'ref_tcs'       =>$refTcs,
                'ref_client'    =>$refClient,
                'date_recept'   =>date("Y-m-d",strtotime(str_replace('/','-',$dateRecept))),
                'date_livr'     =>date("Y-m-d",strtotime(str_replace('/','-',$dateLivr))),
                'statut'        =>$statut,
                'id_client'     =>$idClient
            ));
        }catch(PDOException $e){    
            $errorInfo = $e->errorInfo;
            if($errorInfo[1] == 1062) {
                echo json_encode(["error"=>"Impossible d'enregistrer cette modifacation car cette reference de commande existe deja !!!"]);
                exit();
            }        
        }
    }
    
    public function updateStatutOrder($idOrder,$statut){
        $commandeUpdate = $this->db->prepare('UPDATE commandes SET 
                                             statut    = :statut  
                                             WHERE id = :id');
        
        $commandeUpdate->execute(array(
            'id'            =>$idOrder,            
            'statut'        =>$statut
        )); 
    }
    
    public function updateCommandeBlStatut($idOrder,$statut){
        $commandeUpdate = $this->db->prepare('UPDATE commandes SET 
                                             bon_livraison = :statut  
                                             WHERE id = :id');
        
        $commandeUpdate->execute(array(
            'id'            =>$idOrder,            
            'statut'        =>$statut
        )); 
    }
    
    public function updateCommandeFactureStatut($idOrder,$statut){
        $commandeUpdate = $this->db->prepare('UPDATE commandes SET 
                                             facture = :statut  
                                             WHERE id = :id');
        
        $commandeUpdate->execute(array(
            'id'            =>$idOrder,            
            'statut'        =>$statut
        )); 
    }
    
    public function updateCommandeEnvoyeStatut($idOrder,$statut){
        $commandeUpdate = $this->db->prepare('UPDATE commandes SET 
                                             envoye = :statut  
                                             WHERE id = :id');
        
        $commandeUpdate->execute(array(
            'id'            =>$idOrder,            
            'statut'        =>$statut
        )); 
    }
    
    public function updateCommandeRefAtelier($id,$refTcs){         
        $commandeUpdate = $this->db->prepare('UPDATE commandes SET 
                                             ref_tcs    = :ref_tcs  
                                             WHERE id = :id');
        
        $commandeUpdate->execute(array(
            'id'            =>$id,            
            'ref_tcs'       =>$refTcs
        ));        
    }
    
    public function deleteCommande($id){
        $commandeDel = $this->db->prepare('DELETE FROM commandes WHERE id=:id');
        
        $commandeDel->execute(array(
            'id'=>$id
        ));
    }
    
    public function removeDoublesOrder($orders){
        $ordersArray = [];
        foreach($orders as $order){
            foreach($ordersArray as $tempOrder){
                if($tempOrder->getId() == $order->getId()){
                    continue 2;
                }                
            }
            array_push($ordersArray,$order);
        }
        return $ordersArray;
    }
    
}