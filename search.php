<?php

// FINNER E-BØKER

// Declare variables
$tittel = '';
$forfatter = '';
$bokhyllahtml = '';
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
$makstreff = (int)$_REQUEST['makstreff']; // hvor mange treff henter vi maks fra hvert sted? Definert i shortcode

// vi trenger funksjoner
require_once ('includes/functions.php');

// Get Search
$search_string = urlencode(stripslashes(strip_tags($_POST['query'])));
$search_string = str_replace ("\"", "%22" , $search_string);
$search_string = str_replace (" ", "%20" , $search_string);

// Define Output HTML Formatting of single item

$singlehtml = '';
$singlehtml .= "<div class=\"ebokresult classString\">\n";
$singlehtml .= "<a class=\"ebokresultlink\" href=\"urlString\" target=\"_blank\">\n";
$singlehtml .= "<img class=\"ebokresultcover\" src=\"omslagString\" alt=\"" . htmlspecialchars('titleString - descriptionString') . "\" />\n";
$singlehtml .= "<b>titleString</b></a>\n";
$singlehtml .= "<br /><span class=\"ebokresultdescription\">descriptionString</span><br />\n";
$singlehtml .= '<a target="_blank" href="https://twitter.com/intent/tweet?url=twitterurlString&via=bibvenn&text=twitterdescriptionString&related=bibvenn,sundaune&lang=no"><img style="width: 20px; height: 20px;" src="' . $litentwitt . '" alt="Twitter-deling" /></a>&nbsp;';
$singlehtml .= "<a target=\"_self\" href=\"javascript:fbShare('urlString', 700, 350)\"><img style=\"width: 50px; height: 21px;\" src=\"" . $litenface . "\" alt=\"Facebook-deling\" /></a>";

$singlehtml .= "<br style=\"clear: both;\">";
$singlehtml .= "</div>\n\n";



// Søke i Bokhylla
$rawurl = "http://www.nb.no/services/search/v2/search?q=<!QUERY!>&fq=digital:Ja&fq=mediatype:(Bøker)&fq=contentClasses:<!MATERIAL!>&itemsPerPage=" . $makstreff . "&ft=" . $bokhyllaft;

switch ($_REQUEST['format']) {
	case "NaN":
		//echo "Velg en materialtype!";
		$rawurl = str_replace ("<!MATERIAL!>" , "(DUMMY)" , $rawurl);
		break;
	case "undefined2":
		$rawurl = str_replace ("<!MATERIAL!>" , "(epub)" , $rawurl);
		break;
	case "1undefined":
		$rawurl = str_replace ("<!MATERIAL!>" , "(public)" , $rawurl);
		break;
	case "12";
		$rawurl = str_replace ("<!MATERIAL!>" , "(public%20OR%20epub)" , $rawurl);
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

			// BOKOMSLAG, SE http://www-sul.stanford.edu/iiif/image-api/1.1/#parameters
			if (stristr($nb->urn , ";")) {
				$tempura = explode (";" , $nb->urn);
				$urn = trim($tempura[1]); // vi tar nummer 2
			} else {
				$urn = $nb->urn[0];
			}
			if ($urn != "") {
				//$bokhyllatreff[$teller]['bokomslag'] = "http://www.nb.no/services/image/resolver?url_ver=geneza&urn=" . $urn . "_C1&maxLevel=6&level=1&col=0&row=0&resX=6000&resY=6000&tileWidth=2048&tileHeight=2048";
				$bokhyllatreff[$teller]['bokomslag'] = "http://www.nb.no/services/iiif/api/" . $urn . "_C1/full/160,/0/native.jpg";
			} else {
				$bokhyllatreff[$teller]['bokomslag'] = $generiskbokomslag; // DEFAULTOMSLAG
			}

			$bokhyllatreff[$teller]['url'] = "http://urn.nb.no/" . $urn;
			$bokhyllatreff[$teller]['kilde'] = "Nasjonalbiblioteket";
			$teller++;
		}
	} // SLUTT PÅ HVERT ENKELT TREFF

foreach ($bokhyllatreff as $singeltreff) {
		$bokhyllatreffhtml = str_replace ("twitterurlString" , urlencode($singeltreff['url']) , $singlehtml);
		$bokhyllatreffhtml = str_replace ("twitterdescriptionString" , htmlspecialchars($tittel). htmlspecialchars(" (".$forfatter.")"), $bokhyllatreffhtml);
        $bokhyllatreffhtml = str_replace ("urlString" , $singeltreff['url'] , $bokhyllatreffhtml);
        $bokhyllatreffhtml = str_replace ("titleString" , trunc($singeltreff['tittel'], 12) , $bokhyllatreffhtml);
        $bokhyllatreffhtml = str_replace ("descriptionString" , trunc($singeltreff['forfatter'], 5) , $bokhyllatreffhtml);
		$bokhyllatreffhtml = str_replace ("omslagString" , $singeltreff['bokomslag'] , $bokhyllatreffhtml);
		$bokhyllatreffhtml = str_replace ("classString" , "bokhyllatreff" , $bokhyllatreffhtml);

        $bokhyllahtml .= $bokhyllatreffhtml;
}


// Let's go bokselskap

$search_string = str_replace ("%22" , "", $search_string);
$search_string = urldecode ($search_string);

if (($_REQUEST['format'] == "undefined2") || ($_REQUEST['format']) == "12") { // bokselskap bare hvis epub valgt!

	$xmldata = simplexml_load_file('includes/publiseringsliste_bokselskap_20140808.xml'); // Denne er lokal, så den funker

// Gå gjennom lista for å finne treff
	$teller = 0;

	foreach ($xmldata->text->body->div->list->item as $entry) {
		if ($teller < $makstreff) {
			$url = $entry->ref->attributes()->target;
			$forfatter = $entry->ref->name;
			$tittel = $entry->ref->title;
			if (mb_stristr($forfatter , $search_string) || mb_stristr($tittel , $search_string)) {
				$bokselskaptreffhtml = str_replace ("twitterurlString" , urlencode($url) , $singlehtml);
				$bokselskaptreffhtml = str_replace ("twitterdescriptionString" , htmlspecialchars($tittel). htmlspecialchars(" (".$forfatter.")"), $bokselskaptreffhtml);
				$bokselskaptreffhtml = str_replace ("urlString" , $url , $bokselskaptreffhtml);
				$bokselskaptreffhtml = str_replace ("titleString" , trunc($tittel, 12) , $bokselskaptreffhtml);
				$bokselskaptreffhtml = str_replace ("descriptionString" , trunc($forfatter, 5) , $bokselskaptreffhtml);
				$bokselskaptreffhtml = str_replace ("omslagString" , $generiskbokomslag , $bokselskaptreffhtml);
				$bokselskaptreffhtml = str_replace ("classString" , "bokselskaptreff" , $bokselskaptreffhtml);
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


			$openlibrarytreffhtml = str_replace ("twitterurlString" , urlencode($url), $singlehtml);
			$openlibrarytreffhtml = str_replace ("twitterdescriptionString" , htmlspecialchars($tittel). htmlspecialchars(" (".$forfatter.")"), $openlibrarytreffhtml);
			$openlibrarytreffhtml = str_replace ("urlString" , $url , $openlibrarytreffhtml);
			$openlibrarytreffhtml = str_replace ("titleString" , trunc($tittel, 12) , $openlibrarytreffhtml);
			$openlibrarytreffhtml = str_replace ("descriptionString" , trunc($forfatter, 5) , $openlibrarytreffhtml);
			$openlibrarytreffhtml = str_replace ("omslagString" , $omslag , $openlibrarytreffhtml);
			$openlibrarytreffhtml = str_replace ("classString" , "openlibrarytreff" , $openlibrarytreffhtml);
			$openlibraryhtml .= $openlibrarytreffhtml;
			$teller++;
		}
	}
}

$openlibraryantalltreff = $totalt;


//echo "<pre>";
//print_r ($results);
//echo "</pre>";

// FERDIG MED Å SØKE - SKRIVE UT RESULTATER

	// SKRIVE UT ANTALL TREFF I ACCORDION

/*
echo "<ul class=\"acc\" id=\"acc\">\n";

	echo "<li>\n";
	echo "<h3>Antall treff Bokhylla: " . (int)$bokhyllaantalltreff;
	if ((int)$bokhyllaantalltreff > $makstreff) {
		echo " (viser bare de " . $makstreff . " f&oslash;rste)";
	}
	echo "</h3>\n";
		echo "<div class=\"acc-section\">\n";
			echo "<div class=\"acc-content\">\n";
				echo $bokhyllahtml;
			echo "</div>\n";
		echo "</div>\n";
	echo "</li>\n";

	echo "<li>\n";
	echo "<h3>Antall treff Bokselskap: " . (int)$bokselskapantalltreff;
	if ((int)$bokselskapantalltreff > $makstreff) {
		echo " (viser bare de " . $makstreff . " f&oslash;rste)";
	}
	echo "</h3>\n";
		echo "<div class=\"acc-section\">\n";
			echo "<div class=\"acc-content\">\n";
				echo $bokselskaphtml;
			echo "</div>\n";
		echo "</div>\n";
	echo "</li>\n";

	echo "<li>\n";
	echo "<h3>Antall treff Open Library: " . (int)$openlibraryantalltreff;
	if ((int)$openlibraryantalltreff > $makstreff) {
		echo " (viser bare de " . $makstreff . " f&oslash;rste)";
	}
	echo "</h3>\n";
		echo "<div class=\"acc-section\">\n";
			echo "<div class=\"acc-content\">\n";
				echo $openlibraryhtml;
			echo "</div>\n";
		echo "</div>\n";
	echo "</li>\n";

echo "</ul>\n";

*/
?>
<script type="text/javascript">
//var parentAccordion=new TINY.accordion.slider("parentAccordion");
//parentAccordion.init("acc","h3",1);
</script>

<ul id="eboktrefftabs" class="ebokshadetabs" style="margin-bottom: 0;">
<li><a href="#" rel="tab1" class="selected">Bokhylla (<?php echo (int)$bokhyllaantalltreff;?>)</a></li>
<li><a href="#" rel="tab2">Bokselskap (<?php echo (int)$bokselskapantalltreff;?>)</a></li>
<li><a href="#" rel="tab3">Open Library (<?php echo (int)$openlibraryantalltreff;?>)</a></li>
</ul>

<div>

<div id="tab1" class="eboktabcontent">
<?php
if ((int)$bokhyllaantalltreff > 0) {
	echo $bokhyllahtml;
} else {
	echo "Ingen treff!";
}
?>
</div>

<div id="tab2" class="eboktabcontent">
<?php
if ((int)$bokselskapantalltreff > 0) {
	echo $bokselskaphtml;
} else {
	echo "Ingen treff!";
}
?>
</div>

<div id="tab3" class="eboktabcontent">
<?php
if ((int)$openlibraryantalltreff > 0) {
	echo $openlibraryhtml;
} else {
	echo "Ingen treff!";
}
?>
</div>

</div>

<script type="text/javascript">

var tabber=new ddtabcontent("eboktrefftabs")
tabber.setpersist(false)
tabber.setselectedClassTarget("link") //"link" or "linkparent"
tabber.init()

</script>
