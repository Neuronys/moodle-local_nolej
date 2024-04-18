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
 * @author      2023 Vincenzo Padula <vincenzo@oc-group.eu>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Plugin
$string['pluginname'] = 'Nolej';
$string['nolej:usenolej'] = 'Erstellen Sie Aktivitäten mit Nolej';

// Settings
$string['apikey'] = 'API-Schlüssel';
$string['apikeyinfo'] = 'Ihr Nolej-API-Schlüssel.';
$string['apikeymissing'] = 'Nolej-API-Schlüssel fehlt. Sie müssen ihn in der Plugin-Konfiguration festlegen.';

// Manage
$string['library'] = 'Nolej-Bibliothek';
$string['modules'] = 'Ihre Nolej-Module';
$string['status'] = 'Status';
$string['created'] = 'Erstellt';
$string['lastupdate'] = 'Letzte Aktualisierung';
$string['editmodule'] = 'Bearbeiten';
$string['createmodule'] = 'Ein neues Nolej-Modul erstellen';
$string['deletemodule'] = 'Löschen';
$string['deletemoduledescription'] = 'Sind Sie sicher, dass Sie dieses Nolej-Modul löschen möchten?';
$string['moduledeleted'] = 'Das Nolej-Modul wurde gelöscht.';
$string['action'] = 'Aktion';
$string['documentinfo'] = 'Modulinfo';
$string['genericerror'] = 'Ein Fehler ist aufgetreten: <pre>{$a->error}</pre>';
$string['moduleview'] = 'Modul anzeigen';

// Status
$string['status_0'] = 'Neues Modul';
$string['status_1'] = 'Transkription in Bearbeitung';
$string['status_2'] = 'Transkription abgeschlossen';
$string['status_3'] = 'Analyse in Bearbeitung';
$string['status_4'] = 'Analyse abgeschlossen';
$string['status_5'] = 'Revision in Bearbeitung';
$string['status_6'] = 'Revision abgeschlossen';
$string['status_7'] = 'Aktivitätsgenerierung in Bearbeitung';
$string['status_8'] = 'Aktivitäten generiert';
$string['status_9'] = 'Fehlgeschlagen';

// Notifications
$string['eventwebhookcalled'] = 'Nolej-Webhook wurde aufgerufen.';
$string['messageprovider:transcription_ok'] = 'Transkription abgeschlossen';
$string['messageprovider:transcription_ko'] = 'Transkription fehlgeschlagen';
$string['messageprovider:analysis_ok'] = 'Analyse abgeschlossen';
$string['messageprovider:analysis_ko'] = 'Analyse fehlgeschlagen';
$string['messageprovider:activities_ok'] = 'Aktivitäten generiert';
$string['messageprovider:activities_ko'] = 'Generierung der Aktivität fehlgeschlagen';
$string['action_transcription_ok'] = 'Transkription ist bereit';
$string['action_transcription_ok_body'] = 'Die Transkription des Dokuments "{$a->title}" wurde am {$a->tstamp} abgeschlossen. Sie können es jetzt überprüfen und mit der Analyse beginnen.';
$string['action_transcription_ko'] = 'Transkription fehlgeschlagen';
$string['action_transcription_ko_body'] = 'Leider ist die Transkription des Dokuments "{$a->title}" am {$a->tstamp} fehlgeschlagen. Fehlermeldung: {$a->errormessage}';
$string['action_analysis_ok'] = 'Analyse ist bereit';
$string['action_analysis_ok_body'] = 'Die Analyse des Dokuments "{$a->title}" wurde am {$a->tstamp} abgeschlossen. Sie können es jetzt überprüfen.';
$string['action_analysis_ko'] = 'Analyse fehlgeschlagen';
$string['action_analysis_ko_body'] = 'Leider ist die Analyse des Dokuments "{$a->title}" am {$a->tstamp} fehlgeschlagen. Fehlermeldung: {$a->errormessage}';
$string['action_activities_ok'] = 'Aktivitäten erfolgreich generiert';
$string['action_activities_ok_body'] = 'Die Aktivitäten des Dokuments "{$a->title}" wurden am {$a->tstamp} generiert.';
$string['action_activities_ko'] = 'Generierung der Aktivität fehlgeschlagen';
$string['action_activities_ko_body'] = 'Leider ist die Generierung der Aktivität des Dokuments "{$a->title}" am {$a->tstamp} fehlgeschlagen. Fehlermeldung: {$a->errormessage}';

// Creation
$string['title'] = 'Titel';
$string['titledesc'] = 'Wählen Sie einen Titel oder lassen Sie ihn leer, und Nolej wird einen Titel für Sie auswählen.';
$string['source'] = 'Quelle';
$string['sourcetype'] = 'Quellentyp';
$string['sourcetypefile'] = 'Datei';
$string['sourcetypeweb'] = 'Webressource';
$string['sourcetypetext'] = 'Text direkt eingeben';
$string['sourcefile'] = 'Datei';
$string['sourceurl'] = 'Web-URL';
$string['sourceurldesc'] = 'Geben Sie eine URL ein';
$string['sourceurltype'] = 'Inhaltstyp';
$string['sourcefreetext'] = 'Text';
$string['sourcedocument'] = 'Dokument';
$string['sourceaudio'] = 'Audio';
$string['sourcevideo'] = 'Video';
$string['sourceweb'] = 'Webinhalt';
$string['language'] = 'Sprache des Inhalts';
$string['languagedesc'] = 'Die Auswahl der richtigen Sprache des Supports hilft Nolej bei einer besseren Analyse.';
$string['create'] = 'Modul erstellen';
$string['modulenotcreated'] = 'Modul nicht erstellt';
$string['modulecreated'] = 'Modul erstellt, Transkription läuft...';
$string['modulenotfound'] = 'Modul nicht gefunden';
$string['errdocument'] = 'Bei der Erstellung des Nolej-Moduls ist ein Fehler aufgetreten:<br><pre>{$a}</pre><br>Bitte versuchen Sie es erneut oder wenden Sie sich an einen Administrator, wenn dieser Fehler weiterhin besteht.';

// Content limits
$string['limitcontent'] = 'Inhaltsbeschränkungen';
$string['limitaudio'] = 'Audio-Beschränkungen';
$string['limitvideo'] = 'Video-Beschränkungen';
$string['limitdoc'] = 'Dokumentenbeschränkungen';
$string['limitmaxduration'] = 'Maximale Dauer';
$string['limitmaxpages'] = 'Maximale Anzahl von Seiten';
$string['limitmaxsize'] = 'Maximale Dateigröße';
$string['limitmincharacters'] = 'Mindestzeichen';
$string['limitmaxcharacters'] = 'Maximale Zeichen';
$string['limittype'] = 'Erlaubte Typen';

// Analysis
$string['analyze'] = 'Analyse starten';
$string['analysisconfirm'] = 'Warnung: Haben Sie die Transkription gründlich überprüft, bevor Sie fortfahren? Sobald die Analyse beginnt, können keine Änderungen mehr vorgenommen werden. Bitte stellen Sie die Genauigkeit vor der Fortsetzung sicher.';
$string['transcription'] = 'Transkription';
$string['missingtranscription'] = 'Transkription fehlt';
$string['analysisstart'] = 'Analyse gestartet';
$string['cannotwritetranscription'] = 'Die Transkription kann nicht auf der Festplatte gespeichert werden. Bitte wenden Sie sich an einen Administrator, wenn dieser Fehler weiterhin besteht.';

// Summary
$string['savesummary'] = 'Zusammenfassung speichern';
$string['summary'] = 'Zusammenfassung';
$string['abstract'] = 'Abstract';
$string['keypoints'] = 'Schlüsselpunkte';
$string['cannotwritesummary'] = 'Die Zusammenfassung kann nicht auf der Festplatte gespeichert werden. Bitte wenden Sie sich an einen Administrator, wenn dieser Fehler weiterhin besteht.';
$string['summarynotsaved'] = 'Die Zusammenfassung konnte nicht aktualisiert werden. Bitte wenden Sie sich an einen Administrator, wenn dieser Fehler weiterhin besteht.';
$string['summarysaved'] = 'Die Zusammenfassung wurde gespeichert.';

// Questions
$string['questions'] = 'Fragen';
$string['savequestions'] = 'Fragen speichern';
$string['questionssaved'] = 'Fragen gespeichert.';
$string['questionsnotsaved'] = 'Die Fragen konnten nicht aktualisiert werden. Bitte wenden Sie sich an einen Administrator, wenn dieser Fehler weiterhin besteht.';
$string['cannotwritequestions'] = 'Die Fragen können nicht auf der Festplatte gespeichert werden. Bitte wenden Sie sich an einen Administrator, wenn dieser Fehler weiterhin besteht.';
$string['questionn'] = 'Frage Nr. {$a}';
$string['question'] = 'Frage';
$string['questiontype'] = 'Fragetyp';
$string['questiontypeopen'] = 'Offene Antwort';
$string['questiontypeftb'] = 'Lücken ausfüllen';
$string['questiontypetf'] = 'Richtig oder Falsch';
$string['questiontypemcq'] = 'Mehrfachauswahlfrage';
$string['questiontypehoq'] = 'Hochstufungsfrage';
$string['questionenable'] = 'Frage aktivieren';
$string['questionuseforgrading'] = 'Zur Bewertung verwenden';
$string['questionanswer'] = 'Antwort';
$string['questionanswertrue'] = 'Richtige Aussage';
$string['questionanswerfalse'] = 'Falsche Aussage';
$string['questiondistractor'] = 'Ablenkung';
$string['questionusedistractor'] = 'Angezeigte Aussage';

// Concepts
$string['concepts'] = 'Konzepte';
$string['saveconcepts'] = 'Konzepte speichern';
$string['cannotwriteconcepts'] = 'Die Konzepte können nicht auf der Festplatte gespeichert werden. Bitte wenden Sie sich an einen Administrator, wenn dieser Fehler weiterhin besteht.';
$string['conceptssaved'] = 'Konzepte gespeichert.';
$string['conceptsnotsaved'] = 'Die Konzepte konnten nicht aktualisiert werden. Bitte wenden Sie sich an einen Administrator, wenn dieser Fehler weiterhin besteht.';
$string['conceptenable'] = 'Aktiviert';
$string['conceptlabel'] = 'Bezeichnung';
$string['conceptdefinition'] = 'Definition';
$string['conceptuseforgaming'] = 'Für Spiele verwenden';
$string['conceptuseforcw'] = 'Kreuzworträtsel';
$string['conceptuseforftw'] = 'Finde das Wort';
$string['conceptusefordtw'] = 'Verbinde das Wort';
$string['conceptuseingames'] = 'Verfügbare Spiele';
$string['conceptuseforpractice'] = 'Für Übungen verwenden';

// Activities
$string['settings'] = 'Generieren';
$string['activities'] = 'Vorschau';
$string['generate'] = 'Aktivitäten generieren';
$string['activitiescrossword'] = 'Kreuzworträtsel';
$string['activitiescwwords'] = 'Legen Sie fest, wie viele Wörter im Kreuzworträtsel verwendet werden sollen';
$string['activitiesdragtheword'] = 'Verbinde das Wort';
$string['activitiesdtwwords'] = 'Legen Sie fest, wie viele Wörter in der Aktivität "Verbinde das Wort" verwendet werden sollen';
$string['activitiesfindtheword'] = 'Finde das Wort';
$string['activitiesflashcardsflashcards'] = 'Legen Sie fest, wie viele Lernkarten in der konzeptuellen Bewertungsaktivität angezeigt werden sollen';
$string['activitiesftwwords'] = 'Legen Sie fest, wie viele Wörter in der Aktivität "Finde das Wort" verwendet werden sollen';
$string['activitiesglossary'] = 'Glossar';
$string['activitiesgrade'] = 'Konzeptuelle Bewertung';
$string['activitiesgradequestions'] = 'Legen Sie fest, wie viele Fragen in der konzeptuellen Bewertungsaktivität angezeigt werden sollen';
$string['activitiesgradeq'] = 'Kontextuelle Bewertung';
$string['activitiesgradeqquestions'] = 'Legen Sie fest, wie viele Fragen in der kontextuellen Bewertungsaktivität angezeigt werden sollen';
$string['activitieshoquestions'] = 'Hochstufungsfragen';
$string['activitiesibook'] = 'Interaktives Buch';
$string['activitiesivideo'] = 'Interaktives Video';
$string['activitiesivideoquestions'] = 'Legen Sie fest, wie viele Fragen pro Satz in der interaktiven Videoaktivität vorgeschlagen werden sollen. Es gibt 2 Sätze: einen in der Mitte des Videos und einen am Ende';
$string['activitiesivideosummary'] = 'Legen Sie fest, ob die Zusammenfassung am Ende der interaktiven Videoaktivität hinzugefügt wird';
$string['activitiespractice'] = 'Konzeptuelle Lernkarten';
$string['activitiespracticeflashcards'] = 'Legen Sie fest, wie viele Fragen in der konzeptuellen Lernkartenaktivität angezeigt werden sollen';
$string['activitiespracticeq'] = 'Kontextuelle Lernkarten';
$string['activitiespracticeqflashcards'] = 'Legen Sie fest, wie viele Fragen in der kontextuellen Lernkartenaktivität angezeigt werden sollen';
$string['activitiesselect'] = 'Aktivität auswählen';
$string['activitiesselected'] = 'Nolej-Aktivität: %s aus dem Modul "%s".';
$string['activitiessummary'] = 'Zusammenfassung';
$string['activitiesuseinibook'] = 'Diese Aktivität im interaktiven Buch verwenden';
$string['activitiesenable'] = '{$a} generieren';
$string['minvalue'] = 'Mindestwert';
$string['maxvalue'] = 'Maximalwert';
$string['cannotwritesettings'] = 'Die Einstellungen können nicht auf der Festplatte gespeichert werden. Bitte wenden Sie sich an einen Administrator, wenn dieser Fehler weiterhin besteht.';
$string['settingsnotsaved'] = 'Die Einstellungen konnten nicht aktualisiert werden. Bitte wenden Sie sich an einen Administrator, wenn dieser Fehler weiterhin besteht.';
$string['generationstarted'] = 'Generierung gestartet';
$string['erractivitiesdecode'] = 'Entschlüsselung der Aktivitätsdaten fehlgeschlagen';
$string['erractivitydownload'] = 'Speichern der Aktivität auf der Festplatte fehlgeschlagen';
$string['errh5psave'] = 'Speichern des h5p-Pakets fehlgeschlagen';
$string['errh5pvalidation'] = 'h5p-Paket ist ungültig';
