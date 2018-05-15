var pressePapier = null;
$(document).ready(function() {
    $(".copier").click(function() {
        var ligne = $(this).closest("tr");
        pressePapier = {
            inclusion: ligne.find(".sensInclusion").prop("selectedIndex"),
            inclusionAutre: ligne.find(".inclusionAutre").val()
        };
    });

    $(".coller").click(function() {
        if(pressePapier)
        {
            console.log("BLAH");
            var ligne = $(this).closest("tr");
            ligne.find(".sensInclusion").prop("selectedIndex", pressePapier.inclusion);
            ligne.find(".inclusionAutre").val(pressePapier.inclusionAutre);
            $(".sensInclusion").change();
        }
    });

    $(".appliquerPartout").click(function() {
        var ligne = $(this).closest("tr");
        $(".echantillon .sensInclusion").prop("selectedIndex", ligne.find(".sensInclusion").prop("selectedIndex"));
        $(".echantillon .inclusionAutre").val(ligne.find(".inclusionAutre").val());
        $(".sensInclusion").change();
    });

    $(".sensInclusion").change(function() {
        if($(this).val() == -1)
        {
            $(this).nextAll("input").removeClass("hidden").focus();
        }
        else {
            $(this).nextAll("input").addClass("hidden");
        }
    });
});
