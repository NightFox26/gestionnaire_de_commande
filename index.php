<?php   
include(__DIR__."/include/header.php");
?>

<body id="home">
    <section class="bg homeBg">
        
        <?php
        $cree_par = "atelier";
        include(__DIR__."/include/nav-users.php");        
        include(__DIR__."/include/nav-menu.php");
        include(__DIR__."/include/clock.php");        
        include(__DIR__."/admin/forms/commandesForm.php");
        ?>

        <a href="pages/commandes.php?month=<?= $thisMonth;?>&year=<?= $thisYear;?>" id="headerLink">
            <header class="backg_primary b_shadow">
                <h1>T . C . S</h1>
                <p>Gestionnaire de commandes</p>                
            </header>
        </a>
        
        <?php include(__DIR__."/include/footer.php") ?>
    </section>    
    <?php include(__DIR__."/include/footerJs.php") ?>    
</body>
</html>