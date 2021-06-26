<?php

class ClientManager{
    
    private $db;
    
    public function __construct($bdd){
        $this->db = $bdd;
    }
    
    public function getClient($id){         
        $client = $this->db->query('SELECT * FROM client WHERE id ="'.$id.'" ');        
        return $client->fetchObject('Client');
    }
    
    public function getClients(){             
        $clients = $this->db->query('SELECT * FROM client 
                                    ORDER BY nom ASC');        
        $clients = $clients->fetchAll(PDO::FETCH_CLASS, 'Client');        
        return $clients;        
    }   
    
    public function getClientsSearch($word){             
        $clients = $this->db->prepare("SELECT * FROM client 
                                       WHERE nom LIKE :word
                                       OR mail_c LIKE :word
                                       OR tel LIKE :word
                                       OR adresse LIKE :word
                                       ORDER BY nom ASC");    
        $clients->execute(array(
                   'word'    =>'%'.$word.'%'                  
        ));
        
        $clients = $clients->fetchAll(PDO::FETCH_CLASS, 'Client');
        
        return $clients;        
    }
    
    public function getClientsPro(){         
        $clients = $this->db->query('SELECT * FROM client 
                                    WHERE type ="pro" 
                                    ORDER BY nom ASC');        
        $clients = $clients->fetchAll(PDO::FETCH_CLASS, 'Client');        
        return $clients;
    }
    
    public function getClientsPart(){         
        $clients = $this->db->query('SELECT * FROM client 
                                    WHERE type ="particulier" 
                                    ORDER BY nom ASC');
        $clients = $clients->fetchAll(PDO::FETCH_CLASS, 'Client');        
        return $clients;
    }
    
    public function insertClient($nom, $mail, $tel, $adresse, $type){         
        $clientInsert = $this->db->prepare('INSERT INTO client(nom, mail_c, tel, adresse, type)
                                        VALUES (:nom, :mail_c, :tel, :adresse, :type)');
        
        try{
            $clientInsert->execute(array(            
            'nom'       =>$nom,
            'mail_c'    =>$mail,
            'tel'       =>$tel,
            'adresse'   =>$adresse,
            'type'      =>$type
            ));   
            return $this->db->lastInsertId();
        }catch(PDOException $e){    
            $errorInfo = $e->errorInfo;
            if($errorInfo[1] == 1062) {
                echo json_encode(["error"=>"Ce client existe deja !!!"]);
                exit();
            }        
        }
    }
    
    public function updateClient($id,$nom, $mail, $tel, $adresse,$type){         
        $clientUpdate = $this->db->prepare('UPDATE client SET 
                                        nom     =:nom,
                                        mail_c  =:mail_c,
                                        tel     =:tel,
                                        adresse =:adresse, 
                                        type    =:type 
                                        WHERE id=:id');
        $clientUpdate->execute(array(
            'id'        =>$id,
            'nom'       =>$nom,
            'mail_c'    =>$mail,
            'tel'       =>$tel,
            'adresse'   =>$adresse,            
            'type'      =>$type            
        ));        
    }
    
}