<?php
require_once( '/SolrPhpClient/Apache/Solr/Service.php' );
$query = urlencode($_REQUEST["query"]);

$boxTitle = "Search Results for \"".$query."\"";

$apiKey = "pN2y9FrTxpkUHPJt";
$LWE = new Apache_Solr_Service('localhost', '8888', '/solr/SearchDemo' );
//$LWE = new Apache_Solr_Service('https://pN2y9FrTxpkUHPJt:x@s-5b6c2b1b.azure.lucidworks.io', '8888', '/solr/SearchDemo' );

$offset = 0;
$limit = 100;

$searchParams = array(
'hl' => 'true',
'hl.fl' => 'body'
);

$json_url = 'https://s-5b6c2b1b.azure.lucidworks.io/solr/SearchDemo/select?q=' . $query . '&wt=json';
 
$username = 'pN2y9FrTxpkUHPJt';  // authentication
$password = 'x';  // authentication
 
// Initializing curl
$ch = curl_init( $json_url );
 
// Configuring curl options
$options = array(
CURLOPT_RETURNTRANSFER => true,
CURLOPT_HTTPHEADER => array('Content-type: application/json'),
CURLOPT_USERPWD => $username . ":" . $password,  // authentication
CURLOPT_SSL_VERIFYPEER => 0,
CURLOPT_SSL_VERIFYHOST => 2,
CURLOPT_CAINFO => getcwd() . "\lucidworks.crt",
);
 
// Setting curl options
curl_setopt_array( $ch, $options );
 
// Getting results
$response =  curl_exec($ch); // Getting jSON result string
//$response = $LWE->search($query, $offset, $limit, $searchParams);
$response = json_decode($response, true);
//var_dump ($response);

/*if ($response->response->numFound > 0) {
    foreach ( $response->response->docs as $doc ) {
        echo "<a href='{$doc->id}'>{$doc->title}</a><br />";
            echo "...";
            if (isset($response->highlighting->{$doc->id}->body[0])){
                echo $response->highlighting->{$doc->id}->body[0];
            }
            else {
                //echo $response->highlighting->{$doc->id}->body[0];
            }
            echo "...<br /><br />";
    }
    echo '<br />';
} else {
    echo "No results for '".$query."'";
}*/
if ($response["response"]["numFound"] > 0) {
    foreach ( $response["response"]["docs"] as $doc ) {
        echo "<a href='{$doc["id"]}'>{$doc["title"][0]}</a><br />";
            echo "...";
            if (isset($doc["body"][0])){
                //echo $doc["body"][0];
                $desc = highlight($doc["body"][0], urldecode($query));
                //echo $desc;
            }
            else {
                //echo $response->highlighting->{$doc->id}->body[0];
            }
            echo "...<br /><br />";
    }
    echo '<br />';
} else {
    echo "No results for '". urldecode($query)."'";
}

function highlight($body, $findme){
    $pos = strpos(strtolower($body), $findme);
    if (!$pos){
        if (strpos(strtolower($findme), " ") > 0){
            $test = strstr(strtolower($findme), " ", true);
            $pos = strpos(strtolower($body), $test);
        }
        else{
            echo "whee";
            $pos = 0;  
        }
        
    }
    $start = $pos - 200;
    $end = $pos + 200 + strlen($findme);
    
    if ($start < 0){
        $start = 0;
    }
    else {
        $tester = FALSE;
        while ($tester === FALSE){
            if (substr($body, $start, 1) === " "){
                $start++;
                $tester = TRUE;
            }
            else {
                $start++;
            }
        }
    }

    if ($end > strlen($body)){
        $end = strlen($body);
    }
    
    $offset = 0 - (strlen($body) - $end);
    //echo $pos . " " . $start . " " . $end . " " . strlen($body) . " " . $offset;
    if ($offset <= 0){
        echo substr($body, $start, $offset);
    }
    else {
        echo substr($body, $start);
    }
}

?>






