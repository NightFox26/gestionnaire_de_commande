<?php

class MessageManager{
    
    private $db;
    
    public function __construct($bdd){
        $this->db = $bdd;
    }
    
    public function getMessages(){             
        $messages = $this->db->query('SELECT * FROM chat_msgs ORDER BY id DESC LIMIT 15');        
        $messages = $messages->fetchAll(PDO::FETCH_CLASS, 'Message');
        
        return $messages;        
    }
    
    public function getMessage($id){         
        $message = $this->db->query('SELECT * FROM chat_msgs WHERE id ="'.$id.'" ');        
        return $message->fetchObject('Message');
    }
    
    public function insertMessage($nom, $text){         
        $messageInsert = $this->db->prepare('INSERT INTO chat_msgs(nom, date_m, text) VALUES (:nom, NOW(), :text)');
        
        $messageInsert->execute(array(            
            'nom'=>$nom,
            'text'=>$text,
        )); 
        return $this->db->lastInsertId();
    }
}