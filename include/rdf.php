<?php

function prefix() {
    return "PREFIX dc: <http://purl.org/dc/elements/1.1/>
            PREFIX iwa: <http://iwa.rexvalkering.nl/>
            PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
            PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>";
}

function build_turtle_from_array($subject, $data, $things) {
    $query = prefix();
    $query .= ' INSERT DATA { <' . $subject . '> ';

    // Append simple data items, such as identifier, title, etc.
    foreach ($data as $key => $value) {
        $query .= $key . ' "' . $value . '";';
    }

    // Append more complex data types, such as rdf:type, other objects.
    $count = 0;
    foreach ($things as $key => $value) {
        $query .= $key . ' <' . $value . '>';
        if ($count < count($things) - 1)
            $query .= ';';
        $count += 1;
    }
    $query .= '.}';

    // Return result.
    return $query;
}

function stardog_execute_query($query, $json = false) {
    $url = 'http://rexvalkering.nl:8080/openrdf-sesame/repositories/iwa';

    // Embed the query in an array.
    $data = array('query' => $query, 'output' => 'json'); 

    // Remove double whitespace for faster transfer.
    $data['query'] = preg_replace('/[\s]+/mu', ' ', $data['query']);
    $headers = array(
        'Content-Type' => 'text/turtle',
        'Accept' => 'application/rdf+xml'
    );

    // Setup curl and make a POST request to Stardog, requesting the query
    // to be executed.
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, count($data));
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);

    return $result;

}

function linkedin_company_to_rdf($data) {
    // Setup prefix and subject.
    $subject = 'https://www.linkedin.com/company/li_c' . $data->id;

    // Create data array for simple string variables.
    $data_array = array(
        'dc:identifier' => 'li_c' . $data->id,
        'iwa:name' => $data->name
    ); 

    // Create array for more complex datatypes.
    $things = array(
        'rdf:type' => 'http://iwa.rexvalkering.nl/Company'
    );

    $query =  build_turtle_from_array($subject, $data_array, $things);
    return stardog_execute_query($query);
}

function glassdoor_company_to_rdf($data) {
    // Short variable for the featured review.
    $fr = $data->featuredReview;

    // Setup prefix and subject.
    $company_subject = 'http://iwa.rexvalkering.nl/company/gd_c' . $data->id;
    $review_subject = 'http://iwa.rexvalkering.nl/review/gd_r' . $fr->id;

    // Setup array of company data, copied from Glassdoor result.
    $company_data = array(
        'dc:identifier' => 'gd_c' . $data->id,
        'iwa:name' => $data->name,
        'iwa:website' => $data->website,
        'iwa:industry' => $data->industry,
        'iwa:numberOfRatings' => $data->numberOfRatings,
        'iwa:logo' => $data->squareLogo,
        'iwa:overallRating' => $data->overallRating,
        'iwa:cultureAndValuesRating' => $data->cultureAndValuesRating,
        'iwa:seniorLeadershipRating' => $data->seniorLeadershipRating,
        'iwa:compensationAndBenefitsRating' => $data->compensationAndBenefitsRating,
        'iwa:careerOpportunitiesRating' => $data->careerOpportunitiesRating,
        'iwa:workLifeBalanceRating' => $data->workLifeBalanceRating,
        'iwa:recommendToFriendRating' => $data->recommendToFriendRating
    ); 

    // Setup array of complex datatypes for company.
    $company_things = array(
        'rdf:type' => 'http://iwa.rexvalkering.nl/Company',
        'iwa:featuredReview' => 'http://iwa.rexvalkering.nl/review/gd_r' . $fr->id
    );

    // Setup array of review data, copied from Glassdoor review result.
    $review_data = array(
        'dc:identifier' => 'gd_r' . $fr->id,
        'dc:location' => $fr->location,
        'dc:dateSubmitted' => $fr->reviewDateTime,
        'iwa:name' => $fr->headline,
        'iwa:jobTitle' => $fr->jobTitle,
        'iwa:jobTitleFromDb' => $fr->jobTitleFromDb,
        'iwa:pros' => htmlspecialchars($fr->pros),
        'iwa:cons' => htmlspecialchars($fr->cons),
        'iwa:overall' => $fr->overall,
        'iwa:overallNumeric' => $fr->overallNumeric
    );

    // Set up array of complex datatypes for review.
    $review_things = array(
        'rdf:type' => 'http://iwa.rexvalkering.nl/Review',
        'iwa:ofCompany' => 'http://iwa.rexvalkering.nl/company/gd_c' . $data->id
    );

    $query = build_turtle_from_array($company_subject, $company_data, $company_things);
    //print_r(htmlspecialchars($query));

    $result = stardog_execute_query($query);
    //print_r($result);

    $query = build_turtle_from_array($review_subject, $review_data, $review_things);
    print_r($query);
    $result = stardog_execute_query($query);
    print_r($result);
    return true;
}

function stardog_get_company_by_name($name) {
   $query = prefix() . '
        SELECT DISTINCT ?key ?object WHERE {
            ?company a iwa:Company ;
                ?key ?object ;
                iwa:name "' . $name . '" .
        }';

    return stardog_execute_query($query, true);
}

function stardog_get_company_by_id($id) {
   $query = prefix() . '
        SELECT DISTINCT ?key ?object WHERE {
            ?company a iwa:Company ;
                ?key ?object ;
                dc:identifier "' . $id . '" .
        }';

    return stardog_execute_query($query, true);
}