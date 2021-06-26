<?php

class Client {
    
    private $id;
    private $nom;
    private $mail_c;
    private $tel;
    private $adresse;
    private $type;
        
    public function getId(){
        return $this->id;
    }
    
    public function getNom(){
        return $this->nom;
    }
    
    public function getMail(){
        return $this->mail_c;
    }
    
    public function getTel(){
        return $this->tel;
    }  
    
    public function getAdresse(){
        return $this->adresse;
    } 
    
    public function getType(){
        return $this->type;
    } 
    
    public function extractMultipleMails(){
        if(!empty($this->mail_c)){
            $regexp = '/(?:\s*\/\s*)?([a-z0-9.\-_]+@[a-z0-9.\-_]+)(?:\s*\/\s*)?/mi';
            preg_match_all($regexp, $this->mail_c, $matches,PREG_PATTERN_ORDER);
            if($matches[1]){
                return $matches[1];           
            }
        }
    }
    
    public function extractMultipletels(){
        if(!empty($this->tel)){
            $regexp = '/(?:\s*\/\s*)?([a-z 0-9:()\.\-éè]+)(?:\s*\/\s*)?/mi';
            preg_match_all($regexp, $this->tel, $matches,PREG_PATTERN_ORDER);
            if($matches[1]){
                return $matches[1];           
            }
        }
    }
}