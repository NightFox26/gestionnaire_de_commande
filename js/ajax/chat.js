$(function(){ 
        
    $("#chatBox form").submit(function(e){
        e.preventDefault();
        var formData = $(this).serialize();
        console.log(formData);
                        
        $.ajax({
            type: "POST",
            url: baseUrl+"/admin/formsValidate/chatFormsValidate.php",
            data: formData,
            dataType: 'json',
            success: function(data){
                console.log(data);
                console.log("le message du tchat a été ajouté !");
                $("#chatBox form")[0].reset();
                $('#chatBox .messageListe ul').prepend(addMessageChat(data.id,data.nom,data.date,data.text));
                cookieStorage.setItem('nbMessageLu', $('#chatBox .messageListe li').eq(0).attr('data-idmsg'));
            },
            error: function(res,statut,e){
                console.log("Erreur sur la requete ajax du formulaire 'ajout message chat' : "+e);
                console.log(res);
            }
        });        
    });
    
    function addMessageChat(id,nom,date,text){
        return "<li data-idMsg='"+id+"'><date>"+date+"</date><br><i class='fas fa-user'></i><span class='user'>"+nom+" :</span>"+text+"</li>";
    } 
    
    function checkNewMessages(){
        $.ajax({
            type: "GET",
            url: baseUrl+"admin/getDatasAjax/getMessagesChat.php",
            data: {all:1},
            dataType: 'json',
            success: function(data){
                //console.log(data);
                var countNewMsg = 0;
                var countOldMsg = cookieStorage.getItem('nbMessageLu');
                $("footer #newMessageFooter").fadeOut();
                document.title = 'TCS Gestionnaire de commande';
                if(!data.no_msg){
                    $("#chatBox .messageListe ul").html("");                    
                    $.each(data, function(id,val) {                        
                        $('#chatBox .messageListe ul').append(addMessageChat(val.id,val.nom,val.date,val.text));                        
                    });
                    countNewMsg=$('#chatBox .messageListe li').eq(0).attr('data-idmsg');
                    if(countNewMsg>countOldMsg){
                        var deltaNewMsg = countNewMsg - countOldMsg;
                        $("footer #newMessageCount").text(deltaNewMsg);
                        $("footer #newMessageFooter").fadeIn(200);
                        document.title = deltaNewMsg+' nouveau(x) messages';
                    }
                }
            },
            error: function(res,statut,e){
                console.log("Erreur sur la requete ajax de recherche des nouveaux messages du chat : "+e);
                console.log(res);
            }
        });
    }
    
    checkNewMessages();
    setInterval(checkNewMessages,3000);
    
});