<?php
include("../include/header.php");

$year = null;
$month =null;
$monthName =null;
if(isset($_GET['year'])){
    $year = htmlspecialchars($_GET['year']);
}

if(isset($_GET['month'])){
    $month = htmlspecialchars($_GET['month']);    
    $monthName = monthName($month);
}

$filter =null;
$paramFilter =null;
if(isset($_GET['filter']) and isset($_GET['paramFilter'])){
    $filter = htmlspecialchars($_GET['filter']);
    $paramFilter = htmlspecialchars($_GET['paramFilter']);
}

$iconFiltre = '<i class="fas fa-sort-down"></i>';
?>

<body id="commandesPage">
    <section class="bg commandesBg">
       <?php
        $cree_par = "atelier";
        include(__DIR__."/../include/nav-users.php");        
        include(__DIR__."/../include/nav-menu.php");
        include(__DIR__."/../include/clock.php");        
        include(__DIR__."/../admin/forms/commandesForm.php");
        include(__DIR__."/../include/modalAlert.php");
        include(__DIR__."/../include/modalMailer.php");
        
        
        if($filter == "notTermined" and $paramFilter == "true"){ 
            $orderBy = null;
            if(isset($_GET['orderBy'])){
                $orderBy = htmlspecialchars($_GET['orderBy']);
            }
            $orders = $commandeMng->getCommandesNotTermined($orderBy); 
        }elseif($paramFilter == "clientName"){
            if($filter != "notTermined")
                $orders = $commandeMng->getCommandesOrderByClients($month,$year);
            else                
                $orders = $commandeMng->getCommandesOrderByClientsNotTerminate();
        }elseif($paramFilter == "pour peinture"){
            $orders = $commandeMng->getCommandeWithPieceToPaint();
        }elseif($paramFilter == "dans acide"){
            $orders = $commandeMng->getCommandeWithPieceInAcid();
        }elseif($paramFilter == "non livrées"){
            $orders = $commandeMng->getCommandeToLivr();
        }elseif($paramFilter == "non cloturées"){            
            $orders = $commandeMng->getCommandeWithPieceNotClotured();
        }elseif($paramFilter == "non facturées"){
            $orders = $commandeMng->getCommandesNotFactured();
        }elseif($paramFilter == "ref atelier"){ 
            $orders = $commandeMng->getCommandesRefAtelier();
        }else{
            $orders = $commandeMng->getCommandesByParams($month,$year,$filter,$paramFilter);
        } 
        ?>
        
        <div class="tcs-container">
            <?php 
            if(!isset($_GET['id_commande'])):
                if($filter == "notTermined"):?>
                     <h1 id="title_listing_not_termined" class="t_shadow"> 
                        <u>Listing des commandes non terminées </u>                      
                        <a href="commandes.php?month=<?= $thisMonth;?>&year=<?= $thisYear;?>" class="textBlue btn_shadow"><i class="fas fa-list-alt"></i>  Retour aux commandes <br>de <?= monthName($thisMonth).' '.$thisYear ;?></a>
                    </h1>            
                 <?php 
                 elseif($filter == "all"):?>
                     <h1 id="title_listing_not_termined" class="t_shadow"> 
                        <u>Listing de TOUTES les commandes <?= $paramFilter; ?>  </u>  
                    </h1>                    
                 <?php 
                 else:?> 
                    <div class="codeColors">                
                        <div class="statutRetard btn_shadow">
                            <a href="commandes.php?month=<?= $month;?>&year=<?= $year;?>&filter=dateRetard&paramFilter=<?= $thisYear.'-'.$thisMonth.'-'.$thisDay;?>">
                            En retard <?= $iconFiltre;?></a>
                        </div>
                        <div class="statutEnAttente btn_shadow">
                            <a href="commandes.php?month=<?= $month;?>&year=<?= $year;?>&filter=statut&paramFilter=en attente">
                            En attente <?= $iconFiltre;?></a>
                        </div>
                        <div class="statutEnCours">
                            <a href="commandes.php?month=<?= $month;?>&year=<?= $year;?>&filter=statut&paramFilter=en cours">
                            En cours <?= $iconFiltre;?></a>
                        </div>
                        <div class="statutUrgent btn_shadow">                    
                            Urgent/Soudure
                        </div>
                        <div class="statutPourPeinture btn_shadow">
                           <a href="commandes.php?month=<?= $month;?>&year=<?= $year;?>&filter=statut&paramFilter=pourPeinture">
                            Pour Peinture <?= $iconFiltre;?></a>
                        </div>
                        <div class="statutFini btn_shadow">
                            <a href="commandes.php?month=<?= $month;?>&year=<?= $year;?>&filter=statut&paramFilter=terminé">
                            Terminé <?= $iconFiltre;?></a>
                        </div>
                        <div class="statutLivre btn_shadow">
                            <a href="commandes.php?month=<?= $month;?>&year=<?= $year;?>&filter=statut&paramFilter=livré">
                            Livré <?= $iconFiltre;?></a>
                        </div>  
                        
                        <div class="allOrdersNonTermine btn_shadow">
                            <a href="commandes.php?filter=notTermined&paramFilter=true">Toutes les commandes non terminé ou non livré (sur tous les mois) <?= $iconFiltre;?></a>
                        </div>
                    </div>
                    <div class="inline">       
                        <h1 class="t_shadow">
                            <a href="commandes.php?<?= monthMoins($month,$year);?>"><i class="fas fa-chevron-left"></i></a>
                            <a href="commandes.php?month=<?= $month;?>&year=<?= $year;?>" class="textBlue"><i class="fas fa-list-alt"></i> Mois de <?= $monthName.' '.$year;?></a>
                            <a href="commandes.php?<?= monthPlus($month,$year);?>"> <i class="fas fa-chevron-right"></i></a>
                            
                            <input type="month" id="inputChangeMonth" name="inputChangeMonth" min="2019-01" value="<?=$year."-".$month;?>">
                        </h1>                                  
                    </div>
                <?php endif; 
            else: 
                $idOrder = htmlspecialchars($_GET['id_commande']);
                $orderSearch = $commandeMng->getCommande($idOrder);
                $orders = array();
                $orders[] = $orderSearch;
            ?>
                <h1>Recherche de la commande ref : <?= $orderSearch->getRefTcs();?></h1>
            <?php endif;?>
            <div class="backg_gray_no_transp countOrder">(<?= count($orders);?> commandes trouvées)</div>
            <table class="backg_gray_no_transp b_shadow">
                <thead>
                    <tr>
                        <th scope="col">
                            <?php
                            if($filter == "notTermined"){
                                echo '<a href="commandes.php?filter=notTermined&paramFilter=true&orderBy=ref_tcs">
                                Ref Commande<br>Tcs '.$iconFiltre.'</a>';
                            }else if($paramFilter == "non cloturées"){
                                echo '<span>Ref Commande<br>Tcs</span>';
                            }else{
                                echo '<a href="commandes.php?month='.$month.'&year='.$year.'&filter=orderBy&paramFilter=ref_tcs">
                                Ref Commande<br>Tcs '.$iconFiltre.'</a>';
                            }?>                            
                        </th>
                        <th scope="col">
                            <?php
                            if($filter == "notTermined"){
                                echo '<a href="commandes.php?filter=notTermined&paramFilter=true&orderBy=ref_client">Ref Commande<br>client '.$iconFiltre.'</a>';
                            }else if($paramFilter == "non cloturées"){
                                echo '<span>Ref Commande<br>client</span>';
                            }else{
                                echo '<a href="commandes.php?month='.$month.'&year='.$year.'&filter=orderBy&paramFilter=ref_client">Ref Commande<br>client '.$iconFiltre.'</a>';
                            }?>
                        </th>
                        <th scope="col">
                            <?php
                            if($filter == "notTermined"){
                                echo '<a href="commandes.php?filter=notTermined&paramFilter=clientName">Client '.$iconFiltre.'</a>';
                            }else if($paramFilter == "non cloturées"){
                                echo '<span>Client</span>';
                            }else{
                                echo '<a href="commandes.php?month='.$month.'&year='.$year.'&filter=orderBy&paramFilter=clientName">Client '.$iconFiltre.'</a>';
                            }?>
                        </th>
                        <th scope="col">Date de <br>
                        reception</th>
                        <th scope="col">Date de <br>
                        livraison</th>
                        <th scope="col">Listing <br>
                        pieces</th>
                        <th scope="col">
                            <?php
                            if($filter == "notTermined"){
                                echo '<a href="commandes.php?filter=notTermined&paramFilter=true&orderBy=statut">Statut <br>
                                commande '.$iconFiltre.'</a>';
                            }else if($paramFilter == "non cloturées"){
                                echo '<span>Statut <br>commande </span>';
                            }else{
                                echo '<a href="commandes.php?month='.$month.'&year='.$year.'&filter=orderBy&paramFilter=statut">Statut <br>commande '.$iconFiltre.'</a>';
                            }?>                            
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach($orders as $order):
                        $client = $order->getClientInfos();
                        $piecesOrder = $order->getPieces();
                        $statutOrder = $order->watchStatut();
                        $clientMails = $client->extractMultipleMails();
                        $clientTels = $client->extractMultipletels();
                        $pieceWithCounterFound = $order->isCounterRunningOnAPieceInOrder()==true?'<i class="fas fa-hourglass-half"></i>':'';
                        
                        $pieceWithUrgencePlusFound = $order->isUrgencePlusOnAPieceInOrder()==true?'<i class="fas fa-tachometer-alt"></i>':'';
                    ?>
                        <tr data-id="<?= $order->getId();?>" class="<?= $statutOrder;?>">
                            <td>
                                <?php
                                    $refTcs = $order->getRefTcs();
                                    if(isAdmin()){
                                        echo "<a href='/".$baseUrl."admin.php?refTcs=$refTcs' class='textBlack'>
                                        ".$refTcs."</a>";
                                    }else{
                                        echo $refTcs;                                        
                                    }
                                ?>
                            </td>
                            <td><?= $order->getRefClient();?></td>
                            <td class="tdClient">
                                <table class="tableInfosClient">  
                                    <tr>
                                        <td><i class="fas fa-envelope-square"></i></td>
                                        <td>
                                        <?php 
                                            if($clientMails):
                                                foreach($clientMails as $mail): ?>
                                                    -<a href="mailto:<?= $mail;?>">
                                                    <?= $mail;?></a><br>
                                        <?php   endforeach; 
                                            endif;?>
                                        </td>
                                    </tr>                                    
                                    <tr>
                                        <td><i class="fas fa-phone-square"></i></td>
                                        <td class="capitalise">
                                        <?php 
                                            if($clientTels):
                                                foreach($clientTels as $tel): ?>
                                                    - <?= str_replace(':',':<br>',ucfirst($tel));?><br>
                                        <?php   endforeach; 
                                            endif;?>
                                        </td>                                    
                                    </tr>                                    
                                    <tr>
                                        <td><i class="fas fa-map-marked-alt"></i></td>
                                        <td><?= nl2br($client->getAdresse());?></td>
                                    </tr>
                                    <tr>
                                        <td><i class="fas fa-user-tag"></i></td>
                                        <td><?= $client->getType();?></td>                                    
                                    </tr>
                                </table>
                                <span class="clientName"><?= $client->getNom().' '.$iconFiltre;?></span>
                            </td>
                            <td><?= date('d/m/Y', strtotime($order->getDateRecept()));?></td>
                            <td><?= date('d/m/Y', strtotime($order->getDateLivr()));?></td>
                            
                            <td class="width560"><span class="spanIconesClignotement"><?= $pieceWithCounterFound . $pieceWithUrgencePlusFound;?></span><span class="tdPieces"><?= count($piecesOrder);?> Ref(s) sur commande <i class="fas fa-sort-down"></i></span>
                            <?php
                            if(isAdmin()){
                                echo '<button class="btn_shadow clotureAllPieces" style="margin-left:10px;" data-orderId="'. $order->getId().'" data-lockedMode="1">
                                <i class="fas fa-lock textOrange"></i>
                                </button>'; 
                                
                                echo '<button class="btn_shadow clotureAllPieces" style="margin-left:10px;" data-orderId="'. $order->getId().'" data-lockedMode="0">
                                <i class="fas fa-lock-open textBlue"></i></i>
                                </button>';
                            }
                            ?>
                                <ul class="listePieceCommande">
                                    <?php 
                                    $nbPieces = count($piecesOrder);
                                    $nbPiecesClotured = 0;
                                    
                                    foreach($piecesOrder as $piece): 
                                        $idPiece = $piece->getId();
                                    
                                        $idC = null;
                                        $classCounterRunning = "";
                                        $counterUser = null;
                                        if($pieceWithCounterFound){
                                            $counterUser = $counterMng->getCompteurForPieceAndUser($idPiece,$_SESSION['user']);
                                            if($counterUser){
                                                $idC = $counterUser->getId(); 
                                                $classCounterRunning = ' startCountTime';
                                            }
                                        }
                                                                        

                                        $statut = $piece->getStatut();
                                        $statut1 = null;$statut2 = null;$statut3 = null;$statut4 = null;$statut5 = null;$statut6 = null;$statut7 = null;$statut8 = null;$statut9 = null;$statut10 = null;$statut11 = null;
                                        switch ($statut){
                                            case "En attente":
                                                $statut1 = "selected";
                                                break;
                                            case "En cours":
                                                $statut2 = "selected";
                                                break;
                                            case "Stoppe":
                                                $statut3 = "selected";
                                                break;
                                            case "Termine":
                                                $statut4 = "selected";
                                                break;
                                            case "En peinture":
                                                $statut5 = "selected";
                                                break;
                                            case "A l'acide":
                                                $statut6 = "selected";
                                                break;
                                            case "Livre":
                                                $statut7 = "selected";
                                                break;
                                            case "Au laser":
                                                $statut8 = "selected";
                                                break;
                                            case "Pour peinture":
                                                $statut9 = "selected";
                                                break;
                                            case "Pour soudure":
                                                $statut10 = "selected";
                                                break;
                                            case "Attente matiere":
                                                $statut11 = "selected";
                                                break;
                                        }
                                        $urgence = "";  
                                        $infos ="";
                                        $infosUnique ="";
                                        $locked ="";
                                        $classClotured = "";
                                        $disabledBtn = "";
                                        if($piece->getUrgente() == 1){
                                            $urgence = '<i class="fas fa-tachometer-alt" title="Piece urgente"></i>  ';
                                        }
                                        if(!empty($piece->getInfos())){
                                            $infos = '<i class="fas fa-exclamation-triangle" title="'.$piece->getInfos().'"></i>  ';
                                        }
                                        if(!empty($piece->getInfosUnique())){
                                            $infosUnique = '<i class="fas fa-info-circle" title="'.$piece->getInfosUnique().'"></i>  ';
                                        }
                                        if($piece->getIsCloture()){ 
                                            $nbPiecesClotured++;
                                            $classClotured = "clotured";
                                            $disabledBtn = "disabled";
                                            $locked = '<i class="fas fa-lock" title="Piece cloturée !"></i>  ';
                                        }

                                        ?>

                                        <li data-idPiece="<?= $idPiece;?>" class="<?= $classClotured;?>">
                                        
                                         <?php 
                                            $urgencePlus = 0;                                            
                                            if($piece->getUrgencePlus() == 1){
                                                $urgencePlus = 1;
                                            }    
                                         ?>
                                           
                                            <button class="btn_shadow urgencePlus" data-urgencePlus="<?= $urgencePlus;?>"><i class="fas fa-tachometer-alt" title="Piece urgente"></i></button>
                                         
                                         
                                         
                                          <a href="<?= $root."pages/piece.php?idPieceCommande=".$idPiece."&idCommande=".$order->getId();?>" class="linkRefPiece" target="_blank">
                                           <table>
                                               <tbody>
                                                   <tr>
                                                       <td class='<?= $classClotured;?>'>
                                                       
                                                       
                                                       <?= $urgence.$infos.$infosUnique.$locked.$piece->getRef();?></td>
                                                       <td class="textBlack"><?=$piece->getQt();?>Pcs</td>
                                                   </tr>
                                               </tbody>
                                           </table>
                                           </a>  

                                            <?php
                                            if(!$piece->getIsCloture()):
                                            ?>
                                                <button class="btn btn_lightBlue StartTimer <?= $classCounterRunning;?>" title="Demarer le chronometre de temps pour la piece">
                                                    <i class="fas fa-stopwatch"></i>
                                                </button>
                                                <button class="btn btn_valid StopTimer" data-idCounteur="<?= $idC;?>" title="Stopper le chronometre de temps pour la piece">
                                                    <i class="fas fa-stop"></i>
                                                </button>

                                                <form class="pieceForm addTimeForm">
                                                    <div class="bordered inline">
                                                    <input type="checkbox" name="heuresSup" title="cocher pour declarer votre temps en heures supplementaires !"><i class="fas fa-level-up-alt"></i>
                                                    <input type="number" name="addTime" step="0.25" placeholder="Ajout tps" size="5" title="0.25 = 15min
        0.50 = 30min
        0.75 = 45min">
                                                    </div>
                                                    <button type="button" class="addInfosPiece inline"><i class="fas fa-info-circle"></i></button>
                                                    <textarea name="infosPiece" cols="30" rows="10" placeholder="Ecrire 'alert + texte' pour que le texte soit en rouge"></textarea>
                                                    <button type="submit" class="btn btn_valid" <?= $disabledBtn;?>><i class="fas fa-save" title="Sauvegarde votre temps et les infos de fabrication."></i></button>
                                                </form>
                                            <?php endif;?>

                                            <form action="" class="pieceForm statusPieceForm">
                                                <select name="statusPiece">
                                                    <option value="En attente" <?= $statut1; ?>>En attente</option>
                                                    <option value="En cours" <?= $statut2; ?>>En cours</option>
                                                    <option value="Au laser" <?= $statut8; ?>>Au laser</option>
                                                    <option value="Stoppe" <?= $statut3; ?>>Stoppé</option>
                                                    <option value="Pour soudure" <?= $statut10; ?>>Pour soudure</option>
                                                    <option value="Attente matiere" <?= $statut11; ?>>Att. matiere</option>
                                                    <option value="Pour peinture" <?= $statut9; ?>>Pour peinture</option>
                                                    <option value="Termine" <?= $statut4; ?>>Terminé</option>
                                                    <option value="En peinture" <?= $statut5; ?>>En peinture</option>
                                                    <option value="A l'acide" <?= $statut6; ?>>A l'acide</option>
                                                    <option value="Livre" <?= $statut7; ?>>Livré</option>
                                                </select>                                    
                                            </form>
                                        </li>  
                                        <hr>
                                    <?php endforeach;?>
                                </ul>                        
                            </td>
                            
                            <?php                            
                            $classStatut = "";                            
                            if(($order->watchStatut()=="statutRetard" or $order->watchStatut()=="statutUrgent") 
                               && $order->watchStatut(true)=="statutEnCours"){
                                $classStatut = "statutEnCours";
                            }elseif($order->watchStatut(true)=="statutAttenteMatiere"){
                                $classStatut = "statutAttenteMatiere";
                            }else{
                                $classStatut = $order->watchStatut(true);
                            } 
                            
                            $lockOrder = "";
                            if($nbPiecesClotured == $nbPieces && $nbPiecesClotured>0){
                                $lockOrder = '<i class="fas fa-user-lock textOrange"></i>';
                            }
                        
                            ?>
                            <td class="orderStatut <?= $classStatut;?>"><?= $order->getStatut().'  '.$lockOrder;?></td>
                        </tr>  
                    <?php endforeach;?>                  
                </tbody>
            </table>                   
        </div>        
        
        <?php include(__DIR__."/../include/footer.php") ?>
    </section>
    <?php include(__DIR__."/../include/footerJs.php") ?>
    
    
    
    
</body>
</html>