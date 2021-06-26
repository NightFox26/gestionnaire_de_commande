<?php

class User {
    
    private $id;
    private $nom;
    private $statut;    
        
    public function getId(){
        return $this->id;
    }
    
    public function getNom(){
        return $this->nom;
    }
    
    public function getStatut(){
        return $this->statut;
    }      
}