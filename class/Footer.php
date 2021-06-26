<?php

class Footer {
    
    private $id;
    private $text;
    private $mode;    
        
    public function getId(){
        return $this->id;
    }
    
    public function getText(){
        return $this->text;
    }
    
    public function getMode(){
        return $this->mode;
    }      
}