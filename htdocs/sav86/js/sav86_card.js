/**
 * Scripts SAV86 - Formulaire Carte (Create/Edit)
 */
$(document).ready(function() {
    // Configuration injectée par PHP
    var config = window.SAV86_CONFIG || { alertMdpVide: false };
    var dolRoot = window.DOL_URL_ROOT || '';
    var mode = window.SAV86_MODE || 'create';

    // 1. Datepickers
    $(".datepicker").datepicker({
        dateFormat: "dd/mm/yy",
        showOn: "button",
        buttonImage: dolRoot + '/theme/eldy/img/calendar.png',
        buttonImageOnly: true,
        buttonText: "Choisir date"
    });

    // 2. Validation formulaire
    $("form[name='form_sav86']").submit(function(e) {
        var dateEntreeStr = $("input[name='date_entree']").val();
        var datePrevueStr = $("input[name='date_prevue']").val();

        if (!dateEntreeStr || !datePrevueStr) {
            alert("Les dates d'entrée et prévue sont obligatoires");
            e.preventDefault();
            return false;
        }

        var parseDate = function(str) {
            var p = str.split("/");
            return new Date(p[2], p[1]-1, p[0]);
        };
        var dateEntree = parseDate(dateEntreeStr);
        var datePrevue = parseDate(datePrevueStr);
        var priorite = $("select[name='indice_priorite']").val();

        if (datePrevue < dateEntree) {
            alert("La date prévue ne peut pas être antérieure à la date d'entrée");
            e.preventDefault();
            return false;
        }
        if (priorite == "normal") {
            var diffHeures = (datePrevue - dateEntree) / (1000 * 60 * 60);
            if (diffHeures < 48) {
                alert("Pour une priorité normale, la date prévue doit être au moins 48h après la date d'entrée.");
                e.preventDefault();
                return false;
            }
        }
        if (dateEntree.getTime() === datePrevue.getTime() && priorite != "urgent") {
            alert("Si la date d'entrée est égale à la date prévue, la priorité doit être Urgent.");
            e.preventDefault();
            return false;
        }

        // 3. Alerte mot de passe vide (si activé)
        if (config.alertMdpVide) {
            var mdp = $("input[name='Mdpasse']").val().trim();
            if (mdp === "") {
                if (!confirm("⚠️ Le champ Mot de passe est vide.\n\nEst-ce volontaire ?")) {
                    e.preventDefault();
                    return false;
                }
            }
        }
        return true;
    });

    // 4. Auto-état "Fini" quand Date de fin renseignée (mode edit uniquement)
    if (mode === 'edit') {
        $("input[name='date_fin_day'], input[name='date_fin_month'], input[name='date_fin_year']").on("change", function() {
            var day = $("input[name='date_fin_day']").val();
            var month = $("input[name='date_fin_month']").val();
            var year = $("input[name='date_fin_year']").val();
            if (day && month && year) {
                $("select[name='etat']").val("Fini");
                console.log("✅ Date de fin complète → État = Fini");
            }
        });
    }
});
