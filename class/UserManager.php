<?php
class UserManager{
    
    private $db;
    
    public function __construct($bdd){
        $this->db = $bdd;
    }
    
    public function getUsers(){             
        $users = $this->db->query('SELECT * FROM users ORDER BY statut DESC');        
        $users = $users->fetchAll(PDO::FETCH_CLASS, 'User');
        
        return $users;        
    }
    
    public function getFirstUser(){             
        $user = $this->db->query('SELECT * FROM users LIMIT 1');        
        $user = $user->fetchObject('User');
        
        return $user;        
    }
    
    public function getUser($id){         
        $user = $this->db->query('SELECT * FROM users WHERE id ="'.$id.'" ');        
        return $user->fetchObject('User');
    }
    
    public function updateUser($id,$nom,$statut){         
        $userUpdate = $this->db->prepare('UPDATE users SET nom=:nom,statut=:statut WHERE id=:id');
        $userUpdate->execute(array(
            'id'=>$id,
            'nom'=>$nom,
            'statut'=>$statut,
        ));        
    }
    
    public function insertUser($nom,$statut){         
        $userInsert = $this->db->prepare('INSERT INTO users(nom, statut) VALUES (:nom,:statut)');
        $userInsert->execute(array(            
            'nom'=>$nom,
            'statut'=>$statut,
        ));   
        return $this->db->lastInsertId();
    }
    
    public function deleteUser($id){         
        $userDelete = $this->db->prepare('DELETE FROM users WHERE id=:id');
        $userDelete->execute(array(
            'id'=>$id 
        ));        
    }
    
}