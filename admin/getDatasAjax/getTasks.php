<?php
require(__DIR__."/../../include/fonctions.php");
getAutoloader();

$bddMng = new Manager();        
$bdd = $bddMng->dbConnect();
$tasksMng = new TaskManager($bdd);

if(isset($_GET['all'])){
    $tasks = $tasksMng->getTasks(); 
    if($tasks){
        $tasksArray = array();
        foreach($tasks as $task){
            $id = $task->getId();
            $from = trim($task->getFromUser());
            $for = trim($task->getForUser());
            $tache = $task->getTache();
            $date = date('d/m/Y à H:i',strtotime($task->getDateT()));
            $statut = $task->getStatut();
            
            array_push($tasksArray, ["id"   =>$id,
                                     "from" =>$from,
                                     "for"  =>$for,
                                     "tache" =>$tache,
                                     "date" =>$date,
                                     "statut" =>$statut,
                                    ]);
        }        
        echo json_encode($tasksArray);        
    }else{
        echo json_encode(["no_tasks"=>"Aucune taches en cours..."]);        
    }
}