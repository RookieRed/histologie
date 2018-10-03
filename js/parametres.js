function request(action, ressource, data, callback)
{
    $.ajax({
        url: "ajax/parametres.php?action=" + action + "&ressource=" + ressource,
        method: "POST",
        data: {data: data},
        dataType: "json"
    }).done(callback);
}

$(document).ready(function() {
    $(".ajouter").click(function() {
        var ressource = $(this).closest(".panel").data("ressource");
        var ligne = $(this).closest("tr");
        var nouveauNom = ligne.find('input').val();
        request("ajouter", ressource, {nom: nouveauNom}, function(result) {
            if(result.success)
            {
                location.reload();
            }
            else {
                swal({
                    title: "Erreur!",
                    text: result.message,
                    icon: "error"
                });
            }
        });
        ligne.find('input').val("");
    });

    $(".modifier").click(function() {
        var ligne = $(this).closest("tr");
        var id = ligne.data("id");
        var cell = ligne.find('td:first');
        var ressource = $(this).closest(".panel").data("ressource");
        if(cell.find('input').length === 0)
        {
            cell.html(
                "<input type=\"text\" value=\"" + cell.html().trim() + "\" size=\"10\">"
            );
        }
        else {
            var nouveauNom = cell.find('input').val();
            request("modifier", ressource, {id: ligne.data('id'), nom: nouveauNom}, function(result) {
                if(result.success)
                {
                    cell.html(nouveauNom);
                }
                else {
                    swal({
                        title: "Erreur!",
                        text: result.message,
                        icon: "error"
                    });
                }
            });
        }
    });

    $(".supprimer").click(function() {
        var ligne = $(this).closest("tr");
        var ressource = $(this).closest(".panel").data("ressource");
        swal({
            title: "Etes-vous sur?",
            text: "Voulez-vous vraiment supprimer cette donnée?",
            icon: "warning",
            buttons: ["Annuler", "Supprimer"]
        })
        .then(function(){
            request("supprimer", ressource, {id: ligne.data('id')}, function(result) {
                if(result.success)
                {
                    ligne.remove();
                }
                else {
                    swal({
                        title: "Erreur!",
                        text: result.message,
                        icon: "error"
                    });
                }
            });
        });
    });

    $(document).on('keydown', 'input', function(ev) {
        if(ev.which === 13) {
            $(this).closest('tr').find(".ajouter").click();
        }
    });
});
