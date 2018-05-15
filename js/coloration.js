var pressePapier = null;
$(document).ready(function() {
    $(".copier").click(function() {
        var ligne = $(this).closest("tr");
        pressePapier = {
            coloration: ligne.find(".coloration").prop("selectedIndex"),
            colorationAutre: ligne.find(".colorationAutre").val()
        };
    });

    $(".coller").click(function() {
        if(pressePapier !== null)
        {
            $(this).closest("tr").find(".coloration").prop("selectedIndex", pressePapier.coloration);
            $(this).closest("tr").find(".colorationAutre").val(pressePapier.colorationAutre);
            $(".coloration").change();
        }
    });

    $(".appliquerPartout").click(function() {
        var ligne = $(this).closest("tr");
        $(".echantillon .coloration").prop("selectedIndex", ligne.find(".coloration").prop("selectedIndex"));
        $(".echantillon .colorationAutre").val(ligne.find(".colorationAutre").val());
        $(".coloration").change();
    });

    $(".coloration").change(function() {
        if($(this).val() == "-1")
        {
            $(this).nextAll("input").removeClass("hidden").focus();
        }
        else {
            $(this).nextAll("input").addClass("hidden");
        }
    });
});
