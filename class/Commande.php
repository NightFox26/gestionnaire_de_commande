<?php

class Commande {
    
    private $id;
    private $ref_tcs;
    private $ref_client;
    private $date_recept;
    private $date_livr;
    private $statut;
    private $cree_par;
    private $id_client;
    private $pieces;
    private $bon_livraison;
    private $facture;
    private $envoye;
    
        
    public function getId(){
        return $this->id;
    }
    
    public function getRefTcs(){
        return $this->ref_tcs;
    }
    
    public function getRefClient(){
        return $this->ref_client;
    }
    
    public function getDateRecept(){
        return $this->date_recept;
    }  
    
    public function getDateLivr(){
        return $this->date_livr;
    }
    
    public function getStatut(){
        return $this->statut;
    } 
        
    public function getCreePar(){
        return $this->cree_par;
    } 
    
    public function getIdClient(){
        return $this->id_client;
    }
    
    public function getIsBonLivraion(){
        return $this->bon_livraison;
    }
    
    public function getIsFacture(){
        return $this->facture;
    }
    
    public function getIsEnvoye(){
        return $this->envoye;
    }    
        
    public function getClientInfos(){
        $bddMng = new Manager();        
        $bdd = $bddMng->dbConnect();
        $clientMng = new ClientManager($bdd);
        return $clientMng->getClient($this->id_client);
    } 
    
    public function getPieces(){       
        $bddMng = new Manager();        
        $bdd = $bddMng->dbConnect();
        $piecesOrderMng = new PieceCommandeManager($bdd);
        return $piecesOrderMng->getPiecesCommandes($this->id);        
    }
    
    public function verifPieceUrgenteDansCommande(){
        $pieces = $this->getPieces();        
        foreach($pieces as $piece){
            if($piece->getUrgente() == 1){
                return true;
            }
        }
        return false;
    }
    
    public function verifPieceAsouderDansCommande(){
        $pieces = $this->getPieces();        
        foreach($pieces as $piece){
            if(strtolower($piece->getStatut()) == "pour soudure"){
                return true;
            }
        }
        return false;
    }
    
    public function isUrgencePlusOnAPieceInOrder(){
        $bddMng = new Manager();        
        $bdd = $bddMng->dbConnect();
        $piecesOrderMng = new PieceCommandeManager($bdd);                
        if($piecesOrderMng->getPiecesByCommandeWithUrgencePlus($this->id)){            
            return true;
        }
        return false;
    }
    
    public function isCounterRunningOnAPieceInOrder(){
        $bddMng = new Manager();        
        $bdd = $bddMng->dbConnect();
        $counterMng = new PieceTempsCompteurManager($bdd);        
        if($counterMng->isCompteursForOrder($this->id)){
            return true;
        }
        return false;
    }
    
    public function getAutoStatutByPieces(){
        $statut = $this->statut;
        
        $statutsEnCours = ["en cours", "au laser","stoppe","termine", "livre", "en peinture","a l'acide","pour peinture","pour soudure","attente matiere"];
        $statutsTermine = ["termine", "livre", "en peinture", "a l'acide","pour peinture"];        
        $statutsLivre = ["livre"];
        $pieces = $this->getPieces();
        $nbPiece = count($pieces);
        $newStatut = "en attente";
        
        $i = 0;    
        foreach($pieces as $piece){
            $statutPiece = strtolower($piece->getStatut());
            if(in_array($statutPiece,$statutsLivre)){
                $i++;
            }
            if($i == $nbPiece){
                return "livré";                
            }                          
        }
                
        $i = 0;
        $traitement = "";
        foreach($pieces as $piece){
            $statutPiece = strtolower($piece->getStatut());           
            if(in_array($statutPiece,$statutsTermine)){
                $i++;
            }
            if($statutPiece == "en peinture" or $statutPiece == "a l'acide" or $statutPiece == "pour peinture"){
               $traitement =  ' ('.$statutPiece.')';
            }
            if($i == $nbPiece){                
                return "terminé".$traitement;                
            }                          
        }
        
        $i = 0;
        $traitement = "";
        $tempStatut = array();
        foreach($pieces as $piece){
            $statutPiece = strtolower($piece->getStatut()); 
            if(in_array($statutPiece,$statutsEnCours)){
                $i++;
                if(($statutPiece == "au laser" or $statutPiece == "stoppe" or $statutPiece == "pour peinture" or $statutPiece == "en peinture" or $statutPiece == "pour soudure" or $statutPiece == "attente matiere") and !in_array($statutPiece,$tempStatut)){
                   $traitement .=  '<br> ('.$statutPiece.')';
                   $tempStatut[] = $statutPiece;
                }
                $newStatut = "en cours".$traitement;                    
            }
        }
                                      
        return $newStatut;
    }
    
    public function watchStatut($subStatut = null){
        $today = date("m.d.y");
        $datetime1 = new DateTime($today);
        $datetime2 = new DateTime($this->date_livr);
        $interval = $datetime1->diff($datetime2);  
        
        if($this->statut == "livré"){
            return "statutLivre";
        }
        if(preg_match('/pour peinture/', $this->statut, $matches)){
            if(preg_match('/attente matiere/', $this->statut, $matches) && $subStatut == true){
                return "statutPourPeinture insetAttenteMatiere";
            }
            return "statutPourPeinture";
        }        
        if(preg_match('/terminé/', $this->statut, $matches)){            
            return "statutFini";
        }        
        if(($this->verifPieceUrgenteDansCommande() && $subStatut == null) or 
          $this->verifPieceAsouderDansCommande()){
            if(preg_match('/attente matiere/', $this->statut, $matches) && $subStatut == true){
                return "statutUrgent insetAttenteMatiere";
            }
            return "statutUrgent";
        }
        if($datetime1>$datetime2 && $subStatut == null){
            return "statutRetard";
        }         
        if(preg_match('/attente matiere/', $this->statut, $matches)){
            return "statutAttenteMatiere";
        }        
        if(preg_match('/en cours/', $this->statut, $matches)){
            if(preg_match('/stoppe/', $this->statut, $matches) && $subStatut == true){
                return "statutEnCours insetRetard";
            }            
            return "statutEnCours";
        }
        if($this->statut == "en attente"){
            return "statutEnAttente";
        }
    }
}