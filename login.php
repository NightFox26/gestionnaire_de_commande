<?php   
require(__DIR__."/include/fonctions.php");
getAutoloader();
verifIpAccesIntranet();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>TCS gestion com</title>
    <link rel="icon" type="image/png" href="image/logo.png" />
    <link rel="stylesheet" href="styles.css">
</head>

<body style="display:block;">

    <section id="login" class="bg">
        <form action="" method="post">
            <img src="image/logotcs.png" alt="logo tcs" width="200" class="b_shadow">
            <?php
            if(isset($_POST["pass"])){        
                if($_POST["pass"] == "tcs26"){
                    //require("/include/fonctions.php");
                    //getAutoloader();
                    $bddMng = new Manager();        
                    $bdd = $bddMng->dbConnect();
                    $userMng = new UserManager($bdd);
                    $user = $userMng->getFirstUser();
                   
                    session_start();                    
                    $_SESSION["user"] = trim($user->getNom());            
                    $_SESSION["idUser"] = $user->getId(); 
                    $_SESSION["statutUser"] = trim(htmlspecialchars($user->getStatut())); 
                    header("location:index.php");            
                }else{
                    echo "<p>Mot de passe incorrect !<p>";
                }
            }
            ?>
            <input type="password" name="pass" placeholder="Mot de passe">
            <br>
            <input type="submit" value="Valider" class="btn">
        </form>
    </section>
    
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script>
        $(function(){
           $("form input[type='password']").focus(); 
        });
    </script>
</body>

</html>