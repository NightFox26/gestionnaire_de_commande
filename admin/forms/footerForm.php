<?php
$footer = $footerMng->getFooter();
$radioTextSelected = $footer->getMode()=="text"? "checked":"";
$radioTasksSelected = $footer->getMode()=="tasks"? "checked":"";
?>

<section class="tcs-container b_shadow footerManage">
    <button id="closeFooterForm" class="closeBtn"><i class="fas fa-times"></i></button>
    <h2 class="handleDrag">Message pied de page</h2>
    
    <form action="">
        <textarea name="text" cols="30" rows="10"><?= $footer->getText();?></textarea>

        <div>
            <input type="radio" id="footerChoice1" name="mode" value="text" <?= $radioTextSelected;?>>
            <label for="footerChoice1">Texte ci dessus</label>

            <input type="radio" id="footerChoice2" name="mode" value="tasks" <?= $radioTasksSelected;?>>
            <label for="footerChoice2">Dernieres taches</label>
            <button type="submit" class="btn btn_valid float_right"><i class="fas fa-save"></i></button>
        </div>

    </form>
</section>