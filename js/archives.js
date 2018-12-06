$(document).ready(function() {

    const resetPopup = function() {
        $('#popup-container').hide();
        $('#recap-popup h2.title').text('');
        $('#recap-popup .infos').html('');
        $('#popup-container .commentaires').hide();
        $('#popup-container .commentaires .plateau p').text('');
        $('#popup-container .commentaires .utilisateur p').text('');
    };

    $('table.table-archives tbody tr > td:not(.btn-cases)').on('click', function(event) {
        const $tr = $($(this).parent());
        const idCommande = $tr.data('id');
        const endPoint = '/plateau/ajax/echantillons.php';

        $.ajax({
            url: endPoint,
            accept: 'application/json',
            dataType: 'json',
            method: 'GET',
            data: {
                idCommande: idCommande
            },

            success: function(response) {
                const commande = response.commande;
                // Titre
                $('#recap-popup h2.title').text('Commande ' + commande.numCommande);

                // Infos
                $('#recap-popup .infos').html('<p><b>Commande ' + ( commande.numCommande[0] == 'C' ? 'cryo' : 'parafine' ) + '</b> effectuée par <b>' + commande.utilisateur + '</b></p>');

                // Events
                $('#recap-popup .events');
                if (commande.dateCommande != null) {
                    $('#recap-popup .events .create .date').text(commande.dateCommande);
                    $('#recap-popup .events .create').removeClass('disabled');
                } else {
                    $('#recap-popup .events .create').addClass('disabled');
                    $('#recap-popup .events .create .date').text('en attente');
                }

                if (commande.dateReceptionCommande != null) {
                    $('#recap-popup .events .receive .date').text(commande.dateReceptionCommande);
                    $('#recap-popup .events .receive').removeClass('disabled');
                } else {
                    $('#recap-popup .events .receive').addClass('disabled');
                    $('#recap-popup .events .receive .date').text('en attente');
                }

                if (commande.dateRetourCommande != null) {
                    $('#recap-popup .events .return .date').text(commande.dateRetourCommande);
                    $('#recap-popup .events .return').removeClass('disabled');
                } else {
                    $('#recap-popup .events .return').addClass('disabled');
                    $('#recap-popup .events .return .date').text('en attente');
                }

                if (commande.dateFacturationCommande != null) {
                    $('#recap-popup .events .bill .date').text(commande.dateFacturationCommande);
                    $('#recap-popup .events .bill').removeClass('disabled');
                } else {
                    $('#recap-popup .events .bill').addClass('disabled');
                    $('#recap-popup .events .bill .date').text('en attente');
                }

                // Echantillons
                const $tbody = $('#recap-popup .echantillons tbody');
                for (let i=0; i < commande.echantillons.length; i++) {
                    const echantillon = commande.echantillons[i];
                    const tr = '<tr>'
                        + `<td>${(i+1)}</td>`
                        + `<td>${echantillon.typeAnimal}</td>`
                        + `<td>${echantillon.identAnimalEchantillon}</td>`
                        + `<td>${echantillon.nomOrgane}</td>`
                        + `<td>${echantillon.nomInclusion ? echantillon.nomInclusion : '/' }</td>`
                        + `<td>${echantillon.coupeText}</td>`
                        + `<td>${echantillon.lamesHTML}</td>`
                        + '</tr>';
                    $tbody.append(tr);
                }

                // Commentaires
                if (!commande.commentairePlateau && !commande.commentaireUtilisateur) {
                    $('#popup-container .commentaires').hide();
                } else {
                    $('#popup-container .commentaires').show();
                    if (commande.commentairePlateau) {
                        $('#popup-container .commentaires .plateau p').text(commande.commentairePlateau);
                        $('#popup-container .commentaires .plateau').show();
                    } else {
                        $('#popup-container .commentaires .plateau').hide();
                    }
                    if (commande.commentaireUtilisateur) {
                        $('#popup-container .commentaires .utilisateur p').text(commande.commentaireUtilisateur);
                        $('#popup-container .commentaires .utilisateur').show();
                    } else {
                        $('#popup-container .commentaires .utilisateur').hide();
                    }
                }

                // Ouverture de la popup
                let marginTop = $(event.target).offset().top - 250 > 100 ? $(event.target).offset().top - 250 : 100;
                $('#recap-popup').css('margin-top', marginTop + 'px');
                $('#popup-container').show();
            },

            error: function(xhr) {
                swal({
                    title: "Erreur serveur",
                    text: "Impossible d'afficher cette commande." + (xhr.responseJSON && xhr.responseJSON.errorMessage ? " Motif : '" + xhr.responseJSON.errorMessage + "'" : ''),
                    icon: 'error',
                });
            }
        });
    });

    $('#recap-popup span.close-btn, #popup-container').on('click', function () {
        resetPopup();
    });

    $('#popup-container *:not(span.close-btn)').on('click', function(e) {
        e.stopPropagation();
    });

});