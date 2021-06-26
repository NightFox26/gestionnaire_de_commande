$(function () {    
        
    /* supprime la piece dans la commande coté bdd */
    $("#piecePage #deletePiece").on("click",function(){
        var idPiece = $(this).attr("data-idPiece");
        var idPieceOrder = $(this).attr("data-idPieceCommande");
        var pieceHasTime = $(this).hasClass("times");
        var pieceIsInOrder = $(this).hasClass("inOtherOrders");
        var needConfirm = false;
        var mdp = "";
        
        if(pieceHasTime){
            needConfirm = true;
            mdp = prompt("Cette piece a deja du temps d'inscrit !!!\nQuel est votre mot de passe ?");            
        }
        
        if(pieceIsInOrder){
            needConfirm = true;
            mdp = prompt("Cette piece est dans une ou plusieurs commandes, si vous la suppimez elle sera supprimé dans les commandes qui la possedent !!!\nQuel est votre mot de passe ?");            
        }
        
        $.ajax({
                type: "POST",
                url: baseUrl+"/admin/formsValidate/piecesOrdersFormsValidate.php",
                data: {idPieceDel:idPiece,
                       idPieceOrderDel:idPieceOrder,
                       needConfirm: needConfirm,
                       mdp:mdp},
                dataType: 'json',
                async:false,
                global:false,
                success: function(data){
                    if(data.error){
                        alert(data.error);
                    }else{
                        location.reload();
                    }
                },
                error: function(res,statut,e){
                    alert("Erreur sur la requete ajax de supression de la piece dans la page 'Piece' : "+e);
                    console.log(res);
                }
            }); 
    });
   
        
    /* Sauvegarde les modification dans la page "piece" */
    $("#piecePage").find('#savePiece,#savePiece2').click(function(e){
        var idPiece = $(this).attr("data-idPiece");
        var idPieceOrder = $(this).attr("data-idPieceCommande");
        e.preventDefault();
        
        var formDatas = new FormData($(this).parents('.tcs-container').find('form.formDataPiece')[0]);
        formDatas.append('updatePiece',true);
        formDatas.append('idPieceUp',idPiece);
        formDatas.append('idPieceOrderUp',idPieceOrder);
                
        for (var pair of formDatas.entries()) {
            console.log(pair[0]+ ', ' + pair[1]);            
        }
                                      
        $.ajax({
            type: "POST",
            url: baseUrl+"/admin/formsValidate/piecesOrdersFormsValidate.php",
            data: formDatas,
            dataType: 'json', 
            processData: false,
            contentType: false,
            success: function(dataPiece){
                console.log(dataPiece)
                if(dataPiece.error){
                    alert(dataPiece.error);
                }else{
                    location.reload();
                    console.log("piece updated !!!");
                } 
            },
            error: function(res,statut,e){
                alert("Erreur sur la requete ajax de mise a jour de la piece dans la page 'piece' : "+e);
                console.log(res);
            }
        });
        
    });   
    
})