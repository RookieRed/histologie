var lastChecked = null;
$(document).ready(function() {
    $(".validerLigne").click(function() {
        var etape = $("#etape").val();
        var ligne = $(this).closest("tr");
        var date = ligne.find("input.datepicker").val();
        var commentaire = null;
        if(ligne.find("textarea").length)
        {
            commentaire = ligne.find("textarea").val();
        }
        $.ajax({
            url: "ajax/suiviEtape.php?etape=" + etape,
            method: "POST",
            data: {
                dates: [date],
                lignesIds: [ligne.data("id")],
                commentaires: [commentaire]
            },
            dataType: "json"
        }).done(function(result) {
            if(result.success)
            {
                swal("OK!", result.message, "success");
                ligne.remove();
            }
            else {
                swal("Erreur!", result.message, "error");
            }
        });
    });

    $('.supprimerLigne').click(function () {
        var etape = $("#etape").val();
        if (parseInt(etape) !== 1) {
            console.log('Must be the first step');
            return;
        }
        var ligne = $(this).closest("tr");
        swal({
            title: 'Confirmation de suppression',
            text: 'Voulez vous vraiment supprimer cette commande ?',
            icon: 'warning',
            buttons: ['Annuler', 'Supprimer'],
            dangerMode: true
        })
        .then(function(toBeDeleted) {
            if (toBeDeleted) {
                $.ajax({
                    url: "ajax/suiviEtape.php?idCommande=" + ligne.data("id"),
                    method: "DELETE",
                    dataType: "json"
                })
                .done(function(result) {
                    if(result.success) {
                        swal("OK!", result.message, "success");
                        ligne.remove();
                    } else {
                        swal("Erreur!", result.message, "error");
                    }
                });
            }
        })
    });

    $("input[type='checkbox']").click(function(e) {
        if(e.shiftKey && lastChecked !== null && lastChecked != this)
        {
            var checkboxes = $("input[type='checkbox']");
            if($(this).is(":checked"))
            {
                var first = checkboxes.index(lastChecked);
                var last = checkboxes.index(this);
                checkboxes.slice(Math.min(first, last), Math.max(first, last) + 1).prop("checked", true);
                console.log(first + " " + last);
                console.log(checkboxes.slice(Math.min(first, last), Math.max(first, last) + 1) );
            }
        }
        else if($(this).is(":checked"))
        {
            lastChecked = this;
        }
    });

    $("#selectionnerLignes").click(function() {
        $("input[type='checkbox']").prop("checked", $(this).is(":checked"));
    });

    $("button.validerLignes").click(function() {
        var etape = $("#etape").val();
        var lignesIds = [];
        var dates = [];
        var commentaires = [];
        //On mémorise les lignes pour la suppression en cas de succès
        var lignes = [];
        $("input[type='checkbox']:checked:not(#selectionnerLignes)").each(function() {
            var ligne = $(this).closest("tr");
            lignesIds.push(ligne.data("id"));
            dates.push(ligne.find("input.datepicker").val());
            if(ligne.find("textarea").length)
            {
                commentaires.push(ligne.find("textarea").val());
            }
            lignes.push(ligne);
        });
        $.ajax({
            url: "ajax/suiviEtape.php?etape=" + etape,
            method: "POST",
            data: {
                dates: dates,
                lignesIds: lignesIds,
                commentaires: commentaires
            },
            dataType: "json"
        }).done(function(result) {
            if(result.success)
            {
                swal("OK!", result.message, "success");
                lignes.forEach(function(el) {
                    el.remove();
                });
            }
            else {
                swal("Erreur!", result.message, "error");
            }
        });
    });
});
