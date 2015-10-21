function sendDecision(values) {
    $.ajax({
        type: 'POST',
        url: 'database.php',
        data: values,
        error: function(jqXHR, textStatus, errorThrown) {
            console.log("error!, entry in db not successfull, no matchmaking started!");
        },
        success: function(response) {
            console.log("success!");
            checkMatches();
        }
    });
}

function checkMatches() {
    var currentName = $("#myform :input[name]").val();

    $.ajax({
        type: 'POST',
        url: 'database.php',
        data: {
            check: "yes",
            name: currentName,
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log("error trying to check matches!");
        },
        success: function(response) {
            // convert to JSON
            try {
                response = JSON.parse(response);
            } catch (e) {}

            console.log("checking matches...");
            // display match!;
            if (validateMatches(response)) {
                console.log("display matches!")

            } else {
                console.log("dont display matches!")
            };
            displaymatches(validateMatches(response));
        }
    });
}

function validateMatches(json) {
    console.log("inside validate");

    //var json = JSON.parse(response);
    var result = false;
    if (json.matches > 1) {  // find more than one entry for the time
        console.log(json.matches.toString() + " matches found!");
        result = true;
    } else {
        console.log("no matches found!");
        //console.log(json.matches.toString() + " matches found!")
    }
    return result;
}

function displaymatches(matchfound) {
    $("#match").empty();
    if (matchfound == true) {
        $("#match").append("Match found! :)");
        var html = '<iframe src="//giphy.com/embed/iPTTjEt19igne" width="480" height="270" frameBorder="0" class="giphy-embed" allowFullScreen></iframe><p><a href="http://giphy.com/gifs/dancing-happy-jimmy-fallon-iPTTjEt19igne"></a></p>'
        $("#match").append(html);
    } else {
        $("#match").append("No Match found! :(");
        var html = '<iframe src="//giphy.com/embed/srNYANrTyYeFW" width="480" height="480" frameBorder="0" class="giphy-embed" allowFullScreen></iframe><p><a href="http://giphy.com/gifs/srNYANrTyYeFW"></a></p>'
        $("#match").append(html);
    }
}


$(document).ready(function() {
    $(".button").click(function() {
        var values = $('#myform').serialize();
        sendDecision(values);
    });

    /*
    $(".button").click(function() {
        var decision = $(this).val();
        var playerName = $("#playerName").val();
        console.log(playerName);
        sendDecision(playerName, decision);
        // callbacks for checking and siplaying matches
    });
*/
});
