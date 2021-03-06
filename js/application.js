var app = {
    init: function() {
        app.getJobs();
        app.setObservers();
    },
    setObservers: function() {
        $('body').on('click', 'ul#jobs li', app.openJob);
        $('body').on('click', 'a#more', app.openMore);
    },
    getJobs: function() {
        $.ajax({
            'url': 'api/suggested_jobs.php',
            'method': 'get',
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

        $('h2#jobtitle', 'div#job').html(job.company.name);
        $('p#description', 'div#job').html(job.descriptionSnippet + ' (...)');
        $('h2#about', 'div#job').html('About ' + job.company.name);

        $('div#job').show();

        // app.linkedin.load(
        //     job.id
        // );
        app.dbpedia.load(
            app.normalizeCompanyName(job.company.name)
        );
        app.glassdoor.load(
            job.company.id
        );
    },
    linkedin: {
        load: function(job) {
            $.ajax({
                'url': 'api/get_job.php',
                'method': 'get',
                'data': {
                    'id': job
                },
                'dataType': 'json',
                'success': app.linkedin.show,
            });
        },
        show: function(data) {
            console.log(data);
        }
    },
    dbpedia: {
        load: function(company) {
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
                        'success': app.dbpedia.show
                    });
                }
            });
        },
        show: function(data) {
            if (data.results.bindings.length == 0) {
                $('table#dbpedia').html('<tr><td>No wikipedia article found...</td></tr>');
            } else {
                $('table#dbpedia').html('');
                $.each (data.results.bindings, function(i, data) {
                    var key   = Object.keys(data)[0];
                    var value = data[Object.keys(data)[0]].value;
                    var lang  = data[Object.keys(data)[0]]['xml:lang'];

                    switch(true) {
                        case lang !== undefined && lang != 'en':
                            return;
                            break;
                        case key == 'abstract':
                            var shortAbstract = value.substring(0,200);
                            var more = value.substring(200, 9999);
                            
                            value = $('<p></p>').html(shortAbstract);
                            if (more.length != 0) {
                                $(value).append($('<a></a>', {'id': 'more', 'href': '#'}).html(' read more..'))
                                        .append($('<span></span>', {'class': 'more'}).hide().html(more));
                            }
                            break;
                        case key == 'thumbnail':
                            value = $('<img></img>', {'src': value, 'alt': '', 'height': '80px'});
                            break;
                        default:
                            value = value.replace('http://dbpedia.org/resource/', '');
                    }

                    $('<tr></tr>').append($('<th></th>').html(key))
                                  .append($('<td></td>').html(value))
                                  .appendTo('table#dbpedia');
                });
            }
        },
    },
    glassdoor: {
        prefix: 'http://iwa.rexvalkering.nl/',
        reviewfields: {
            careerOpportunitiesRating:      'Career Opportunities',
            compensationAndBenefitsRating:  'Compensation and Benefits',
            cultureAndValuesRating:         'Culture and Values',
            seniorLeadershipRating:         'Senior Leadership',
            workLifeBalanceRating:          'Work/Life Balance',
            recommendToFriendRating:        'Recommend to Friend',
            overallRating:                  'Overall'
        },
        load: function(companyid) {
            $('table#glassdoor').html('<tr><td>Loading...</td></tr>');

            $.ajax({
                'url': 'api/get_company.php',
                'method': 'get',
                'dataType': 'json',
                'data': {
                    'id': 'li_c' + companyid
                },
                'success': app.glassdoor.show,
                'failure': app.glassdoor.fail
            }); 
        },
        show: function(data) {
            if (data.error) {
                $('table#glassdoor').html('<tr><td>No Glassdoor entry found...</td></tr>');
            } else {
                $('table#glassdoor').html('');

                $.each(app.glassdoor.reviewfields, function(name, label) {
                    $('<tr></tr>').append($('<th></th>').html(label))
                                  .append($('<td></td>').html(
                                      $('<div class="stars"></div>').css('width', 118 * (data[app.glassdoor.prefix + name] * 0.2))
                                  ))
                                  .append($('<td></td>').html('(' + data[app.glassdoor.prefix + name] + '/5)'))
                                  .appendTo('table#glassdoor');
                });
                $('<tr></tr>').append($('<td colspan="3"></td>').html("From " + data[app.glassdoor.prefix + 'numberOfRatings'] + ' ratings')).appendTo('table#glassdoor');
            }
        },
        fail: function() {
            $('table#dbpedia').html('<tr><td>No Glassdoor entry found...</td></tr>');
        }
    },
    normalizeCompanyName: function(name) {
        return name.replace('B.V.', '')
                   .replace('BV', '')
                   .replace('Netherlands', '')
                   .replace('Nederland', '')
                   .trim();
    },
    openMore: function(event) {
        event.preventDefault();
        $(this).hide();
        $(this).siblings('span.more').show();
    }
};
$(app.init);