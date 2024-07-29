<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for local_nolej
 *
 * @package     local_nolej
 * @author      Vincenzo Padula <vincenzo@oc-group.eu>
 * @copyright   2024 OC Open Consulting SB Srl
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Plugin.
$string['pluginname'] = 'Nolej';
$string['nolej:usenolej'] = 'Activiteiten maken met Nolej';

// Privacy API.
$string['privacy:metadata:nolej_module'] = 'Informatie over de auteur van de Nolej-module.';
$string['privacy:metadata:nolej_module:user_id'] = 'De ID van de gebruiker die de module heeft gemaakt.';
$string['privacy:metadata:nolej_module:tstamp'] = 'De tijdstempel toen de gebruiker de module maakte.';
$string['privacy:metadata:nolej_activity'] = 'Informatie over de gebruiker die de Nolej API heeft aangeroepen.';
$string['privacy:metadata:nolej_activity:user_id'] = 'De ID van de gebruiker die de API heeft aangeroepen.';
$string['privacy:metadata:nolej_activity:tstamp'] = 'De tijdstempel toen de gebruiker de API aanriep.';
$string['privacy:metadata:nolej_activity:action'] = 'De API die de gebruiker heeft aangeroepen.';
$string['privacy:metadata:endpoint'] = 'Om te integreren met Nolej, moeten gebruikersgegevens met die service worden uitgewisseld.';
$string['privacy:metadata:endpoint:user_id'] = 'De gebruikers-ID wordt vanuit Moodle verzonden om u toegang te geven tot uw gegevens op het externe systeem.';
$string['privacy:metadata:core_files'] = 'De Nolej-plugin slaat bestanden op die door de gebruiker zijn geüpload om modules te maken en te worden geanalyseerd door AI.';

// Settings.
$string['apikey'] = 'API-sleutel';
$string['apikeyinfo'] = 'Uw Nolej API-sleutel.';
$string['apikeyhowto'] = 'Om een API-sleutel te verkrijgen, moet u eerst een account aanmaken op live.nolej.io en vervolgens contact opnemen met Nolej via moodle@nolej.io, waarbij u een API-sleutel aanvraagt voor uw geregistreerde e-mailadres.';
$string['apikeymissing'] = 'Nolej API-sleutel ontbreekt. U moet deze instellen in de plug-inconfiguratie.';

// Manage.
$string['library'] = 'Nolej-bibliotheek';
$string['modules'] = 'Uw Nolej-modules';
$string['status'] = 'Status';
$string['created'] = 'Aangemaakt';
$string['lastupdate'] = 'Laatste update';
$string['editmodule'] = 'Bewerken';
$string['createmodule'] = 'Een nieuwe Nolej-module maken';
$string['deletemodule'] = 'Verwijderen';
$string['deletemoduledescription'] = 'Weet u zeker dat u deze Nolej-module wilt verwijderen?';
$string['moduledeleted'] = 'De Nolej-module is verwijderd.';
$string['action'] = 'Actie';
$string['documentinfo'] = 'Module-informatie';
$string['genericerror'] = 'Er is een fout opgetreden: <pre>{$a->error}</pre>';
$string['moduleview'] = 'Bekijk module';
$string['pollinginterval'] = 'Polling-interval';
$string['pollingintervalinfo'] = 'Duur van het interval in seconden, waarna wordt gecontroleerd of er updates zijn op de modules. Minimaal 1 seconde.';

// Status.
$string['statuscreation'] = 'Nieuwe module';
$string['statuscreationpending'] = 'Transcriptie bezig';
$string['statusanalysis'] = 'Transcriptie voltooid';
$string['statusanalysispending'] = 'Analyse bezig';
$string['statusrevision'] = 'Analyse voltooid';
$string['statusrevisionpending'] = 'Revisie bezig';
$string['statusactivities'] = 'Revisie voltooid';
$string['statusactivitiespending'] = 'Activiteiten genereren bezig';
$string['statuscompleted'] = 'Activiteiten gegenereerd';
$string['statusfailed'] = 'Mislukt';

// Notifications.
$string['eventwebhookcalled'] = 'Nolej webhook is opgeroepen.';
$string['messageprovider:transcription_ok'] = 'Transcriptie voltooid';
$string['messageprovider:transcription_ko'] = 'Transcriptie mislukt';
$string['messageprovider:analysis_ok'] = 'Analyse voltooid';
$string['messageprovider:analysis_ko'] = 'Analyse mislukt';
$string['messageprovider:activities_ok'] = 'Activiteiten gegenereerd';
$string['messageprovider:activities_ko'] = 'Genereren van activiteiten mislukt';
$string['action_transcription_ok'] = 'Transcriptie is klaar';
$string['action_transcription_ok_body'] = 'De transcriptie van document "{$a->title}" is voltooid op {$a->tstamp}, u kunt het nu controleren en de analyse starten.';
$string['action_transcription_ko'] = 'Transcriptie mislukt';
$string['action_transcription_ko_body'] = 'Helaas is de transcriptie van document "{$a->title}" mislukt op {$a->tstamp}. Foutmelding: {$a->errormessage}';
$string['action_analysis_ok'] = 'Analyse is klaar';
$string['action_analysis_ok_body'] = 'De analyse van document "{$a->title}" is voltooid op {$a->tstamp}, u kunt het nu controleren.';
$string['action_analysis_ko'] = 'Analyse mislukt';
$string['action_analysis_ko_body'] = 'Helaas is de analyse van document "{$a->title}" mislukt op {$a->tstamp}. Foutmelding: {$a->errormessage}';
$string['action_activities_ok'] = 'Activiteiten succesvol gegenereerd';
$string['action_activities_ok_body'] = 'Activiteiten van document "{$a->title}" zijn gegenereerd op {$a->tstamp}.';
$string['action_activities_ko'] = 'Genereren van activiteiten mislukt';
$string['action_activities_ko_body'] = 'Helaas is het genereren van activiteiten voor document "{$a->title}" mislukt op {$a->tstamp}. Foutmelding: {$a->errormessage}';

// Creation.
$string['title'] = 'Titel';
$string['titledesc'] = 'Kies een titel of laat het leeg en Nolej zal een titel voor u kiezen.';
$string['source'] = 'Bron';
$string['sourcetype'] = 'Soort bron';
$string['sourcetypefile'] = 'Bestand';
$string['sourcetypeweb'] = 'Webbron';
$string['sourcetypetext'] = 'Schrijf tekst direct';
$string['sourcefile'] = 'Bestand';
$string['sourceurl'] = 'Web-URL';
$string['sourceurldesc'] = 'Schrijf een URL';
$string['sourceurltype'] = 'Soort inhoud';
$string['sourcefreetext'] = 'Tekst';
$string['sourcedocument'] = 'Document';
$string['sourceaudio'] = 'Audio';
$string['sourcevideo'] = 'Video';
$string['sourceweb'] = 'Webinhoud';
$string['language'] = 'Inhoudstaal';
$string['languagedesc'] = 'Het kiezen van de juiste taal van de media helpt Nolej om het beter te analyseren.';
$string['create'] = 'Module maken';
$string['modulenotcreated'] = 'Module niet gemaakt';
$string['modulecreated'] = 'Module gemaakt, transcriptie bezig. U ontvangt een melding wanneer de transcriptie is voltooid.';
$string['modulenotfound'] = 'Module niet gevonden';
$string['errdatamissing'] = 'Sommige gegevens ontbreken';
$string['errdocument'] = 'Er is een fout opgetreden bij het maken van de Nolej-module:<br><pre>{$a}</pre><br>Probeer het opnieuw of neem contact op met een beheerder als deze fout aanhoudt.';

// Content limits.
$string['limitcontent'] = 'Inhoudsbeperkingen';
$string['limitaudio'] = 'Audio beperkingen';
$string['limitvideo'] = 'Videobeperkingen';
$string['limitdoc'] = 'Documentbeperkingen';
$string['limitmaxduration'] = 'Maximale duur: {$a} minuten.';
$string['limitmaxpages'] = 'Maximaal aantal pagina\'s: {$a}.';
$string['limitmaxsize'] = 'Maximale bestandsgrootte: {$a} GB.';
$string['limitmincharacters'] = 'Minimale tekens: {$a}.';
$string['limitmaxcharacters'] = 'Maximale tekens: {$a}.';
$string['limittype'] = 'Toegestane typen: {$a}.';

// Analysis.
$string['analyze'] = 'Start analyse';
$string['analysisconfirm'] = 'Waarschuwing: heeft u de transcriptie grondig bekeken voordat u verder gaat? Zodra de analyse begint, kunnen er geen wijzigingen meer worden aangebracht. Zorg ervoor dat alles nauwkeurig is voordat u doorgaat.';
$string['transcription'] = 'Transcriptie';
$string['missingtranscription'] = 'Transcriptie ontbreekt';
$string['analysisstart'] = 'Analyse gestart. U ontvangt een melding wanneer de analyse is voltooid.';
$string['cannotwritetranscription'] = 'Kan transcriptie niet op schijf opslaan, neem contact op met een beheerder als deze fout aanhoudt.';

// Summary.
$string['summary'] = 'Samenvatting';
$string['abstract'] = 'Abstract';
$string['keypoints'] = 'Belangrijke punten';
$string['cannotwritesummary'] = 'Kan samenvatting niet op schijf opslaan, neem contact op met een beheerder als deze fout aanhoudt.';
$string['summarynotsaved'] = 'Kon de samenvatting niet bijwerken. Neem contact op met een beheerder als deze fout aanhoudt.';
$string['summarysaved'] = 'Samenvatting is opgeslagen.';

// Questions.
$string['questions'] = 'Vragen';
$string['questionssaved'] = 'Vragen opgeslagen.';
$string['questionsnotsaved'] = 'Kon de vragen niet bijwerken. Neem contact op met een beheerder als deze fout aanhoudt.';
$string['cannotwritequestions'] = 'Kan vragen niet op schijf opslaan, neem contact op met een beheerder als deze fout aanhoudt.';
$string['questionn'] = 'Vraag #{$a}';
$string['question'] = 'Vraag';
$string['questiontype'] = 'Soort vraag';
$string['questiontypeopen'] = 'Open antwoord';
$string['questiontypeftb'] = 'Vul de lege plekken in';
$string['questiontypeftbmissingblank'] = 'Voor de activiteit "Vul de lege plekken in" is een tijdelijke aanduiding voor het ontbrekende woord vereist. Gebruik 4 (vier) onderstrepingstekens "____" om het ontbrekende woord aan te geven.';
$string['questiontypetf'] = 'Waar of niet waar';
$string['questiontypemcq'] = 'Meerkeuzevraag';
$string['questiontypehoq'] = 'Vraag van hogere orde';
$string['questionenable'] = 'Vraag inschakelen';
$string['questionuseforgrading'] = 'Gebruiken voor beoordeling';
$string['questionanswer'] = 'Antwoord';
$string['questionanswertrue'] = 'Juiste bewering';
$string['questionanswerfalse'] = 'Onjuiste bewering';
$string['questiondistractor'] = 'Distractor';
$string['questionusedistractor'] = 'Bewering om weer te geven';

// Concepts.
$string['concepts'] = 'Concepten';
$string['cannotwriteconcepts'] = 'Kan concepten niet op schijf opslaan, neem contact op met een beheerder als deze fout aanhoudt.';
$string['conceptssaved'] = 'Concepten opgeslagen.';
$string['conceptsnotsaved'] = 'Kon de concepten niet bijwerken. Neem contact op met een beheerder als deze fout aanhoudt.';
$string['conceptenable'] = 'Ingeschakeld';
$string['conceptlabel'] = 'Label';
$string['conceptdefinition'] = 'Definitie';
$string['conceptuseforgaming'] = 'Gebruik voor games';
$string['conceptuseforcw'] = 'Kruiswoordraadsel';
$string['conceptuseforftw'] = 'Woord vinden';
$string['conceptusefordtw'] = 'Sleep het woord';
$string['conceptuseingames'] = 'Beschikbare games';
$string['conceptuseforpractice'] = 'Gebruik voor oefenen';

// Activities.
$string['settings'] = 'Genereren';
$string['activities'] = 'Voorbeeld';
$string['generate'] = 'Activiteiten genereren';
$string['activitiescrossword'] = 'Kruiswoordraadsel';
$string['activitiescwwords'] = 'Geeft aan hoeveel woorden moeten worden gebruikt in de activiteit Kruiswoordraadsel';
$string['activitiesdragtheword'] = 'Sleep het woord';
$string['activitiesdtwwords'] = 'Geeft aan hoeveel woorden moeten worden gebruikt in de activiteit Sleep het woord';
$string['activitiesfindtheword'] = 'Woord vinden';
$string['activitiesflashcardsflashcards'] = 'Geeft aan hoeveel flashcards moeten verschijnen in de conceptuele beoordelingsactiviteit';
$string['activitiesftwwords'] = 'Geeft aan hoeveel woorden moeten worden gebruikt in de activiteit Woord vinden';
$string['activitiesglossary'] = 'Woordenlijst';
$string['activitiesgrade'] = 'Conceptuele beoordeling';
$string['activitiesgradequestions'] = 'Geeft aan hoeveel vragen moeten verschijnen in de conceptuele beoordelingsactiviteit';
$string['activitiesgradeq'] = 'Contextuele beoordeling';
$string['activitiesgradeqquestions'] = 'Geeft aan hoeveel vragen moeten verschijnen in de contextuele beoordelingsactiviteit';
$string['activitieshoquestions'] = 'Vragen van hogere orde';
$string['activitiesibook'] = 'Interactief boek';
$string['activitiesivideo'] = 'Interactieve video';
$string['activitiesivideoquestions'] = 'Geeft aan hoeveel vragen er per set moeten worden voorgesteld in de Interactieve video-activiteit. Er zijn 2 sets: één in het midden van de video, één aan het einde van de video';
$string['activitiesivideosummary'] = 'Geeft aan of de samenvatting aan het einde van de Interactieve video-activiteit wordt toegevoegd';
$string['activitiespractice'] = 'Conceptuele flashcards';
$string['activitiespracticeflashcards'] = 'Geeft aan hoeveel vragen moeten verschijnen in de conceptuele flashcard-activiteit';
$string['activitiespracticeq'] = 'Contextuele flashcards';
$string['activitiespracticeqflashcards'] = 'Geeft aan hoeveel vragen moeten verschijnen in de contextuele flashcard-activiteit';
$string['activitiesselect'] = 'Selecteer een activiteit';
$string['activitiesselected'] = 'Nolej-activiteit: %s van module "%s".';
$string['activitiessummary'] = 'Samenvatting';
$string['activitiesuseinibook'] = 'Gebruik deze activiteit in het interactieve boek';
$string['activitiesenable'] = '{$a} genereren';
$string['minvalue'] = 'Minimale waarde';
$string['maxvalue'] = 'Maximale waarde';
$string['cannotwritesettings'] = 'Kan instellingen niet op schijf opslaan, neem contact op met een beheerder als deze fout aanhoudt.';
$string['settingsnotsaved'] = 'Kon de instellingen niet bijwerken. Neem contact op met een beheerder als deze fout aanhoudt.';
$string['generationstarted'] = 'Generatie gestart. U ontvangt een melding wanneer de activiteiten zijn gegenereerd.';
$string['erractivitiesdecode'] = 'Kan activiteitengegevens niet decoderen';
$string['erractivitydownload'] = 'Kan activiteit niet op schijf opslaan';
$string['errh5psave'] = 'Kan h5p-pakket niet opslaan';
$string['errh5pvalidation'] = 'h5p-pakket is niet geldig';
