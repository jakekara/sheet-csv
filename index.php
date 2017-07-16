<?php

// --------------------------------------
// index.php - main script for sheets-csv
// to serve google sheets as CSVs from
// your own server, reducing the load time
// for your apps that still need a simple
// spreadsheet backend for live data
// --------------------------------------

include "getter.php";
include "csv.php";

function main(){

    // if there is no spreadsheet code specified, complain
    if ( ! isset($_GET["u"] ))
    {
        serve_csv("ERROR: Not Google sheet code specified. Use '?u=' parameter");
    }

    $sheet_code = preg_replace("/(\.\.)/","", $_GET["u"]);
    // $sheet_code = $_GET["u"];

    // If we have a master file, serve that, but continuing fetching from
    // the server. This allows you to override the spreadsheet manually if
    // you need to jump in during a live event and force a fixed CSV to be
    // served.
    $master_filename = get_master_filename($sheet_code);
    
    if (file_exists($master_filename)){

        $contents = serve_csv(file_get_contents($master_filename));
        serve_csv($contents);

        // if the file is expired, get a new one
        if (file_is_expired(get_filename($sheet_code)))
            get_and_archive($sheet_code); // now update the cache
    }
    
    // If we do not have any copy, get it  from google, and
    // then serve that. This is the slowest path
    else if ( !do_we_have_it($sheet_code) ) {

        // get the spreadsheet from google, save a copy and serve it 
        $contents = get_and_archive($sheet_code);
        serve_csv($contents);
    }
    // Otherwise, we have a copy, so send that, kill the connection, and
    // then update the cache from google
    else {


        $contents = get_it_from_the_back($sheet_code); // get our old copy
        serve_csv(get_it_from_the_back($sheet_code));  // send it along

        // if the file is expired, get a new one
        if (file_is_expired(get_filename($sheet_code)))
            get_and_archive($sheet_code); // now update the cache
    }

}


main();

?>