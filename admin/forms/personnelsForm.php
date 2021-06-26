<?php
    $allUsers = $userMng->getUsers();    
?>

<section class="tcs-container b_shadow ouvriersManage">
    <button id="closePersonnelsForm" class="closeBtn"><i class="fas fa-times"></i></button>
    <h2 class="handleDrag">Personnels</h2>
    
    <ul>
       <?php 
        foreach($allUsers as $user):            
        ?>
            <li class="user-<?= $user->getId();?>">
                <form action="" method="post" class="inline modifyUserForm">                
                    <input type="number" name="id" value="<?= $user->getId();?>" hidden>                
                    <input type="text" value="<?= $user->getNom();?>" name="nom">                
                    <?php
                        $ouvrier = ""; $interim = ""; $cadreAdmin = ""; $cadreGerant = ""; $cadreGlobal = "";
                        if($user->getStatut() == "ouvrier"){
                            $ouvrier = "selected";
                        }elseif($user->getStatut() == "interimaire"){
                            $interim = "selected";
                        }elseif($user->getStatut() == "cadre admin"){
                            $cadreAdmin = "selected";
                        }elseif($user->getStatut() == "cadre gerant"){
                            $cadreGerant = "selected";
                        }elseif($user->getStatut() == "cadre global"){
                            $cadreGlobal = "selected";
                        }
                    ?>

                    <select name="statut">
                        <option value="ouvrier" <?= $ouvrier;?>>Ouvrier</option>
                        <option value="interimaire" <?= $interim;?>>Interimaire</option>
                        <option value="cadre admin" <?= $cadreAdmin;?>>Cadre admin</option>
                        <option value="cadre gerant" <?= $cadreGerant;?>>Cadre gerant</option>
                        <option value="cadre global" <?= $cadreGlobal;?>>Cadre global</option>
                    </select>
                    <button type="submit" class="btn btn_done"><i class="fas fa-save"></i></button>
                </form>
                <button class="btn btn_danger deleteUser" data-idUser="<?= $user->getId();?>"><i class="fas fa-trash-alt"></i></button>
            </li>
        <?php endforeach; ?>
    </ul>

    <form action="" method="post" id="addUserForm">
        <input type="text" name="nom">
        <select name="statut" id="">
            <option value="ouvrier">Ouvrier</option>
            <option value="interimaire">Interimaire</option>
            <option value="cadre">Cadre</option>
        </select>
        <button type="submit" class="btn btn_valid"><i class="fas fa-user-plus"></i></button>
    </form>
</section>