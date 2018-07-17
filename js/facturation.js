$(document).ready(function() {
    $("button.validerFacturation").click(function() {
        var idsCommandes = [];
        $("input[type='checkbox']:checked").each(function() {
            idsCommandes.push($(this).closest("tr").data("id"));
        });
        $.ajax({
            url: "ajax/facturation.php",
            method: "POST",
            data: {
                idsCommandes: idsCommandes,
            },
            dataType: "json"
        }).done(function(result) {
            if(result.success)
            {
                swal("OK!", result.message, "success");
                $("input[type='checkbox']:checked").each(function() {
                    $(this).closest("tr").remove();
                });
            }
            else {
                swal("Erreur!", result.message, "error");
            }
        });
    });
    
    $("#selectionnerLignes").click(function() {
        $("input[type='checkbox']").prop("checked", $(this).is(":checked"));
    });

    $("input[type='checkbox']").click(function() {
        var args = "";
        $("input[type='checkbox']:checked").each(function() {
            args += "idsCommandes[]=" + $(this).closest("tr").data("id") + "&";
        });
        console.log(args);
        $("a.exporterFactures").attr("href", "exporterFactures.php?" + args);
    });
});
