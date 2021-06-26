$(function () {
    /* gestion de la commandes box */ 
    $("#addOrderForm").draggable({
        handle: "h2.handleDrag",
        containment: "parent"
    });
    
    $("#listPiecesOrder").draggable({
        handle: "h2.handleDrag",
        containment: "parent"
    });
    
    $("#ordersBtnAdmin").click(function(){
        $("#addOrderForm").show(300);
    });
    
    $("#closeCommandeForm").click(function(){
        $("#addOrderForm, #listPiecesOrder").hide(300);
    }); 
    
    $("#admin #addOrderForm").show();
    $("#admin #listPiecesOrder").hide();
    $("#viewPiecesCommandeForm").click(function(){
        $("#listPiecesOrder").toggle(300);
    });
    /********************************/

    /* gestion de la ouvriers box */  
    $("#admin .ouvriersManage").draggable({
        handle: "h2.handleDrag",
        containment: "parent"
    });
    
    $("#admin #usersBtnAdmin").click(function(){
        $("#admin .ouvriersManage").toggle(300).css("display","inline-block");
    });
    
    $("#admin #closePersonnelsForm").click(function(){
        $("#admin .ouvriersManage").hide(300);
    });    
    /********************************/ 
    
    /* gestion de la footer box */    
    $("#admin .footerManage").draggable({
        handle: "h2.handleDrag",
        containment: "parent"
    });
    
    $("#admin #footerBtnAdmin").click(function(){
        $("#admin .footerManage").toggle(300).css("display","inline-block");
    });
    
    $("#admin #closeFooterForm").click(function(){
        $("#admin .footerManage").hide(300);
    });    
    /********************************/ 
    
        

});