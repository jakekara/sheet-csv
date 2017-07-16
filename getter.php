<?php

/*
 * getter.php
 */

include "conf.php";

// return file age in seconds or FALSE
function get_file_age($filename){
    $file_time = filemtime($filename);
    if (!$file_time) return FALSE;
    return time() - $file_time;
}

// return TRUE if the file expired or else FALSE
// NOT IMPLEMENTED: Always returns true
function file_is_expired($filename){

    return TRUE;                // short-circut the function

    // the below implementation does not work because the precision between
    // file modification and the time() function seems to be different. not
    // sure why, or if this is system-dependent, and I don't need this
    // feature enough for my Monday deadline to figure it out

    // global $TTL;

    // $file_age = get_file_age($filename);

    // if (!$file_age) return TRUE;

    // if ($TTL <= 0 || $file_age >= $TTL) return TRUE;

    // return FALSE;
    
}

function get_sheet_url($sheet_code){
    global $URL_PREF, $URL_SUFF;
    return $URL_PREF . $sheet_code . $URL_SUFF;
}

function time_str($unix_time){
    return strftime("%Y-%m-%d-%H-%M", $unix_time);
}

function now_str(){
    return time_str(time());
}


function get_master_filename($sheet_code){
    global $DATA_DIR;
    return $DATA_DIR . "master/" . $sheet_code . "-MASTER.csv";
}

function get_filename($sheet_code){
    global $DATA_DIR;
    return $DATA_DIR . $sheet_code . ".csv";
}

function get_archive_filename($sheet_code){
    global $DATA_DIR;
    return $DATA_DIR . "archive/" . $sheet_code . "-" . now_str() . ".csv";
}

function do_we_have_it($sheet_code){
    return file_exists(get_filename($sheet_code));
}

function get_it_from_the_back($sheet_code){
    return file_get_contents(get_filename($sheet_code));
}

function get_and_archive($sheet_code){

    $contents = file_get_contents(get_sheet_url($sheet_code));
    $sheet_url = get_sheet_url($sheet_code);

    if (!$contents){
        return NULL;
    }

    $file_name = get_filename($sheet_code);

    // overwrite the copy in the root of the data folder
    // which will be used as the "latest" copy
        
    $outfh = fopen(get_filename($sheet_code),"w");
    $archfh = fopen(get_archive_filename($sheet_code),"w");

    fwrite($outfh, $contents);
    fwrite($archfh, $contents);
        
    fclose($outfh);
    fclose($archfh);

    return $contents;
}

?>