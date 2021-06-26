<section class="tcs-container b_shadow <?= $cree_par ;?>" id="addOrderForm">
    <div class="float_right">      
        <?php        
        $refTcs = "";
        if(isset($_GET['refTcs'])){
            $refTcs = htmlspecialchars($_GET['refTcs']);
        }
        
        $desactivate ="";
        if($cree_par == "atelier"){
            $desactivate = "hidden";    
        }elseif($cree_par == "bureau"){
        ?>
            <button id="deleteCommande" class="btn btn_danger" title="supprimer la commande"><i class="fas fa-trash-alt"></i></button>
        <?php } ?>
            <button id="clearCommandeForm" class="btn btn_lightBlue" title="Nouvelle commande"><i class="far fa-file-excel"></i></button>
        
        <button id="closeCommandeForm" class="btn" title="fermer la boite de dialogue"><i class="fas fa-times"></i></button>
    </div>
    <h2 class="handleDrag">Ajout/Modif de commande <span id="infoSaveOrder"></span></h2>

    <form action="" id="commandeForm">
        <fieldset>
            <legend>Commande</legend>
            <input type="nombre" name="id" value="" placeholder="index" class="float_right smallInput" hidden>
            <input type="text" name="statut" value="" hidden>
            <input type="text" placeholder="Ref commande TCS" name="ref_tcs" value="<?= $refTcs;?>" <?= $desactivate;?>>
            
            <?php if($cree_par == "bureau"): ?>
                <button type="button" id="searchOrder" class="btn_transp"><i class="fas fa-search"></i></button>
            <?php endif; ?>
            
            <input type="text" placeholder="Ref commande client" name="ref_client"><br><br>

            <div class="inline">
                <label for="dateRecep">Date de reception</label><br>
                <input type="date" id="dateRecep" name="date_recept">
            </div>
            <div class="inline">
                <label for="dateLivr">Date de livraison</label><br>
                <input type="date" id="dateLivr" name="date_livr">
            </div>
        </fieldset>

        <br>
        <fieldset>
            <div class="inline">
                <label>Professionel</label><br>
                <input list="client_pro" name="client_nom_pro" autocomplete="off" placeholder="Nom client pro">                
                <datalist id="client_pro">
                    <?php
                    $clientsPro = $clientMng->getClientsPro();
                    foreach($clientsPro as $client):
                        echo '<option data-id="'.$client->getId().'" value="'.$client->getNom().'">';
                    endforeach;?>
                </datalist>
            </div>

            <div class="inline">
                <label>Particulier</label><br>
                <input list="client_part" name="client_nom_part" autocomplete="off" placeholder="Nom client particulier">  
                <datalist id="client_part">
                    <?php
                    $clientsPart = $clientMng->getClientsPart();
                    foreach($clientsPart as $client):
                        echo '<option data-id="'.$client->getId().'" value="'.$client->getNom().'">';
                    endforeach;?>
                </datalist>
            </div>
        
            <legend>Client</legend>
            <div class="inline">                
                <input type="text" placeholder="Email1 / Email2 / Email3" name="client_mail">
                <input type="tel" placeholder="Nom1:Tel1 / Nom2:Tel2" name="client_tel">
                <textarea name="client_adresse" cols="30" rows="10" placeholder="Infos supplementaires (adresse...etc)"></textarea>
            </div>
        </fieldset>
        <input type="text" name="cree_par" value="<?= $cree_par;?>" hidden><br>
        <button type="submit" class="btn btn_valid"><i class="fas fa-save"></i></button>
    </form>

   <br>
    <fieldset id="formAddPieces">
        <legend>Ajouter une piece a la commande</legend>
        <form action="" id="insertPieceCommande">            
            <div class="pieces">
                <label for="">Creer une nouvelle piece pour <span class="nomClient"></span></label><br>
                                
                <input list="pieces_client" name="piece_ref" autocomplete="off" placeholder="Nouvelle piece ref">                
                <datalist id="pieces_client">                    
                    
                </datalist>
                
                <input type="number" placeholder="Qts" name='piece_qt' value="1" min="1" class="smallInput">
                <input type="text" placeholder="Ral P." name='piece_ral' size="10" class="notVisibleExisting">
                <input type="checkbox" name="piece_urgente" id="urgence">
                <label for="urgence">Piece urgente</label>
                
                <div class="float_right">
                    <button id="viewPiecesCommandeForm" type="button"><i class="fas fa-eye"></i></button>
                    <button class="btn btn_done" type="submit"><i class="fas fa-plus-circle"></i></button>                    
                </div>
                <br>
                <input type="radio" name="piece_unique" value="unique" id="pUnique" class="notVisibleExisting">
                <label for="pUnique" class="notVisibleExisting">Piece sans reutilisation</label>
                <input type="radio" name="piece_unique" value="garder" id="pGarder" class="notVisibleExisting" checked>
                <label for="pGarder" class="notVisibleExisting">Piece a garder</label> <br>
                <input type="text" name="piece_infos" size="37" placeholder="Informations sur la piece" class="notVisibleExisting">
                <input type="file" name='piece_plan' accept="image/*,.pdf" enctype="multipart/form-data" class="notVisibleExisting">
            </div>
        </form>
        
    </fieldset>
</section>

<section class="tcs-container <?= $cree_par ;?>" id="listPiecesOrder">
    <h2 class="handleDrag">Liste des pieces</h2>
    <table>
        <thead>
            <th><i class="fas fa-pallet"></i> Ref piece</th>
            <th><i class="fas fa-boxes"></i> Qts</th>
            <th><i class="fas fa-swatchbook"></i> Ral peinture</th>
            <th><i class="fas fa-file-pdf"></i> PLan</th>
            <th></th>
        </thead>
        <tbody>

        </tbody>
    </table>
</section>