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

function stardog_execute_get_query($query) {
    $url = 'http://rexvalkering.nl:8080/openrdf-sesame/repositories/iwa';

    // Embed the query in an array.
    $data = array(
        'query' => $query,
        'Accept' => 'application/sparql-results+json',
    ); 

    // Remove double whitespace for faster transfer.
    $data['query'] = preg_replace('/[\s]+/mu', ' ', $data['query']);

    $headers = array(
        'Content-Type: application/x-www-form-urlencoded'
    );

    //print_r(htmlspecialchars($data['query']));

    // Setup curl and make a GET request to Stardog, requesting the query
    // to be executed.
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, count($data));
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //curl_setopt($ch, CURLOPT_HEADER, 1);
    //curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    // curl_setopt($ch, CURLOPT_HEADER, 1);
    // curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    // $data = curl_exec($ch);
    // var_dump($data);
    // var_dump(curl_getinfo($ch));

    //print_r($data);

    $result = curl_exec($ch);

    return $result;

}

function stardog_execute_update_query($query) {
    $url = 'http://rexvalkering.nl:8080/openrdf-sesame/repositories/iwa/statements';

    // Embed the query in an array.
    $data = array(
        'update' => $query
    );

    // Remove double whitespace for faster transfer.
    $data['update'] = preg_replace('/[\s]+/mu', ' ', $data['update']);

    $headers = array(
        'Content-Type: application/x-www-form-urlencoded'
    );

    // Setup curl and make a GET request to Stardog, requesting the query
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
    if (!isset($data->id))
        return;

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

    $query = build_turtle_from_array($subject, $data_array, $things);
    return stardog_execute_update_query($query);
}

function glassdoor_company_to_rdf($data) {
    // Short variable for the featured review.
    $fr = property_exists($data, 'featuredReview') ? $data->featuredReview : false;

    // Setup prefix and subject.
    $company_subject = 'http://iwa.rexvalkering.nl/company/gd_c' . $data->id;
    $review_subject = 'http://iwa.rexvalkering.nl/review/gd_r' . $fr->id;

    // Setup array of company data, copied from Glassdoor result.
    $company_data = array(
        'dc:identifier'                     => 'gd_c' . $data->id,
        'iwa:name'                          => $data->name,
        'iwa:website'                       => $data->website,
        'iwa:industry'                      => $data->industry,
        'iwa:numberOfRatings'               => $data->numberOfRatings,
        'iwa:logo'                          => $data->squareLogo,
        'iwa:overallRating'                 => $data->overallRating,
        'iwa:cultureAndValuesRating'        => $data->cultureAndValuesRating,
        'iwa:seniorLeadershipRating'        => $data->seniorLeadershipRating,
        'iwa:compensationAndBenefitsRating' => $data->compensationAndBenefitsRating,
        'iwa:careerOpportunitiesRating'     => $data->careerOpportunitiesRating,
        'iwa:workLifeBalanceRating'         => $data->workLifeBalanceRating,
        'iwa:recommendToFriendRating'       => $data->recommendToFriendRating
    ); 

    // Setup array of complex datatypes for company.
    $company_things = array(
        'rdf:type'           => 'http://iwa.rexvalkering.nl/Company',
        'iwa:featuredReview' => $fr ? 'http://iwa.rexvalkering.nl/review/gd_r' . $fr->id : null
    );

    // Setup array of review data, copied from Glassdoor review result.
    $review_data = array(
        'dc:identifier'         => $fr ? 'gd_r' . $fr->id : null,
        'dc:location'           => $fr ? $fr->location : null,
        'dc:dateSubmitted'      => $fr ? $fr->reviewDateTime : null,
        'iwa:name'              => $fr ? $fr->headline : null,
        'iwa:jobTitle'          => $fr ? $fr->jobTitle : null,
        // 'iwa:jobTitleFromDb' => $fr ? $fr->jobTitleFromDb : null,
        'iwa:pros'              => $fr ? htmlspecialchars($fr->pros) : null,
        'iwa:cons'              => $fr ? htmlspecialchars($fr->cons) : null,
        'iwa:overall'           => $fr ? $fr->overall : null,
        'iwa:overallNumeric'    => $fr ? $fr->overallNumeric : null,
    );

    // Set up array of complex datatypes for review.
    $review_things = array(
        'rdf:type' => 'http://iwa.rexvalkering.nl/Review',
        'iwa:ofCompany' => 'http://iwa.rexvalkering.nl/company/gd_c' . $data->id
    );

    $query = build_turtle_from_array($company_subject, $company_data, $company_things);
    //print_r(htmlspecialchars($query));

    $result = stardog_execute_update_query($query);
    //print_r($result);

    $query = build_turtle_from_array($review_subject, $review_data, $review_things);
    //print_r($query);
    $result = stardog_execute_update_query($query);
    //print_r($result);
    return true;
}

function stardog_get_company_by_name($name) {
   $query = prefix() . '
        SELECT DISTINCT ?key ?object WHERE {
            ?company a iwa:Company ;
                ?key ?object ;
                iwa:name "' . $name . '" .
        }';

    return stardog_execute_get_query($query, true);
}

function stardog_get_company_by_id($id) {
   $query = prefix() . '
        SELECT DISTINCT ?key ?object WHERE {
            ?company a iwa:Company ;
                ?key ?object ;
                dc:identifier "' . $id . '" .
        }';

    return stardog_execute_get_query($query, true);
}