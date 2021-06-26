<nav class="menu backg_primary b_shadow">
    <ul>
        <li>
            <a href="<?= $root;?>" id="homeBtnMenu" title="Retour a l'accueil">
                <i class="fas fa-home"></i>
            </a>
        </li>
        
        <li>
            <!-- charge toutes les commandes non finie -->
            <a href="<?= $root."pages/commandes.php?filter=notTermined&paramFilter=true&orderBy=statut";?>" id="ordersBtnMenu" title="Vers page des commandes non terminées"> 
                <i class="fas fa-list-alt"></i>
            </a>
        </li>
        
        <li>
            <a href="#" id="addOrderAtelierBtnMenu" title="Ajout d'une commande depuis l'atelier">
                <i class="fas fa-cart-plus"></i>
            </a>
        </li>
        
        <li>
            <a href="#" id="searchBtnMenu" title="Recherche piece ou commande">
                <i class="fas fa-search"></i>
            </a>
        </li>
        
        <li>
            <a href="#" id="chatBtnMenu" title="Boite de dialogue">
                <i class="fas fa-comments"></i>
            </a>
        </li>
        
        <li>
            <a href="#" id="todoBtnMenu" title="Boite des taches importantes">
                <i class="fas fa-clipboard-list"></i>
            </a>
        </li>
        
        <?php if(in_array(get_client_ip_server(),$ipAdmins)): ?>        
        <li title="Acces au listing pour cocher les bons de livraison et factures" class="btn_shadow">
            <a href="<?= $root.'pages/listingBlFactures.php?month='.$thisMonth.'&year='. $thisYear;?>" id="gestionBlFactures"> 
            <i class="fas fa-tasks"></i></a>
        </li>
        
        <li>
            <a href="<?= $root."admin.php";?>" id="configBtnMenu" title="Acces administration">
                <i class="fas fa-users-cog"></i>
            </a>
        </li>        
        <?php endif; ?>       
        
    </ul>   
</nav>


<?php
include(__DIR__."/chat.php");
include(__DIR__."/search.php");
include(__DIR__."/todo.php");
