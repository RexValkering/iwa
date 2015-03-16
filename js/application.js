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
    }
};
$(app.init);