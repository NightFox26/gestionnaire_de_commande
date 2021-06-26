<?php
include(__DIR__."/include/header.php");
?>

<body id="admin">
    <section class="bg adminBg">
        <?php
        include(__DIR__."/include/nav-users.php");        
        include(__DIR__."/include/nav-menu.php");
        include(__DIR__."/include/nav-admin.php");
        include(__DIR__."/include/clock.php"); 
        include(__DIR__."/include/modalAlert.php");
        ?>

        <div class="adminWrapper">
            <?php
            $cree_par = "bureau";
            include(__DIR__."/admin/forms/commandesForm.php");        
            include(__DIR__."/admin/forms/personnelsForm.php");
            include(__DIR__."/admin/forms/footerForm.php");        
            ?>
        </div>

        <?php include(__DIR__."/include/footer.php") ?>
    </section>
    <?php include(__DIR__."/include/footerJs.php") ?>
</body>
</html>