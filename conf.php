<?php
// -----------------------------------
// conf.php - sheets-csv configuration
// -----------------------------------

// GOOGLE URL PARTS
$URL_PREF = "https://docs.google.com/spreadsheets/d/";
$URL_SUFF = "/pub?gid=0&single=true&output=csv"; //  is this necessary?

// DIR ON SERVER TO STORE DATA
$DATA_DIR = "./data/";		// URL to cache sheets

// CACHE TIME TO LIVE
$TTL = 60; 		// seconds

// ARCHIVE FILE TIMESTAMP FORMAT
// one per minute:
$TIME_FMT = "%Y-%m-%d-%H-%M";
// make less precise to save less often, for example:
// one per hour:
// $TIME_FMT = "%Y-%m-%d-%H";
// one per day:
// $TIME_FMT = "%Y-%m-%d";
// one per month:
// $TIME_FMT = "%Y-%m";
// one per year:
// $TIME_FMT = "%Y";
?>