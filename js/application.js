var app = {
    init: function() {
        app.getJobs();
        app.setObservers();
    },
    setObservers: function() {
        $('body').on('click', 'ul#jobs li', app.openJob);
    },
    getJobs: function() {
        $.ajax({
            'url': 'api/suggested_jobs.php',
            'method': 'get',
            'data': {
                'method': 'suggestedjobs'
            },
            'dataType': 'json',
            'success': app.displayJobs,
            // TODO: failure handler
        });
    },
    displayJobs: function(data) {
        $.each(data.jobs.values, function(i, job) {
            $('<li></li>').html(job.company.name)
                           .data('raw', JSON.stringify(job))
                           .appendTo('ul#jobs')
        });
    },
    openJob: function(event) {
        var job = JSON.parse($(event.target).data('raw'));

        // $('h2#jobtitle', 'div#job').html(job.title);
        $('p#description', 'div#job').html(job.descriptionSnippet + ' (...)');
        $('h2#about', 'div#job').html('Over ' + job.company.name);

        $('div#job').show();

        app.loadDBpediaInfo(job.company.name);
    },
    loadDBpediaInfo: function(company) {
        $('table#dbpedia').html('<tr><td>Loading...</td></tr>');

        // Check for redirect
        var query = "PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> " +
        "PREFIX foaf: <http://xmlns.com/foaf/0.1/> " +
        "PREFIX dbo: <http://dbpedia.org/ontology/> " +

        "SELECT ?s WHERE { " + 
        "  { " +
        "    ?s rdfs:label \"" + encodeURIComponent(company) + "\"@en ;" + 
        "       a owl:Thing . " +
        "  } " +
        "  UNION " +
        "  { " +
        "    ?altName rdfs:label \"" + encodeURIComponent(company) + "\"@en ; "+
        "             dbo:wikiPageRedirects ?s . " +
        "  } " +
        "}";

        $.ajax({
            'url': 'http://dbpedia.org/sparql',
            'method': 'post',
            'data': {
                'output': 'json',
                'query': query
            },
            'success': function(data) {
                if (data.results.bindings.length == 0) {
                    var name = "http://dbpedia.org/resource/" + encodeURIComponent(company); 
                } else {
                    var name = data.results.bindings[0].s.value;
                }

                var query = "PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> " +
                "PREFIX foaf: <http://xmlns.com/foaf/0.1/> " +
                "PREFIX dbo: <http://dbpedia.org/ontology/> " +
                "SELECT DISTINCT ?abstract ?thumbnail ?location where { " +
                "  { <"+name+"> dbpedia-owl:abstract ?abstract }" +
                "  UNION" +
                "  { <"+name+"> dbpedia-owl:thumbnail ?thumbnail } " +
                "  UNION" +
                "  { <"+name+"> dbpedia-owl:location ?location } " +
                "}";

                $.ajax({
                    'url': 'http://dbpedia.org/sparql',
                    'method': 'post',
                    'data': {
                        'output': 'json',
                        'query': query
                    },
                    'success': app.showDBPediaInfo
                });
            }
        });
    },
    showDBPediaInfo: function(data) {
        if (data.results.bindings.length == 0) {
            $('table#dbpedia').html('<tr><td>No wikipedia article found...</td></tr>');
        } else {
            $('table#dbpedia').html('');
            $.each (data.results.bindings, function(i, value) {
                $('<tr></tr>').append($('<th></th>').html(Object.keys(value)[0]))
                              .append($('<td></td>').html(value[Object.keys(value)[0]].value))
                              .appendTo('table#dbpedia');
            });
        }
    }
};
$(app.init);