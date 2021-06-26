$(function(){
    // insert todo in bdd
    $("#todoBox #submitTaskForm").submit(function(e){
        e.preventDefault();
        var formData = $(this).serialize();
                                
        $.ajax({
            type: "POST",
            url: baseUrl+"/admin/formsValidate/tasksFormsValidate.php",
            data: formData,
            dataType: 'json',
            success: function(data){
                $("#todoBox #submitTaskForm input[name='tache']").val("");
                console.log("la tache : "+data.tache+' de '+data.from+' pour '+data.for+' a été mis a jour !');
                getAllTasks();
            },
            error: function(res,statut,e){
                console.log("Erreur sur la requete ajax du formulaire 'insert task' : "+e);
                console.log(res);
            }
        });        
    });
    
    // change statut or delete todo in bdd
    $("#todoBox .todoListe ").on("click",".btnsTodo button.btn",function(e){
        var statut;
        var id = $(this).parents("li").attr("data-idTask");
        
        if($(this).hasClass("btn_done")){
            statut = "validate";
        }else if($(this).hasClass("btn_valid")){
            statut = "done";
        }else if($(this).hasClass("btn_danger")){
            statut = "delete";
        }
        console.log(id);
        console.log(statut);
                                
        $.ajax({
            type: "POST",
            url: baseUrl+"/admin/formsValidate/tasksFormsValidate.php",
            data: {id:id,statut:statut},
            dataType: 'json',
            success: function(data){                
                console.log("la tache : "+data.id+' a changé de statut vers : '+data.statut);
                getAllTasks();
            },
            error: function(res,statut,e){
                alert("Erreur sur la requete ajax 'modify statut task' : "+e);
                console.log(res);
            }
        });        
    });
    
    function addTaskHtml(id,fromUser,forUser,task,date,statut){
        return '<li data-idTask="'+id+'" class="statut-'+statut+'"><div class="btnsTodo"><button class="btn btn_done"><i class="fas fa-calendar-check"></i></button><button class="btn btn_valid"><i class="fas fa-check-circle"></i></button><button class="btn btn_danger"><i class="fas fa-times-circle"></i></button></div><i class="far fa-calendar-plus"></i>'+date+'<br><i class="fas fa-user"></i> <span> '+fromUser+' </span> <i class="fas fa-long-arrow-alt-right"></i><i class="fas fa-user"></i> <span> '+forUser+'</span> : <br><p class="tacheTexte">'+task+'</p></li><hr>';
    }
    
    // get all todo
    function getAllTasks(){
        $.ajax({
            type: "GET",
            url: baseUrl+"/admin/getDatasAjax/getTasks.php",
            data: {all:1},
            dataType: 'json',
            success: function(data){
                //console.log(data);
                $("#todoBox .todoListe ul").html("");
                if(!data.no_tasks){
                    $.each(data, function(id,data) {
                        $('#todoBox .todoListe ul').append(addTaskHtml(data.id,data.from,data.for,data.tache,data.date,data.statut));                        
                    });
                }
            },
            error: function(res,statut,e){
                console.log("Erreur sur la requete ajax d'affichage des tasks' : "+e);
                console.log(res);
            }
        });
    }
    
    getAllTasks();    
})