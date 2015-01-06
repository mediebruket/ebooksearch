=== Ebook Search by Webloft ===
Contributors: sundaune
Tags: E-bøker, PDF, Nasjonalbiblioteket, Bokselskap, bok, bøker, Open Library, openlibrary, Ebooks, ebook search, search, webløft, webloft
Requires at least: 4.0
Tested up to: 4.1
Stable tag: 1.0.3
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Search for free Norwegian PDF books and e-books from different sources. Norwegian: Søk etter gratis PDF- og e-bøker fra forskjellige kilder.

== Description ==

This plugin searches for PDF books and e-books available from Open Library, the Norwegian National Library and www.bokselskap.no. Upon activation the plugin installs a shortcode available for pages and posts. Using this shortcode will insert a search form - the width of this box in pixels or percent as well as the maximum number of hits to retreieve from each source is specified as arguments to the shortcode. 

The plugin uses jQuery to automatically show the search result under the search forms in real time as you type. If you get a great number of hits you can click on the magnifying glass or hit Enter to open the search results in a sortable table in a new window. 

NORWEGIAN:

Dette innstikket søker blant PDF-bøker og e-bøker fra Open Library, Nasjonalbiblioteket og Bokselskap.no. Når innstikket aktiveres installerer det en kortkode (shortcode) som du kan sette inn i sider og innlegg for å vise en søkeboks. I kortkoden kan det angis bredde på søkeboksen i piksler eller prosent, samt hvor mange treff som maksimalt skal hentes fra hver kilde. 

Innstikket benytter seg av jQuery for å automatisk vise en oppdatert treffliste under søkeboksen etter hvert som du skriver. Får du mange treff kan du klikke på forstørrelsesglasset eller trykke Enter for å åpne trefflisten i et nytt vindu, i en tabell som kan sorteres.

== Installation ==

= Uploading the plugin via the Wordpress control panel = 

Make sure you have downloaded the .zip file containing the plugin. Then:

1. Go to 'Add' on the plugin administration panel
2. Proceed to 'Upload'
3. Choose the .zip file on your local drive containing the plugin
4. Click 'Install now'
5. Activate the plugin from the control panel

= Upload the plugin via FTP =

Make sure you have downloaded the .zip file containing the plugin. Then:

1. Unzip the folder 'finnebok' to your local drive
2. Upload the folder 'finnebok' to the '/wp-content/plugins/' folder (or wherever you store your plugins)
3. Activate the plugin from the control panel

= Or install it via the Wordpress repository! =

To place the search form in your post/page, simply insert this shortcode:

[finnebok_skjema]

This is the simplest way. There are two optional parameters you can pass along:

	1. width : The width of the form. Given in pecentage (e.g. "40%") or pixels (e.g. "400px"). Default is "250px".
	2. makstreff : Maximum number of hits to retrieve from each source. Default is 25.

Example of shortcode to insert a search form 300px wide and fetch a maximum of 50 hits from each source:

[finnebok_skjema width="300px" makstreff="50"]

Note that the search form can be styled to your liking by overruling the CSS included with the plugin. 

NORWEGIAN:

= Laste opp innstikket i kontrollpanelet for Wordpress =

Sørg for at du har lastet ned ZIP-filen som inneholder innstikket. Deretter:

1. Gå til 'Legg til' på administrasjonssiden for innstikk
2. Gå til 'Last opp'
3. Velg ZIP-filen som inneholder innstikket på harddisken din
4. Klikk 'Installer nå'
5. Aktiver innstikket fra kontrollpanelet

= Laste opp innstikket via FTP =

Sørg for at du har lastet ned ZIP-filen som inneholder innstikket. Deretter:

1. Pakk ut mappen 'finnebok' til datamaskinen din
2. Last opp mappen 'finnebok' til '/wp-content/plugins/'-katalogen under din Wordpress-installasjon
3. Aktiver innstikket fra kontrollpanelet

= Eller installér det via Wordpress-katalogen! =

For å sette inn søkeboksen på siden eller i innleget ditt, bruk følgende kortkode:

[finnebok_skjema]

Dette er den enkleste måten. Det er også to valgfrie parametre du kan bruke:

* width : Bredden på boksen. Oppgis i prosent (f.eks. "40%") eller piksler (f.eks. "400px"). Standardverdi er "250px".
* makstreff : Maks. antall treff å hente fra hver kilde. Standardverdi er 25.

Her er et eksempel som setter inn en 300 piksler bred søkeboks og henter maks. 50 treff fra hver kilde:

[finnebok_skjema width="300px" makstreff="50"]

Du kan også bestemme i detalj hvordan søkeboksen skal se ut ved å redigere den medfølgende CSS-filen.

== Frequently Asked Questions ==

= Why are there no frequently asked questions? =

Because the plugin is so new that no question has had the chance to become frequent yet!

NORWEGIAN:

= Hvorfor er det ingen Ofte Stilte Spørsmål? =

Fordi dette er første versjon - spørsmålene kommer nok etter hvert.

== Screenshots ==

1. This is the search form
2. Here is the shortcode as it appears on a page or in a post

NORWEGIAN:

1. Slik ser søkeboksen ut
2. Slik setter du inn kortkoden i sider eller innlegg

== Change log ==

= 1.0.3 = 

* Bugfix: header text not respecting specified width
* Bugfix: Links to Open library titles were wrong (thank you, Håvard Grimelid!)
* Various cosmetic improvements

= 1.0.2 =

* Improved and more readable readme.txt

= 1.0.1 =

* Fixed various errors

= 1.0 =

* First version

NORWEGIAN:

= 1.0.3 =

* Bugfix: Angitt bredde ble ikke respektert i overskriftstekst
* Bugfix: Lenker til verker til Openlibrary var feil (takk, Håvard Grimelid!)
* Forskjellige kosmetiske forbedringer

= 1.0.2 =

* Bedre og mer forståelig readme.txt

= 1.0.1 =

* Fikset forskjellig småtteri

= 1.0 =

* Første versjon

== Upgrade Notice ==

This version primarily fixes the bug where Openlibrary links came out wrong.

NORWEGIAN:

Denne versjonen tjener først og fremst til å fikse feilen hvor Openlibrary-lenker var feil programmert inn.
