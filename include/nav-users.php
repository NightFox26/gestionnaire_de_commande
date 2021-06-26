<script>console.log("user actif ="+'<?=$_SESSION["user"];?>');</script>

<nav class="users backg_primary b_shadow">    
    <ul>
        <?php        
            $users = $userMng->getUsers(); 
            $i = 0;
            foreach($users as $user):
                $selected = $user->getId() == $_SESSION["idUser"]? "selected":"";
                $class = "color-".$user->getStatut();
                if(strpos($user->getStatut(),'cadre')!==false && isOuvrier()){
                    $class .= " hidden";
                }
        ?>
                <li class="<?= $selected.' '.$class;?>" data-idUser="<?= $user->getId();?>"><i class="fas fa-user"></i>  <?= $user->getNom();?></li>            
        <?php
            $i++;
            endforeach; 
        ?>            
    </ul>
</nav>