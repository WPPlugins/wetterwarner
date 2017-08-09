=== Wetterwarner ===
Contributors: bocanegra
Tags: Wetter, Warnungen, Warnung, Sturm, Meldung, Wetterdienst, Unwetter, Widget
Requires at least: 3.6
Tested up to: 4.8
Stable tag: 2.2
License: GPLV2
License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.de.html

Amtliche Wetterwarnungen für Deine eingestellte Region, werden in einem Widget für deine Besucher angezeigt.

== Description ==
Wetterwarner zeigt amtliche Wetterwarnungen vom deutschen Wetterdienst in einem Widget an.

Darüber hinaus noch vieles mehr... 

Optional kann beispielsweise eine Wetterkarte angezeigt werden, oder auch das ganze Widget ausgeblendet werden, falls keine Warnungen für die eingestellte Warnregion vorhanden sind. 
Die Karte aktualisiert sich selbstständig und wird herausgegeben vom Deutschen Wetterdienst.

Funktionen im Überblick:

* Anzeige von beliebig vielen Wetterwarnungen
* Widget Texte komplett frei einstellbar
* Einfache integration in Wordpress Theme
* Optional: Deutsche (oder Bundesland bezogene) Wetterkarte in anpassbarer Größe
* Optional: Mouseover Effekt -  Erweiterter Warnungstext wird angezeigt
* Optional: Icons vor den Wettermeldungen
* Optional: Cache Funktion welche die Daten auf deinem Webspace zwischengespeichert, um die benötigten Daten schneller zu laden
* Und vieles mehr...

= Wichtige Informationen zum Bild =
Die verwendete Wetterkarte entstammt der Seite www.dwd.de - Beim Aufrufen des Widgets wird das Bild von der genannten Externen Seite geladen. Sämtliche Bildrechte liegen bei dem Betreiber. Ich weise ausdrücklich darauf hin, im Namen des Betreibers, dass das Bild nicht verändert werden darf! Ich hafte nicht für eventuelle Verstöße des Copyrights.
Weitere Informationen: www.wettergefahren.de/copyright.html

= Wichtige Informationen zur Informations Quelle =
Als Quelle der Informationen werden RSS Feeds der Webseite http://wettwarn.de genutzt. Diese Seite nutzt amtliche Meldungen des Deutschen Wetterdienst. Sämtliche Urheberrechte verbleiben bei dem Betreiber. Ich hafte nicht für eventuelle Verstöße des Copyrights.

= Weitere Informationen =
Dieses Plugin sollte in keinem Fall einer amtlichen Informationsquelle vorgezogen werden und dient nur zur Erstinformation. Die Meldungen sind teilweise bewusst gekürzt und daher nicht vollständig!

Das Plugin "Wetterwarner" wurde mit größter sorgfalt erstellt und getestet. Ich hafte nicht für entstadene Schäden oder Fehlfunktionen. Nur für die Nutzung in Deutschland vorgesehen. Generelle Nutzung auf eigene Gefahr! In keinster Weise steht dieses Plugin in Verbindung mit der gleichnamigen Android/iOS App. 

= Credits  =
[Font Awesome](http://fontawesome.io) by Dave Gandy
[PopUp text boxes](http://nicolashoening.de?twocents&nr=8) by Nicolas Höning
[Weather Icons project](https://erikflowers.github.io/weather-icons/) by Erik Flowers
[wp-color-picker-alpha](https://github.com/23r9i0/wp-color-picker-alpha) by Sergio P.A.

== Installation ==
1. Lade die Dateien unverändert aus der ZIP-Datei in das Wordpress Pluginverzeichnis: /wp-content/plugins oder installiere es direkt über die Plugin Seite deines Blogs.
2. Aktiviere das Plugin im Menü "Installierte Plugins" deines Blogs
3. Nun solltest du es unter Design --> Widgets nutzen können.

Stelle sicher, bei aktiviertem Caching, dass der /tmp/ Ordner im Wetterwarnter Verzeichnis Schreibberechtigungen besitzt. (777)
Allgemeine Einstellungen findest du direkt in der Widget Konfiguration. Weiteres unter Einstellungen --> Wetterwarner.

== Frequently Asked Questions ==

= Welche Feed ID hat meine Stadt? =

Besuche hierfür die Seite: http://wettwarn.de/warnregion, wähle die Warnregion und trage die ID der Region in die Widget Konfiguration ein. 
Weitere Hilfe findest du in der Dokumentation: http://tim.knigge-ronnenberg.de/projekte/wetterwarner/dokumentation/#feed-id

= Warum muss die Feed ID genau von wettwarn.de sein? =

Die RSS Feeds dieser Seite werden als Quelle der Meldungen genutzt.

= Wie erreiche ich den Entwickler? =
Am besten nutzt du einfach das [Ticket System](http://support.knigge-ronnenberg.de/open.php) oder das Kontaktformular in meinem [Blog](http://tim.knigge-ronnenberg.de/kontakt/)

= Wie kann im meine Einstellungen testen? =
Es gibt seit Version 2.1 einen Demomodus, einfach als Feed ID "100" eintragen und schon werden Beispielmeldungen angezeigt!

= Fehlermeldung: "Externes laden von Inhalten in PHP.ini deaktiviert" - Was ist zu tun? =
Zum Hintergrund: In der php.ini Datei können Einstellungen vorgenommen werden, die das Verhalten von PHP und damit auch das Verhalten der PHP Scripte auf dem Server beeinflussen.
Wichtig für dieses Plugin ist, dass die Einstellung "allow_url_fopen " auf ON steht. Sonst kann Wetterwarner nicht auf den externen RSS Feed bzw. die Wetterkarte zugreifen.
Bitte erkundigt Euch bei eurem Provider, wie ihr die Einstellung in der php.ini verändern könnt.

= Die Wetterkarte verändert sich nicht nach Änderung der Einstellungen, woran liegt das? =
Bei aktiviertem Caching wird derzeit die Karte nicht sofort neu geladen, sondern erst wenn der Cache erneuter wird (i.d.R. nach max 10 Minuten), daher bitte ein wenig Geduld, oder das Caching temporär abschalten!

= Technische Vorraussetzungen =
Folgende Vorraussetzungen muss dein Webspace erfüllen:

*PHP Einstellung "allow_url_fopen = ON"
*PHP Version > 5.3.10
*PHP Erweiterung "CURL" ==> Optional für Cache Funktion

== Screenshots ==
1. Widget im Front-End Bereich
2. Widget Konfiguration im Back-End
3. Weitere Optionen über WordPress Einstellungen

== Changelog ==
= 2.2 =
* Neu: Das Icon Paket wurde ausgetauscht und beinhaltet nun noch mehr Vielfalt.
* Optimierung: Das Design der Meldungen wurde optimiert.
* Optimierung: Vorabinformation werden nun besser als solche kenntlich gemacht.

= 2.1.3 =
* Bugfix: Wenn "Immer anzeigen" deaktiviert und keine Wetterwarnungen aktiv waren, verursachte Wetterwarner einen Darstellungsfehler auf der Webseite.
* Optimierung: Font-Awesome wird nicht mehr im Plugin Ordner vorgehalten, sodern direkt online abgerufen.

= 2.1.2 =
* Bugfix: Die Cache Funktion weißt nun keine Fehler mehr auf (CURL PHP-Erweiterung vorrausgesetzt)
* Optimierung: Fehlerhandling verbessert
* Optimierung: Speichervorgang bei Änderungen im Widget optimiert
* Optimierung: Aufzählungszeichen vor den Wettermeldungen standardmäßig ausgeblendet

= 2.1.1 =
* Neu: Die Verlinkung einer Wetterwarnung auf die detailierte Informationsseite ist nun deaktivierbar.
* Weitere Quellcode Optimierungen
* Kompatibilität zu WordPress 4.8 sichergestellt

= 2.1 =
* Neu: Vorabinformation werden nun auch wie gewünscht angezeigt
* Neu: Meldungen können nun, abhängig von der Warnstufe, definierbare Hintergrundfarben erhalten
* Neu: Demomodus verfügbar der Beispielmeldungen anzeigen kann
* Kompatibilität zu WordPress 4.7 sichergestellt

= 2.0.1 =
* Bugfix: Widget blockiert nicht mehr eine Zeile, wenn Einleitungs und/oder Hinweistext leer sind
* Bugfix: Der Cache funktioniert nun auch auf Webseiten die über eine Subdomain laufen.
* Weitere Quellcode Optimierungen
* Kompatibilität zu WordPress 4.6 sichergestellt

= 2.0 =
* Neu: Automatisches Caching für externe Inhalte implementiert - Die externen Inhalte (RSS Datei + Wetterkarte) können nun auf dem lokalen Webspace zwischengespeichert werden um die Performance zu steigern.
* Neu: Eigene Optionsseite im WordPress Backend unter "Einstellungen". Dort kann der Cache aktiviert und die Konfiguration überprüft werden. Zudem gibt es eine Debug Funktion.
* Optimierung: Plugin PHP Dateien vor direktem Aufruf geschützt
* Optimierung: Komplette neuorganisation aller benötigten Skripte und Dateien
* Optimierung: Tooltip enthält detaillierten Text
* Bugfix: Auftretende Buchstaben vor den Icos behoben

= 1.4 =
* Neu: Optional kann beim Mouseover der Meldung, der erweiterte Warnungstext angezeigt werden.
* Neu: Integrations von 'Meteocons':  Zu den Wetterwarnungen kann ein Icon angezeigt werden. Derzeit werden noch nicht alle Unwetterwarnungen unterstützt.
* Optimierung: Eigenes Stylesheet implementiert
* Weitere Quellcode Optimierungen
* Kompatibilität zu WordPress 4.5 sichergestellt

= 1.2.1 =
* Bugfix: Anzahl maximale Meldunge / Kartengröße durfte nicht "0"  sein
* Bugfix: Komplikationen mit PHP 7 behoben
* Optimierung: Aufgrund von Quellcode Optimierungen ist die Laufzeit des Widget nun deutlich performanter

= 1.2 =
* Neu: Wetterkarte kann nun nur ein bestimmtes Bundesland anzeigen.
* Optimierung: Es wird nun eine wichtige PHP Einstellung eures Webspaces vor dem laden des Widgets überprüft.
* Optimierung: Beim auftreten eines Fehlers, wird nun nicht mehr das Laden der kompletten Webseite gestoppt.

= 1.1 =
* Bugfix: Kartengröße nicht änderbar
* Bugfix: Anzahl maximale Meldungen nicht änderbar

= 1.0 =
* Neu: Die Gültigkeit der Meldungen lässt sich optional anzeigen
* Neu: Die Quelle der Meldungen lässt sich optional anzeigen
* Neu: Font-Awesome ist nun komplett integriert
* Einstellmöglichkeit aufgeräumt: komplett neu Angeordnet und sortiert

= 0.4.5 =
* Bugfix: Widget wird nicht mehr zweimal generiert, wenn Warnungen vorhanden sind und "Immer anzeigen" aktiviert ist.
* Meldungen bei Fehlern präziser formuliert.

= 0.4.0 =
* Neu: Das Widget lässt sich nun optional ausblenden, wenn keine Meldung für die Eingestellte Warnregion vorhaden ist.
* Neu: Der RSS Feed Link lässt sich ebenfalls optional ausblenden.
* Sortierung der Optionen in der Widget-Konfiguration angepasst
* Optimierung des Quellcodes

= 0.3.0 =
* Neu: In den Einstellungen kann nun die Variable %region% verwendet werden (Zeigt den Namen der eingestellten Warnregion an)
* Author URL / Plugin Hilfe auf meinen neuen Blog verlinkt; Absofort ist dort eine Dokumentation verfügbar, siehe FAQ
* Das Widget blendet sich nicht mehr automatisch aus, sondern zeigt "Keine Meldungen...", bis die Wählbareoption in den Einstellungen implementiert ist.
* kleinere Anpassungen im Quellcode

= 0.2.0 =
* Neu: Maximale Anzahl an anzuzeigenden Meldungen kann nun eingestellt werden
* Es ist nun nicht mehr erforderlich die Feed ID in kleinbuchstaben einzutragen.
* Verlinkung zu Wettwarn auf Permalink umgestellt
* kleinere Anpassungen im Quellcode

= 0.1.1 =
* Bugfix: Fehlermeldung nach Installation behoben

= 0.1.0 =
* Veröffentlichung der ersten Version
* Widget zeigt Meldungen von gewählter Warnregion an
* Wetterkarte optional sichtbar

== Upgrade Notice ==
= 2.2 =
Neues Icon Paket und Optimierungen am Design
= 2.1.3 =
Sorry, erneute Fehlerbehebung :-/

= 2.1.2 =
Fehlerbehebung

= 2.1.1 =
Kompatibilität zu WordPress 4.8 sichergestellt, Optimierungen am Quellcode

= 2.1 =
Kompatibilität zu WordPress 4.7 sichergestellt sowie Neue Features 

= 2.0.1 =
Kompatibilität zu WordPress 4.6 sichergestellt sowie Fehler behoben

= 2.0 =
Wetterwarner hat "unter der Haube" ein großes Update bekommen. Fast alles wurde neu geschrieben und optimiert.

= 1.3 =
Neue Features und Optimierungen

= 1.2.1 =
Nur ein kleines Update, hauptsächlich mit Optimierungen am Quellcode.

= 1.2 =
Die Wetterkarte kann nun nur ein bestimmtes Bundesland anzeigen. Zudem werden nun wichtige technische Vorraussetzungen automatisch geprüft.

= 1.1 =
Fehlerbehebung

= 1.0 =
Die Version 1.0 ist da, mit vielen Verbesserungen und neuen Funktionen.

= 0.4.5 =
Fehlerbehebung

= 0.4.0 =
Kompatibilität zu Wordress 4.4 sichergestellt, neue Einstellmöglichkeiten

= 0.3.0 =
Neues Feature "Variablen", Plugin Hilfe verfügbar

= 0.2.0 =
Neue Einstellmöglichkeit

= 0.1.1 =
Fehlerbehebung

= 0.1.0 =
Erste Version