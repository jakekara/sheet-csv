<?

// --------------------------------
// csv.php - serve csv files
// --------------------------------

function serve_csv($contents){
    // this ob_ stuff is for buffering, so we can send the contents and
    // then keep doing stuff (updating the cache) ater the connection
    // is flushed
        
    ob_start();             // start buffering
    
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: text/csv");
    header("Content-Disposition: inline; filename=\"" . "download" . ".csv\"");
    echo $contents;

    header('Connection: close'); // close the connection after
    header('Content-Length: '. ob_get_length()); // sending XX bytes
    ob_end_flush();                              // send the buffer
    ob_flush();                                  // this might be
    // redundant
    flush();                                
    
}

?>
