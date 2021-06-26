<section id="todoBox" class="backg_secondary b_shadow">    
    <h2 class="handleDrag">Liste des taches</h2>
    <button id="closeTodo" class="closeBtn"><i class="fas fa-times"></i></button>
    
    <hr>
    
    <div class="todoListe">        
       <ul>
           <?php 
                $tasks = $taskMng->getTasks();
                foreach($tasks as $task):
                    $date = $task->getDateT();
                    $statut = $task->getStatut();
           ?>
                   <li data-idTask="<?= $task->getId();?>" class="statut-<?= $statut;?>"> 
                        <div class="btnsTodo">
                            <button class="btn btn_done"><i class="fas fa-calendar-check"></i></button>
                            <button class="btn btn_valid"><i class="fas fa-check-circle"></i></button>
                            <button class="btn btn_danger"><i class="fas fa-times-circle"></i></button>
                        </div>
                        <i class="far fa-calendar-plus"></i><?= date('d/m/Y à H:i', strtotime($date));?> <br>
                        <i class="fas fa-user"></i> <span> <?= $task->getFromUser();?> </span> <i class="fas fa-long-arrow-alt-right"></i>
                        <i class="fas fa-user"></i> <span> <?= $task->getForUser();?></span> : <br>
                        <p class="tacheTexte"><?= $task->getTache();?></p>
                   </li>
                   <hr>           
           <?php endforeach;?>
       </ul>       
    </div>
    
    <hr>
    
    <form action="" method="post" id="submitTaskForm">        
        <input type="text" name="tache" placeholder="Tache a realiser"><br>
        <label for="whoTodo">Pour qui ?</label>
        <select name="for_user" id="whoTodo">
            <?php
            $users = $userMng->getUsers();
            foreach($users as $user){
                echo '<option value="'.$user->getNom().'">'.$user->getNom().'</option>';
            }
            ?> 
        </select>
        <input type="submit" value="Ajouter" class="btn btn_valid float_right">
    </form>
</section>