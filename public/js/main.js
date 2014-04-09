$(document).ready(function(){

    var results = $('#results');
    var responseText = $('#responseText');

    $('#submit').on('click', function(e){

        e.preventDefault();
        var search = $('#search').val();
        search = search.replace(/\W+/g, " ");

        if ( !search || search == " ")
            search = "sale Dublin";

        $.get('/search.php', {"search" : search})
            .done(function(response){
                console.log(response);
                response = JSON.parse(response);
                results.html('');
                if ( response.code == 200 )
                {
                    var query = response.data.results.search_sentence;
                    var total = response.data.results.pagination.total_results;
                    var ads = response.data.results.ads;

                    responseText.html(total + ' results for: ' + query.substr(0, query.length - 12));

                    for( var i = 0; i < ads.length; i++ )
                    {
                        ad = ads[i];
                        results.append('<a href="' + ad.daft_url + '" target="_blank">' + ad.description + '</a><br/><br/>');
                    }
                }
                else if ( response.code == 204 )
                {
                    var query = response.data.results.search_sentence;
                    var total = response.data.results.pagination.total_results;

                    responseText.html(total + ' results for: ' + query.substr(0, query.length - 12));
                }
                else if ( response.code == 400 )
                {
                    var status = 'Your query could not be translated. Try search in the format:<br/>Flat for {sale | rent} in {location} for {price} ensuring location begins with a capital letter';

                    responseText.html(status);
                }
            });
    });
});
