<?php

class PieceTemps {
    
    private $id;
    private $user;
    private $temps;
    private $heures_sup;
    private $id_piece;
        
    public function getId(){
        return $this->id;
    }
    
    public function getUser(){
        return $this->user;
    }
    
    public function getTemps(){
        return $this->temps;
    }
    
    public function getHeuresSup(){
        return $this->heures_sup;
    }
    
    public function getIdPiece(){
        return $this->id_piece;
    }     
}