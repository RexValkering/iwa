
function fill_profile() {
    console.log("hi");
    $.ajax({
        url: "api/profile.php",
    }).done(function(result) {
        column = $("#iwa-profile-link");
        column.html(result);
    });
}

function fill_companies() {
    $.getJSON("api/suggested_companies.php", {}, function(result) {
        $("#companies").html("");
        $(result.values).each(function(k, v) {
            $("#companies").append(
                '<div class="row"><div class="col-xs-6">' + v["id"] +
                '</div><div class="col-xs-6">' + v["name"] + '</div></div>');
        });
    });
}

function fill_jobs() {
    $.getJSON("api/suggested_jobs.php", {}, function(result) {
        $("#jobs").html("");
        console.log(result);
        $(result.jobs.values).each(function(k, v) {
            console.log("hi");
            $("#jobs").append(
                '<div class="row"><div class="col-xs-2">' + v["id"] +
                '</div><div class="col-xs-2">' + v["company"]["name"] + '</div>' +
                '<div class="col-xs-8">' + v["descriptionSnippet"] + '</div></div>');
        });
    });
}
