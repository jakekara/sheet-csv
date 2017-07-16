sheets-csv - serve google spreadsheets from your own server, which is
usually faster

by Jake Kara
jake@jakekara.com

WHY
	Google spreadsheets is a good backend for news apps, when you need
	to quickly give your colleagues a spreadsheet to update. This is
	especially valuable for live events, such as elections.

	The problem is that if your app has to get to .csv from Google's
	servers via an AJAX request, that can be slow, and slow down your
	app a lot.

	EXAMPLE: Your app can instead get the data from your own
	folder. I'll use the example of setting up the data backend for an
	election results app on a super-short deadline throughout this guide.

OVERVIEW

	The user requests a CSV file via a GET request.

	The server checks to see if it has a copy.

	If it doesn't have a copy, it gets it from Google and sends that,
	which is the slowest option, and stores a copy for the next request.

	If it does have a copy, it serves that, closes the connection and
	the updates the cache by getting a new copy from Google, which is
	faster than going to Google directly.

	EXAMPLE: As your colleagues fill election results into the Google
	spreadsheet, the changes will be reflected each time a new version
	is pulled from Google's servers.

SETUP

	Copy this repo into a folder on a server that has PHP running.

	In that new folder, run the setup script setup.sh, or just make the
	following directory tree:

		  ./data
			./archive
			./master

BASIC USE

	To get a spreadsheet, first publish it in Google Sheets, as a CSV,
	and get the big string of gibberish from the URL, which is the
	ID of the spreadsheet. Observe:

	The url to share the spreadsheet as a CSV might look like this:

	https://docs.google.com/spreadsheets/d/BIG_STRING_OF_GIBBERISH/pub?gid=0&single=true&output=csv

	Take out the BIG_STRING_OF_GIBBERISH part, and we'll call that the
	sheet_id from here on.

	Next, browse to the URL of the folder where you copied this repo,
	and add ?u=BIG_STRING_OF_GIBBERISH, like so:

	http://localhost/your-election-app/sheets-csv-copy/?u=BIG_STRING_OF_GIBBERISH

	Voila. You should get your .csv.

	You'll notice this created two files on the server:
	./data/BIG_STRING_OF_GIBBERISH.csv and
	./data/archive/BIG_STRING_OF_GIBBERISH-[TIMESTAMP].csv

	We'll get to that in the next section.

THE DATA FOLDER

	 The ./data/BIG_STRING_OF_GIBBERISH.csv file is the "latest" copy
	 of the file. It will be served for the next request. The
	 timestamped file in the ./data/archive/ folder is just an archive
	 (up to one per minute, but we'll be able to change how often a new
	 file is archived), in case you want to see the data changing over
	 time or roll back to a previous version of the CSV.

USAGE: OVERRIDING WITH A MASTER CSV

	 The ./data/master folder allows you to override the spreadsheet
	 completely.

	 EXAMPLE: You might want to do this when the election is over, so
	 the results are effectively "locked in" and no longer dependent on
	 the google sheet living on.

	 To use this feature, you copy your file from the archive folder,
	 and replace the timestamp part of the name with MASTER, so it
	 looks like:

	 ./data/master/BIG_STRING_OF_GIBBERISH-MASTER.csv

	 As long as that file exists, it will always be served. The system
	 will still try to update the cache in the background.

USAGE: SAVING FEWER ARCHIVE COPIES

	  Saving a copy of a spreadsheet each minute could lead to major
	  wasting of disk space, but for our example, it's fine, at least
	  on the night of an election.

	  To change it so that it only stores an archive file each hour,
	  day, month, etc, simple change the $TIME_FMT variable in conf.php
	  to any valid time format that strftime will recognize. I have
	  some examples in there.

	  NOTE: The current implemention overwrites files with the same
	  timestamp, which does save disk space, but if the write cost is a
	  problem for you, keep that in mind. I should make the program
	  check if the file exist and don't bother overwriting it. 

USAGE: DON'T QUERY GOOGLE SO OFTEN

       NOT IMPLEMENTED

       I have a $TTL variable in the conf.php file, which is not
       implemented. When implemented, it would throttle the cache updating
       to queries that are at leaset $TTL seconds apart. I didn't implement
       it because I wasn't sure about why the precision for filemtime() and
       time() was different, and whether they differed based on the machine
       they were running on -- so I couldn't reliably determine the "age"
       of a file to test whether it was older than $TTL seconds.


