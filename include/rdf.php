<?php

function prefix() {
    return "PREFIX dc: <http://purl.org/dc/elements/1.1/>;
            PREFIX iwa: <http://iwa.rexvalkering.nl/>;";
}

function build_turtle_from_array($prefix, $data, $type, $postfix) {
    $query = $prefix;
    foreach ($data as $key => $value) {
        $query .= $key . ' "' . $value . '";';
    }

    $query .= 'rdf:type <' . $type . '>.';
    $query .= $postfix;
    return $query;
}

function linkedin_company_to_rdf($data) {
    $ch = curl_init();
    $url = 'http://rexvalkering.nl:5820/iwa/query';
    $prefix = prefix();
    $subject = 'https://www.linkedin.com/company/li_' . $data->id;
    $prefix = $prefix.' INSERT DATA { <' . $subject . '> ';
    $postfix = '}';
    $type = 'http://iwa.rexvalkering.nl/Company';

    $data_array = array(
        'dc:identifier' => 'li_' . $data->id,
        'dc:title' => $data->name
    ); 

    $data = array(
        'query' => build_turtle_from_array($prefix, $data_array, $type, $postfix)
    ); 

    // Remove double whitespace for faster transfer.
    $data['query'] = preg_replace('/[\s]+/mu', ' ', $data['query']);
    $headers = array(
        'Content-Type' => 'text/turtle'
    );

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, count($data));
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    return $result;
}

function glassdoor_company_to_rdf($data) {
    $ch = curl_init();
    $url = 'http://rexvalkering.nl:5820/iwa/query';
    $subject = 'https://iwa.rexvalkering.nl/company/gd_' . $data->id;

    $prefix = prefix();
    $prefix = $prefix.' INSERT DATA { <' . $subject . '> ';
    $postfix = '}';

    $type = 'http://iwa.rexvalkering.nl/Company';

    $data_array = array(
        'dc:identifier' => 'gd_' . $data->id,
        'dc:title' => $data->name,
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
        //'iwa:featuredReview' => $data->featuredReview
    ); 

    $data = array(
        'query' => build_turtle_from_array($prefix, $data_array, $type, $postfix)
    ); 

    print_r(htmlspecialchars($data['query']));

    // Remove double whitespace for faster transfer.
    $data['query'] = preg_replace('/[\s]+/mu', ' ', $data['query']);
    $headers = array(
        'Content-Type' => 'text/turtle'
    );

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, count($data));
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    return $result;
}
