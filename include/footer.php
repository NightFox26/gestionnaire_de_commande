<?php
$footer = $footerMng->getFooter();
?>
<footer class="backg_primary b_shadow">
    <?php 
        if($footer->getMode() == "text"):
    ?>
        <marquee behavior="alternate" scrollamount="10" onmouseover="this.stop();" onmouseout="this.start();"><?= $footer->getText();?></marquee>
    <?php 
        elseif($footer->getMode() == "tasks"):
            $users = $userMng->getUsers();
            $nbOrdersRefAtelier = count($commandeMng->getCommandesRefAtelier());
            $nbOrdersNoFact = count($commandeMng->getCommandesNotFactured());
            $nbOrdersNolivr = count($commandeMng->getCommandeToLivr());       
            $nbPiecesNotClotured = count($pieceCommandeMng->getPiecesNotClotured());       
            $nbPieceToPaint = count($pieceCommandeMng->getPiecesToPaint()); 
            $nbPieceInAcid = count($pieceCommandeMng->getPiecesInAcid()); 
    
            echo '<marquee behavior="alternate" scrollamount="10" onmouseover="this.stop();" onmouseout="this.start();">';
            $i = 0;
            foreach($users as $user){
                $userName = $user->getNom();
                $nbTasksNotDone = count($taskMng->getTasksByUser($userName));
                if($nbTasksNotDone > 0){
                    if($i>0){
                        echo '**';
                    }
                    $i++;
                    echo ' <i class="fas fa-user"></i> <a class="userTask">'.$userName.' :</a> <span class="textRed">'.$nbTasksNotDone.' <i class="fas fa-clipboard-list"></i> </span>';                    
                }
            }
           
            if($_SESSION["statutUser"] == "cadre global"){                
                showPieceNotClotured($nbPiecesNotClotured,$root);
                showPieceNoFactured($nbOrdersNoFact,$root);
                showPieceRefAtelier($nbOrdersRefAtelier,$root);
                showPieceToPaint($nbPieceToPaint,$root);
                showPieceInAcid($nbPieceInAcid,$root);
                showPieceToLivr($nbOrdersNolivr,$root);
            }elseif($_SESSION["statutUser"] == "cadre gerant"){
                showPieceNotClotured($nbPiecesNotClotured,$root);
                showPieceRefAtelier($nbOrdersRefAtelier,$root);
                showPieceToPaint($nbPieceToPaint,$root);
                showPieceToLivr($nbOrdersNolivr,$root);
            }elseif($_SESSION["statutUser"] == "cadre admin"){
                showPieceNoFactured($nbOrdersNoFact,$root);
                showPieceRefAtelier($nbOrdersRefAtelier,$root);
                showPieceToPaint($nbPieceToPaint,$root);
                showPieceToLivr($nbOrdersNolivr,$root);
            }elseif($_SESSION["statutUser"] == "ouvrier"){
                showPieceToPaint($nbPieceToPaint,$root);
                showPieceInAcid($nbPieceInAcid,$root);
                showPieceToLivr($nbOrdersNolivr,$root);                
            }
    
            echo "<span id='newMessageFooter'> ** <span class='textGreen'><span id='newMessageCount'>1</span> nouveau <i class='fas fa-comments'></i></span></span>";
            echo '</marquee>';
    ?>
    
    <?php 
        endif;
    ?>
</footer>


<?php

function showPieceNotClotured($nbPiecesNotClotured,$root){
    if($nbPiecesNotClotured > 0){
        echo ' ** <span><a href="'.$root.'pages/commandes.php?filter=all&paramFilter=non cloturées">Non cloturées : </a></span> <span class="textRed">'.$nbPiecesNotClotured.' <i class="fas fa-lock-open"></i> </span>';
    }
}

function showPieceRefAtelier($nbOrdersRefAtelier,$root){
    if($nbOrdersRefAtelier > 0){
        echo ' ** <span><a href="'.$root.'pages/commandes.php?filter=all&paramFilter=ref atelier">Com. atelier : </a></span> <span class="textRed">'.$nbOrdersRefAtelier.' <i class="fas fa-file-medical"></i> </span>';
    }
}

function showPieceToPaint($nbPieceToPaint,$root){
    if($nbPieceToPaint > 0){
        echo ' ** <span><a href="'.$root.'pages/commandes.php?filter=all&paramFilter=pour peinture">Pour Peinture : </a></span> <span class="textRed">'.$nbPieceToPaint.' <i class="fas fa-paint-roller"></i> </span>';
    }
}

function showPieceInAcid($nbPieceInAcid,$root){
    if($nbPieceInAcid > 0){
        echo ' ** <span><a href="'.$root.'pages/commandes.php?filter=all&paramFilter=dans acide">Dans acide : </a></span> <span class="textRed">'.$nbPieceInAcid.' <i class="fas fa-water"></i> </span>';
    }
}

function showPieceToLivr($nbOrdersNolivr,$root){
    if($nbOrdersNolivr > 0){
        echo ' ** <span><a href="'.$root.'pages/commandes.php?filter=all&paramFilter=non livrées">Non livrées : </a></span> <span class="textRed">'.$nbOrdersNolivr.' <i class="fas fa-truck"></i> </span>';
    }
}

function showPieceNoFactured($nbOrdersNoFact,$root){
    if($nbOrdersNoFact > 0){
        echo ' ** <span><a href="'.$root.'pages/commandes.php?filter=all&paramFilter=non facturées">Non facturées : </a></span> <span class="textRed">'.$nbOrdersNoFact.' <i class="fas fa-file-invoice-dollar"></i> </span>';
    }
}