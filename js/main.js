//Fonction qui retourne la lettre passée en paramètre sans son accent
function replaceAccents(lettre)
{
    switch(lettre)
    {
        case "é":
        case "è":
        case "ê":
        case "ë":
            return "e";
        case "à":
        case "â":
        case "ä":
            return "a";
        case "î":
        case "ï":
            return "i";
        case "ô":
        case "ö":
            return "o";
        case "ñ":
            return "n";
        case "û":
        case "ù":
        case "ü":
            return "u";
        case "ç":
            return "c";
        default:
            return "";
    }
}

//Retourne une adresse mail inserm à partir du nom et du prénom passés en paramètre
function formatMail(prenom, nom)
{
    var identifiant = prenom + "." + nom;
    //On remplace les espaces par des tirets, on supprimer les apostrophe et on remplace les accents par leurs équivalents
    identifiant = identifiant.replace(/\ /g, "-").replace(/'/g, "").replace(/â|ä|à|é|è|ê|ë|î|ï|ô|ö|ù|ü|û|ñ|ç/g, replaceAccents).toLowerCase();
    return identifiant + "@inserm.fr";
}

$(document).ready(function() {
    //Activation des tooltips
    $("[data-toggle=tooltip]").tooltip();
    //Activation des datepickers
    $(".datepicker").datepicker({
        language: "fr",
        format: "dd-mm-yyyy"
    });
    //Activation des popovers d'aide
    $('.aide').popover({
        trigger: 'manual'
    });
    //Paramètrage des popovers
    $("#aide").focusin(function() {
        $(".aide").popover("show");
    });
    $("#aide").focusout(function() {
        $(".aide").popover("hide");
    });

    $(".modifierMdpUser").click(function() {
        //Récuperation de l'ID du responsable dont le mot de passe sera modifié
        var idPersonne = $(this).data("id-personne");
        //Affichge d'une alerte contenant un champs de type password
        swal({
            title: "Changer le mot de passe",
            text: "Entrez le nouveau mot de passe : ",
            content: {
                element: "input",
                attributes: {
                    placeholder: "Nouveau mot de passe",
                    type: "password",
                },
            },
            buttons: ['Annuler', 'Enregistrer']
        },
        function(mdp){
            if (mdp === false) return false;
            if (mdp === "") {
                swal({
                    title: "Erreur",
                    text: "Vous devez rentrer un mot de passe!",
                    icon: 'error',
                });
                return false
            }
            //Sauvegarde du nouveau mot de passe en ajax
            $.ajax({
                url : 'ajax/modifierMdpResponsable.php',
                type : 'POST',
                data : 'idPersonne=' + idPersonne +
                       '&mdp=' + mdp,
                dataType : 'json',
                success: function(result, statut) {
                    if(result.success)
                    {

                        swal({
                            title: "Succès !",
                            text: "Mot de passe modifié avec succès!",
                            icon: 'success',
                        });
                    }
                    else
                    {
                        swal({
                            title: "Erreur",
                            text: result.message,
                            icon: 'error'
                        });
                    }
                }
            });
        });
    });
});

function scrollToBottom(event) {
    event.preventDefault();
    if (Math.floor(window.innerHeight + window.scrollY) < document.body.offsetHeight - 1) {
        $("html, body").animate({scrollTop: document.body.scrollHeight}, "slow");
    }
}

$(window).on("scroll", function() {
    if (Math.floor(window.innerHeight + window.scrollY) >= document.body.offsetHeight - 1) {
        $('div.down-btn-container').addClass('disabled');
    } else {
        $('div.down-btn-container').removeClass('disabled');
    }
});
