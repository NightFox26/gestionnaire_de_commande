<section id="searchBox" class="backg_gray b_shadow">    
    <h2 class="handleDrag">Recherche</h2>
    <button id="closeSearch" class="closeBtn"><i class="fas fa-times"></i></button>
    
    <hr>
    <form action="<?=$root.'pages/search.php'?>" method="post">
        <input type="text" name="search" >
        <input type="submit" value="Chercher">
    </form>
</section>