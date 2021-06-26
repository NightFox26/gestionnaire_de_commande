<?php

class Piece {
    
    private $id;
    private $ref;
    private $ral;
    private $plan;
    private $infos;     
    private $id_client;
        
    public function getId(){
        return $this->id;
    }
    
    public function getRef(){
        return $this->ref;
    }
    
    public function getRal(){
        return $this->ral;
    }
    
    public function getPlan(){
        return $this->plan;
    } 
    
    public function getInfos(){
        return $this->infos;
    }      
    
    public function getIdClient(){
        return $this->id_client;
    } 
    
    public function isPdfPlanType(){        
        if(preg_match('/.pdf$/i', $this->plan, $matches)){
            return true;
        }
        return false;
    }
    
    public function getClient(){
        $bddMng = new Manager();        
        $bdd = $bddMng->dbConnect();
        $clientMng = new ClientManager($bdd);
        return $clientMng->getClient($this->id_client);
    }  
}