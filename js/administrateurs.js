$(document).ready(function() {
    $(".modifierMdp").click(function() {
        var idAdministrateur = $(this).closest("tr").data("id");
        swal({
            title: "Change le mot de passe",
            text: "Entrez le nouveau mot de passe :",
            icon: "info",
            content: {
                element: "input",
                attributes: {
                    placeholder: "Nouveau mot de passe",
                    type: "password",
                },
            },
            buttons: ['Annuler', 'Enregistrer']
        })
        .then(function(inputValue){
            if (inputValue === null) return false;

            if (inputValue === "") {
                swal('Erreur !',"Vous devez rentrer quelque chose!", 'error');
                return false;
            }
            $.ajax({
                url: "ajax/administrateurs.php?action=modifier",
                method: "POST",
                data: {
                    idAdministrateur: idAdministrateur,
                    password: inputValue
                },
                dataType: "json"
            }).done(function(result) {
                if(result.success)
                {
                    swal({
                        title: "Succès!",
                        text: result.message,
                        icon: "success"
                    });
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

    $(".supprimerAdmin").click(function() {
        var idAdministrateur = $(this).closest("tr").data("id");
        var ligne = $(this).closest("tr");
        swal({
            title: "Etes-vous sur?",
            icon: 'warning',
            dangerMode: true,
            text: "Voulez-vous vraiment supprimer cet administrateur?",
            buttons: ['Annuler', 'Supprimer']
        })
        .then(function(value) {
            if (value === true) {
                $.ajax({
                    url: "ajax/administrateurs.php?action=supprimer",
                    method: "POST",
                    data: {
                        idAdministrateur: idAdministrateur
                    },
                    dataType: "json"
                }).done(function (result) {
                    if (result.success) {
                        ligne.remove();
                        swal({
                            title: "Succès!",
                            text: result.message,
                            icon: "success"
                        });
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
    });

    $(".ajouterAdminModal").click(function() {
        $("#modalAjouterAdmin").modal();
    });

    $("#modalAjouterAdmin").on("shown.bs.modal", function() {
        $("#login").focus();
    });

    $("#ajouterAdmin").click(function() {
        var login = $("#login").val();
        var password = $("#password").val();
        $.ajax({
            url: "ajax/administrateurs.php?action=ajouter",
            method: "POST",
            data: {
                login: login,
                password: password
            },
            dataType: "json"
        }).done(function(result) {
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
    });
});
