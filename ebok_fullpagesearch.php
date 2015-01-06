<?php

// FINNER E-BØKER OG VISER TREFF PÅ HELE SIDEN

// Declare variables
$tittel = '';
$forfatter = '';
$bokhyllahtml = '';
$bokhyllatreff = '';
$bokselskaphtml = '';
$openlibraryhtml = '';
$bokhyllaantalltreff = '';
$bokselskapantalltreff = '';
$openlibraryantalltreff = '';

// turn on for debug

/*
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);
*/

// INNSTILLINGER
$bokhyllaft = 'false'; // fulltekstsøk i Bokhylla? (gir myriader av treff)
$makstreff = 500; // her slår vi på stortromma - dette er fullpagesearch

// vi trenger funksjoner
require_once ('includes/functions.php');

// Get Search
$search_string = urlencode(stripslashes(strip_tags($_REQUEST['query'])));
$search_string = str_replace ("\"", "%22" , $search_string);
$search_string = str_replace (" ", "%20" , $search_string);

// Define Output HTML Formatting of single item

$singlehtml = "<tr>\n";
$singlehtml .= "<td><a href=\"urlString\" target=\"_blank\">titleString</a></td>\n";
$singlehtml .= "<td>authorString</td>\n";
$singlehtml .= "<td>publishedString</td>\n";
$singlehtml .= "<td>yearString</td>\n";
$singlehtml .= "<td>kildeString</td>\n";
$singlehtml .= "</tr>\n\n";

// Søke i Bokhylla
$rawurl = "http://www.nb.no/services/search/v2/search?q=<!QUERY!>&fq=digital%3AJa&fq=mediatype%3A(Bøker)&fq=contentClasses%3A<!MATERIAL!>&itemsPerPage=" . $makstreff . "&ft=" . $bokhyllaft;

if ($_REQUEST['epub'] == "2") { // epub valgt
	$rawurl = str_replace ("<!MATERIAL!>" , "(epub)" , $rawurl);
$result = "epub";
	if ($_REQUEST['pdf'] == "1") { // pdf også valgt
		$rawurl = str_replace ("(epub)" , "(public%20OR%20epub)" , $rawurl);
$result = "BEGGE";
	}
} else if ($_REQUEST['pdf'] == "1") { // bare pdf valgt
	$rawurl = str_replace ("<!MATERIAL!>" , "(public)" , $rawurl);
$result = "pdf";
} else {
	echo "<h2>Velg enten PDF eller e-bok (eller begge!)</h2>";
	echo '<input type="button" value="Lukk vinduet" id="close" onclick="window.close()" />';
	break;
}


//$rawurl = str_replace ("<!QUERY!>" , utf8_decode($search_string) , $rawurl); // sette inn søketerm
$rawurl = str_replace ("<!QUERY!>" , $search_string , $rawurl); // sette inn søketerm

// LASTE TREFFLISTE SOM XML

$output = get_content($rawurl);
$xmldata = simplexml_load_string($output);

// FINNE ANTALL TREFF
$bokhyllaantalltreff = substr(stristr($xmldata->subtitle, " of ") , 4);

// ... SÅ HVERT ENKELT TREFF
	$teller = 0;
	foreach ($xmldata->entry as $entry) {
		if ($teller < $makstreff) {

			// METADATA SOM XML FOR DETTE TREFFET
			$childxml = ($entry->link[0]->attributes()->href); // Dette er XML med metadata
			
			$output = get_content($childxml);
			$childxmldata = simplexml_load_string($output);

			$namespaces = $entry->getNameSpaces(true);
			$nb = $entry->children($namespaces['nb']);
	
			$bokhyllatreff[$teller]['tittel'] = $childxmldata->titleInfo->title;
			$bokhyllatreff[$teller]['forfatter'] = $nb->namecreator;
	
			// FINNE URN
			if (stristr($nb->urn , ";")) {
				$tempura = explode (";" , $nb->urn);
				$urn = trim($tempura[1]); // vi tar nummer 2 
			} else {
				$urn = $nb->urn[0];
			}
	
			$bokhyllatreff[$teller]['url'] = "http://urn.nb.no/" . $urn;
			$bokhyllatreff[$teller]['kilde'] = "Nasjonalbiblioteket";
		
			// FINNE UTGIVELSESSTED OG -ÅR

			if (!empty($childxmldata->originInfo->dateIssued[1])) {
				$bokhyllatreff[$teller]['year'] = $childxmldata->originInfo->dateIssued[1];
			} else {
				$bokhyllatreff[$teller]['year'] = $childxmldata->originInfo->dateIssued[0];
			}

			$publishedby = $childxmldata->originInfo->publisher;
			$publishedplace = $childxmldata->originInfo->place->placeTerm;
			if ($publishedplace != "") {
				$bokhyllatreff[$teller]['utgitt'] = $publishedplace;
				if ($publishedby != "") {
				$bokhyllatreff[$teller]['utgitt'] .= " : " . $publishedby;
				}
			} else {
				$bokhyllatreff[$teller]['utgitt'] = $publishedby;
			}

			$teller++;
		}
	} // SLUTT PÅ HVERT ENKELT TREFF

foreach ($bokhyllatreff as $singeltreff) {
        $bokhyllatreffhtml = str_replace ("urlString" , $singeltreff['url'] , $singlehtml);
        $bokhyllatreffhtml = str_replace ("titleString" , trunc($singeltreff['tittel'], 12) , $bokhyllatreffhtml);
        $bokhyllatreffhtml = str_replace ("authorString" , trunc($singeltreff['forfatter'], 5) , $bokhyllatreffhtml);
		$bokhyllatreffhtml = str_replace ("publishedString" , $singeltreff['utgitt'] , $bokhyllatreffhtml);		
		$bokhyllatreffhtml = str_replace ("yearString" , $singeltreff['year'] , $bokhyllatreffhtml);		
		$bokhyllatreffhtml = str_replace ("classString" , "bokhyllatreff" , $bokhyllatreffhtml);
		$bokhyllatreffhtml = str_replace ("kildeString" , "bokhylla.no" , $bokhyllatreffhtml);
       
        $bokhyllahtml .= $bokhyllatreffhtml;
}


// Let's go bokselskap

$search_string = str_replace ("%22" , "", $search_string);
$search_string = urldecode ($search_string);

//echo "<h1>*" . $search_string . "*</h1>";

if ($_REQUEST['epub'] == "2") { // bokselskap bare hvis epub valgt!
	
	$xmldata = simplexml_load_file('includes/publiseringsliste_bokselskap_20140808.xml'); // Denne er lokal, så den funker

// Gå gjennom lista for å finne treff
	$teller = 0;

	foreach ($xmldata->text->body->div->list->item as $entry) {
		if ($teller < $makstreff) {
			$url = $entry->ref->attributes()->target;
			$forfatter = $entry->ref->name;
			$tittel = $entry->ref->title;
			if (mb_stristr($forfatter , $search_string) || mb_stristr($tittel , $search_string)) {
				$bokselskaptreffhtml = str_replace ("urlString" , $url , $singlehtml);
				$bokselskaptreffhtml = str_replace ("titleString" , trunc($tittel, 12) , $bokselskaptreffhtml);
				$bokselskaptreffhtml = str_replace ("authorString" , trunc($forfatter , 5) , $bokselskaptreffhtml);
				$bokselskaptreffhtml = str_replace ("publishedString" , "N/A" , $bokselskaptreffhtml);
				$bokselskaptreffhtml = str_replace ("yearString" , "N/A" , $bokselskaptreffhtml);
				$bokselskaptreffhtml = str_replace ("classString" , "bokselskaptreff" , $bokselskaptreffhtml);
				$bokselskaptreffhtml = str_replace ("kildeString" , "bokselskap.no" , $bokselskaptreffhtml);
				$bokselskaphtml .= $bokselskaptreffhtml;
				$teller++;

			}
		}

	} // SLUTT PÅ HVERT ITEM
	$bokselskapantalltreff = $teller;

} // SLUTT PÅ BARE HVIS EPUB VALGT


// Søke i Openlibrary

$search_string = str_replace ("%22" , "", $search_string);
$search_string = urldecode ($search_string);

$rawurl = "https://openlibrary.org/search.json?q=<!QUERY!>&has_fulltext=true";
$rawurl = str_replace ("<!QUERY!>" , $search_string , $rawurl); // sette inn søketerm

$resultsfile = get_content($rawurl);
$allresults = json_decode($resultsfile);

$results = $allresults->docs;

// Hvert enkelt treff

$teller = 0;
$totalt = 0;

foreach ($results as $treff) {
	if ($treff->public_scan_b == '1') {
		$totalt++;
		if ($teller < $makstreff) {
			$tittel = $treff->title;
			if ($treff->subtitle != '') {
				$tittel .= " : " . $treff->subtitle;
			}
			$forfatter = $treff->author_name[0];
			$omslag = "https://covers.openlibrary.org/b/olid/" . $treff->cover_edition_key . "-M.jpg";
			$kilde = "Open Library";
			$url = "https://openlibrary.org" . $treff->key;
			$utgitt = $treff->publish_place[0] . " : " . $treff->publisher[0];
			$utgittaar = $treff->publish_date[0];
	
			$openlibrarytreffhtml = str_replace ("urlString" , $url , $singlehtml);
			$openlibrarytreffhtml = str_replace ("titleString" , trunc($tittel, 12) , $openlibrarytreffhtml);
			$openlibrarytreffhtml = str_replace ("authorString" , trunc($forfatter, 5) , $openlibrarytreffhtml);
			$openlibrarytreffhtml = str_replace ("publishedString" , $utgitt , $openlibrarytreffhtml);
			$openlibrarytreffhtml = str_replace ("yearString" , $utgittaar , $openlibrarytreffhtml);
			$openlibrarytreffhtml = str_replace ("classString" , "openlibrarytreff" , $openlibrarytreffhtml);
			$openlibrarytreffhtml = str_replace ("kildeString" , "Open Library" , $openlibrarytreffhtml);

			$openlibraryhtml .= $openlibrarytreffhtml;
			$teller++;

		}
	}
}

$openlibraryantalltreff = $totalt;


// FERDIG MED Å SØKE - SKRIVE UT SIDEN MED RESULTATER

?>

<!DOCTYPE html>
<html lang="no">
  <head>
    <meta charset="utf-8">
    <title>Resultater for s&oslash;k etter '<?php echo stripslashes(strip_tags($_REQUEST['query']));?>'</title>

<style>
body {
	background:#fff url(g/tablesort/bg.gif) repeat-x;
	color:#091f30;
	font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
	font-weight: 300;
}

.sortable {
	width: 100%;
	border-left:1px solid #c6d5e1;
	border-top:1px solid #c6d5e1;
	border-bottom:none;
	margin:0 auto 15px;
	}

.sortable th {
	background-color: #000;
	text-align:left;
	color:#fff;
}

.sortable th h3 {
	font-size:18px; 
	padding:6px 8px 8px;
	margin: 0;
}

.sortable td {
	padding:4px 6px 6px;
	border-bottom:1px solid #c6d5e1;
	border-right:1px solid #c6d5e1;
}

.sortable .head h3 {
	background:url(g/tablesort/sort.gif) 7px center no-repeat;
	cursor:pointer;
	padding-left:18px;
}

.sortable .desc h3 {
	background:url(g/tablesort/desc.gif) 7px center no-repeat;
	cursor:pointer;
	padding-left:18px
}

.sortable .asc h3 {
	background:url(g/tablesort/asc.gif) 7px center no-repeat;
	cursor:pointer;
	padding-left:18px
}

.sortable .head:hover, .sortable .desc:hover, .sortable .asc:hover {
	color:#fff;
	}

.sortable .evenrow td {
	background:#fff;
	}

.sortable .oddrow td {
	background:#ecf2f6;
	}

.sortable td.evenselected {
	background:#ecf2f6;
	}

.sortable td.oddselected {
	background:#dce6ee;
	}

#controls {
	width: 100%;
	margin:0 auto; 
	height:20px
}

#perpage {
	float:left;
	width:20%;
}

#perpage select {
	float:left;
}

#perpage span {
	float:left;
	margin:2px 0 0 5px;
}

#navigation {
	float:left;
	width: 60%;
	text-align:center;
}

#navigation img {
	cursor:pointer;
}

#text {
	float:left;
	width: 20%;
	text-align:right;
	margin-top:2px;
}

</style>

  </head>


  <body>
<div class="loader"></div>

	<div id="controls">
		<div id="perpage">
			<select onchange="sorter.size(this.value)">
				<option value="50" selected="selected">50</option>
				<option value="100">100</option>
				<option value="250">250</option>
			</select>
			<span>Treff per side</span>
		</div>
		<div id="navigation">
			<img src="g/tablesort/first.gif" width="16" height="16" alt="First Page" onclick="sorter.move(-1,true)" />
			<img src="g/tablesort/previous.gif" width="16" height="16" alt="First Page" onclick="sorter.move(-1)" />
			<img src="g/tablesort/next.gif" width="16" height="16" alt="First Page" onclick="sorter.move(1)" />
			<img src="g/tablesort/last.gif" width="16" height="16" alt="Last Page" onclick="sorter.move(1,true)" />
		</div>
		<div id="text">Viser side <span id="currentpage"></span> av <span id="pagelimit"></span></div>
	</div>

<table id="table" class="sortable">
<thead><tr><th style="width: 40%;"><h3>Tittel</h3></th><th style="width: 25%;"><h3>Forfatter</h3></th><th style="width: 25%;"><h3>Utgitt</h3></th><th style="width: 5%;"><h3>År</h3></th><th style="width: 5%;"><h3>Kilde</h3></th></tr></thead>
<tbody>
<?php echo $bokhyllahtml;?>
<?php echo $bokselskaphtml;?>
<?php echo $openlibraryhtml;?>
</tbody>
</table>

	<div id="controls">
		<div id="perpage">
			<select onchange="sorter.size(this.value)">
				<option value="50" selected="selected">50</option>
				<option value="100">100</option>
				<option value="250">250</option>
			</select>
			<span>Treff per side</span>
		</div>
		<div id="navigation">
			<img src="g/tablesort/first.gif" width="16" height="16" alt="Første side" onclick="sorter.move(-1,true)" />
			<img src="g/tablesort/previous.gif" width="16" height="16" alt="Forrige side" onclick="sorter.move(-1)" />
			<img src="g/tablesort/next.gif" width="16" height="16" alt="Neste side" onclick="sorter.move(1)" />
			<img src="g/tablesort/last.gif" width="16" height="16" alt="Siste side" onclick="sorter.move(1,true)" />
		</div>
	</div>

<script src="js/table.js"></script>
<script type="text/javascript">
var sorter = new TINY.table.sorter('sorter');
sorter.head = 'head'; //header class name
sorter.asc = 'asc'; //ascending header class name
sorter.desc = 'desc'; //descending header class name
sorter.even = 'evenrow'; //even row class name
sorter.odd = 'oddrow'; //odd row class name
sorter.evensel = 'evenselected'; //selected column even class
sorter.oddsel = 'oddselected'; //selected column odd class
sorter.paginate = true; //toggle for pagination logic
sorter.pagesize = 50; //toggle for pagination logic
sorter.currentid = 'currentpage'; //current page id
sorter.limitid = 'pagelimit'; //page limit id
sorter.init('table',0);

</script>



</body>
</html>
