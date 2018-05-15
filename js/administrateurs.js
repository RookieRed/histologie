$(document).ready(function() {
    $(".modifierMdp").click(function() {
        var idAdministrateur = $(this).closest("tr").data("id");
        swal({
            title: "Change le mot de passe",
            text: "Entrez le nouveau mot de passe :",
            type: "input",
            showCancelButton: true,
            closeOnConfirm: false,
            animation: "slide-from-top",
            inputType: "password"
        },
        function(inputValue){
            if (inputValue === false) return false;

            if (inputValue === "") {
                swal.showInputError("Vous devez rentrer quelque chose!");
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
                        type: "success"
                    });
                }
                else {
                    swal({
                        title: "Erreur!",
                        text: result.message,
                        type: "error"
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
            text: "Voulez-vous vraiment supprimer cet administrateur?",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Oui",
            cancelButtonText: "Annuler",
            closeOnConfirm: false
        },
        function() {
            $.ajax({
                url: "ajax/administrateurs.php?action=supprimer",
                method: "POST",
                data: {
                    idAdministrateur: idAdministrateur
                },
                dataType: "json"
            }).done(function(result) {
                if(result.success)
                {
                    ligne.remove();
                    swal({
                        title: "Succès!",
                        text: result.message,
                        type: "success"
                    });
                }
                else {
                    swal({
                        title: "Erreur!",
                        text: result.message,
                        type: "error"
                    });
                }
            });
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
                    type: "error"
                });
            }
        });
    });
});
