<?php

class PieceCommande {
    
    private $id;
    private $id_commande;
    private $id_piece;
    private $ref;
    private $qt;
    private $date_livr;
    private $infos;
    private $infos_unique;
    private $infos_fab;
    private $ral;
    private $plan;
    private $statut;
    private $urgente;
    private $cloture;
    private $urgencePlus;
        
    public function getId(){
        return $this->id;
    }
    
    public function getIdCommande(){
        return $this->id_commande;
    }
    
    public function getIdPiece(){
        return $this->id_piece;
    }
    
    public function getRef(){
        return $this->ref;
    }
    
    public function getDateLivr(){
        return $this->date_livr;
    }
    
    public function getQt(){
        return $this->qt;
    }
    
    public function getInfos(){
        return $this->infos;
    }
    
    public function getInfosUnique(){
        return $this->infos_unique;
    }
    
    public function getInfosFab(){        
        return trim($this->infos_fab);
    }
    
    public function getRal(){
        return $this->ral;
    }
    
    public function getPlan(){
        return $this->plan;
    } 
    
    public function getStatut(){
        return $this->statut;
    } 
    
    public function getUrgente(){
        return $this->urgente;
    } 
    
    public function getIsCloture(){
        return $this->cloture;
    }
    
    public function getUrgencePlus(){
        return $this->urgencePlus;
    }
    
    public function getCommande(){
        $bddMng = new Manager();        
        $bdd = $bddMng->dbConnect();
        $orderMng = new CommandeManager($bdd);
        return $orderMng->getCommande($this->id_commande);
    }
    
    public function isPdfPlanType(){
        if(preg_match('/.pdf$/i', $this->plan, $matches)){
            return true;
        }
        return false;
    }
    
}