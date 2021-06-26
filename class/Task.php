<?php

class Task {
    
    private $id;
    private $from_user;
    private $for_user;
    private $date_t;
    private $tache;
    private $statut;
        
    public function getId(){
        return $this->id;
    }
    
    public function getFromUser(){
        return $this->from_user;
    }
    
    public function getForUser(){
        return $this->for_user;
    }
    
    public function getDateT(){
        return $this->date_t;
    } 
    
    public function getTache(){
        return $this->tache;
    }  
    
    public function getStatut(){
        return $this->statut;
    }  
}