<?php
class FooterManager{
    
    private $db;
    
    public function __construct($bdd){
        $this->db = $bdd;
    }   
        
    public function getFooter(){         
        $footer = $this->db->query('SELECT * FROM footer WHERE id ="1" ');        
        return $footer->fetchObject('Footer');
    }
    
    public function updateFooter($text,$mode){         
        $footerUpdate = $this->db->prepare('UPDATE footer SET text=:text,mode=:mode WHERE "1"');
        $footerUpdate->execute(array(            
            'text'=>$text,
            'mode'=>$mode,
        ));        
    }    
    
}