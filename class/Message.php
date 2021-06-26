<?php

class Message {
    
    private $id;
    private $nom;
    private $date_m;
    private $text;
        
    public function getId(){
        return $this->id;
    }
    
    public function getNom(){
        return $this->nom;
    }
    
    public function getDateM(){
        return $this->date_m;
    }
    
    public function getText(){
        return $this->text;
    }    
}