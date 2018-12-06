var pressePapier = null;
$(document).ready(function() {

    $(".copier").click(function() {
        var ligneEchantillon = $(this).closest("tr");
        pressePapier = {
            animal: ligneEchantillon.find(".animal").prop("selectedIndex"),
            animalAutre: ligneEchantillon.find(".animalAutre").val(),
            identification: ligneEchantillon.find(".identAnimal").val(),
            tissu: ligneEchantillon.find(".organe").prop("selectedIndex"),
            organeAutre: ligneEchantillon.find(".organeAutre").val(),
            inclusion: ligneEchantillon.find(".inclusion").prop("selectedIndex"),
            coupe: ligneEchantillon.find(".coupe").prop("selectedIndex"),
            coloration: ligneEchantillon.find(".coloration").prop("selectedIndex")
        };
    });

    $(".coller").click(function() {
        if(pressePapier)
        {
            var ligneEchantillon = $(this).closest("tr");
            ligneEchantillon.find(".animal").prop("selectedIndex", pressePapier.animal);
            ligneEchantillon.find(".animalAutre").val(pressePapier.animalAutre);
            ligneEchantillon.find(".identAnimal").val(pressePapier.identification);
            ligneEchantillon.find(".organe").prop("selectedIndex", pressePapier.tissu);
            ligneEchantillon.find(".organeAutre").val(pressePapier.organeAutre);
            ligneEchantillon.find(".inclusion").prop("selectedIndex", pressePapier.inclusion);
            ligneEchantillon.find(".coupe").prop("selectedIndex", pressePapier.coupe);
            ligneEchantillon.find(".coloration").prop("selectedIndex", pressePapier.coloration);
            //On actualise les champs
            ligneEchantillon.find(".organe, .animal").change();
        }
    });

    $(".appliquerPartout").click(function() {
        var ligneEchantillon = $(this).closest("tr");
        $(".echantillon .animal").prop("selectedIndex", ligneEchantillon.find(".animal").prop("selectedIndex"));
        $(".echantillon .animalAutre").val(ligneEchantillon.find(".animalAutre").val());
        $(".echantillon .organe").prop("selectedIndex", ligneEchantillon.find(".organe").prop("selectedIndex"));
        $(".echantillon .organeAutre").val(ligneEchantillon.find(".organeAutre").val());
        $(".echantillon .inclusion").prop("selectedIndex", ligneEchantillon.find(".inclusion").prop("selectedIndex"));
        $(".echantillon .coupe").prop("selectedIndex", ligneEchantillon.find(".coupe").prop("selectedIndex"));
        $(".echantillon .coloration").prop("selectedIndex", ligneEchantillon.find(".coloration").prop("selectedIndex"));
        //On actualise les champs
        $(".animal, .organe").change();
    });

    $(".animal, .organe").change(function() {
        if($(this).val() == -1)
        {
            $(this).nextAll("input").removeClass("hidden").focus();
        }
        else {
            $(this).nextAll("input").addClass("hidden");
        }
    });

    $("input[name=import]").on('change', function () {
        const input = $(this);

        const submit = $("input#send-import-btn");
        if (input.val() != null) {
            submit.prop( "disabled", false );
            submit.addClass('btn-primary');
            input.prop( "disabled", true );
        } else {
            submit.prop( "disabled", true );
            submit.addClass('btn-default');
            submit.removeClass('btn-primary');
            input.prop( "disabled", false );
        }
    })
});
