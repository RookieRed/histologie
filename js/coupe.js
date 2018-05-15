var pressePapier = null;
$(document).ready(function() {
    $(".copier").click(function() {
        var ligne = $(this).closest("tr");
        var epaisseur = ligne.find(".epaisseur").val();
        var nbCoupes = ligne.find(".nbCoupes").val();
        var nbLames = ligne.find(".nbLames").val();
        pressePapier = {
            epaisseur: epaisseur,
            nbCoupes: nbCoupes,
            nbLames: nbLames
        };
    });
    $(".coller").click(function() {
        if(pressePapier)
        {
            var ligne = $(this).closest("tr");
            ligne.find(".epaisseur:enabled").val(pressePapier.epaisseur);
            ligne.find(".nbCoupes:enabled").val(pressePapier.nbCoupes);
            ligne.find(".nbLames").val(pressePapier.nbLames);
        }
    });
    $(".appliquerPartout").click(function() {
        var ligne = $(this).closest("tr");
        var epaisseur = ligne.find(".epaisseur").val();
        var nbCoupes = ligne.find(".nbCoupes").val();
        var nbLames = ligne.find(".nbLames").val();
        $(".echantillon .epaisseur:enabled").val(epaisseur);
        $(".echantillon .nbCoupes:enabled").val(nbCoupes);
        $(".echantillon .nbLames").val(nbLames);
    });
});
