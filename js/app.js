var cookieStorage = localStorage;
var baseUrl = document.querySelector('html').getAttribute('data-base');

$(function () {
    var timerRefreshPage = 120000;
        
    /* gestion de la boite d'ajout de commande coté atelier */
    //$("#addOrderForm, #listPiecesOrder").hide();
    $("#addOrderAtelierBtnMenu").click(function(){
        $("#addOrderForm").show(300);
    });
    
    
    /***************************************************/

    /* gestion de la tchat box */
    $("#chatBox").draggable({
        handle: "h2",
        containment: "parent"
    });

    $("#chatBtnMenu").click(function(){
        $("#chatBox").toggle(300,function(){ 
            if(window.matchMedia("(min-width: 1281px)").matches){
                $("#chatBox input[type='text']").focus();                
            }
            cookieStorage.setItem('nbMessageLu', $(this).find(".messageListe li").eq(0).attr('data-idmsg')); 
        });
    });
    
    $("#closeChat").click(function(){
        $("#chatBox").hide(300);
    });    
    /********************************/    
    
    /* gestion de la boite de recherche */    
    $("#searchBox").draggable({
        handle: "h2",
        containment: "parent"
    });
    
    $("#searchBtnMenu").click(function(){
        $("#searchBox").toggle(300);
        $("#searchBox input[type='text']").focus();
    });
    
    $("#closeSearch").click(function(){
        $("#searchBox").hide(300);
    });    
    /**************************************/
    
    /* gestion de la todo liste */    
    $("#todoBox").draggable({
        handle: "h2",
        containment: "parent"
    });
    
    $("#todoBtnMenu").click(function(){
        $("#todoBox").toggle(300);
        if(window.matchMedia("(min-width: 1281px)").matches){
            $("#todoBox input[type='text']").focus();
        }
    });
    
    $("#closeTodo").click(function(){
        $("#todoBox").hide(300);
    });    
    /**************************************/
    
    //click sur les utilisateurs dans le footer
    $("footer .userTask").click(function(){
        $("#todoBox").css("display","block");
    });
    
            
    /* gestion du bouton infos fabrication sur commande -> pieces */     
    var btnInfosPieces = $("#commandesPage .listePieceCommande .addTimeForm .addInfosPiece");    
    $(btnInfosPieces).click(function(){
       var textarea = $(this).next();
       $(textarea).slideToggle(200).focus();       
    });  
    
    
    $("#commandesPage .tdPieces").click(function(e){
        e.stopImmediatePropagation();
        $(this).parent().find(".listePieceCommande").slideToggle(300);
    });
    
    $("#commandesPage .tdClient .clientName").click(function(e){
        e.stopImmediatePropagation();
        $(this).parent().find('.tableInfosClient').fadeToggle(400);
    });    
    
    $("#commandesPage").find("input, button, textarea, select").click(function(e){
        e.stopPropagation();
    });    
    
    $("#commandesPage table tr").click(function(e){
        e.stopImmediatePropagation();
        if($(this).find(".tdPieces").parent().find(".listePieceCommande").css("display") == "block" ||
          $(this).find(".tdClient .clientName").parent().find('.tableInfosClient').css("display")=="table"){
            $(this).find(".tdPieces").parent().find(".listePieceCommande").hide(300);
            $(this).find(".tdClient .clientName").parent().find('.tableInfosClient').hide(400);            
        }else{
            $(this).find(".tdPieces").parent().find(".listePieceCommande").show(300);
            $(this).find(".tdClient .clientName").parent().find('.tableInfosClient').show(400);
        }
    });
    
    /* si on est sur la page commande on refresh la page toute les 2minutes si la souris ne bouge pas */
    var url = window.location.href;     
    if(url.match(/commandes.php/g) && window.matchMedia("(min-width: 1281px)").matches){ 
        var lastMove = new Date().getTime() ,currentTime;
        $(window).mousemove(function(){
          lastMove = new Date().getTime();
        });
        
        setInterval(function(){
        currentTime = new Date().getTime();
        if(currentTime - lastMove > timerRefreshPage)
            location.reload();       
        },500);
     }
    

    

    

    
    
  
    
    
    
    
    
    
    
    
    
    
    
    
    

});