<?php

class TaskManager{
    
    private $db;
    
    public function __construct($bdd){
        $this->db = $bdd;
    }
    
    public function getTasks(){             
        $tasks = $this->db->query('SELECT * FROM todo_list ORDER BY id DESC');        
        $tasks = $tasks->fetchAll(PDO::FETCH_CLASS, 'Task');
        
        return $tasks;        
    }
    
    public function getTasksByUser($userName){             
        $tasks = $this->db->prepare('SELECT * FROM todo_list 
                                    WHERE for_user = :for_user
                                    AND statut = "waiting"
                                    ORDER BY id DESC');  
        $tasks->execute(array(                       
            'for_user'=>$userName,
        ));
        $tasks = $tasks->fetchAll(PDO::FETCH_CLASS, 'Task');
        
        return $tasks;        
    }
    
    public function getTask($id){         
        $task = $this->db->query('SELECT * FROM todo_list WHERE id ="'.$id.'" ');        
        return $task->fetchObject('Task');
    }
    
    public function insertTask($from,$for,$tache){         
        $messageInsert = $this->db->prepare('INSERT INTO todo_list(from_user, for_user,date_t,tache,statut) VALUES (:from,:for,NOW(),:tache,:statut)');
        
        $messageInsert->execute(array(            
            'from'=>$from,
            'for'=>$for,
            'tache'=>$tache,
            'statut'=>"waiting",
        ));        
    }
    
    public function updateTask($id,$statut){         
        $taskUpdate = $this->db->prepare('UPDATE todo_list SET statut=:statut WHERE id=:id');
        $taskUpdate->execute(array(
            'id'=>$id,            
            'statut'=>$statut,
        ));        
    }
    
    public function deleteTask($id){         
        $userDelete = $this->db->prepare('DELETE FROM todo_list WHERE id=:id');
        $userDelete->execute(array(
            'id'=>$id 
        ));        
    }
}