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
$string['nolej:usenolej'] = 'Crea attività con Nolej';

// Settings
$string['apikey'] = 'Chiave API';
$string['apikeyinfo'] = 'La tua chiave API di Nolej.';
$string['apikeymissing'] = 'Manca la chiave API di Nolej. È necessario impostarla nella configurazione del plugin.';

// Manage
$string['library'] = 'Libreria Nolej';
$string['modules'] = 'I tuoi moduli Nolej';
$string['status'] = 'Stato';
$string['created'] = 'Creato';
$string['lastupdate'] = 'Ultimo aggiornamento';
$string['editmodule'] = 'Modifica';
$string['createmodule'] = 'Crea un nuovo modulo Nolej';
$string['deletemodule'] = 'Elimina';
$string['deletemoduledescription'] = 'Sei sicuro di voler eliminare questo modulo Nolej?';
$string['moduledeleted'] = 'Il modulo Nolej è stato eliminato.';
$string['action'] = 'Azione';
$string['documentinfo'] = 'Informazioni sul modulo';
$string['genericerror'] = 'Si è verificato un errore: <pre>{$a->error}</pre>';
$string['moduleview'] = 'Visualizza modulo';

// Status
$string['status_0'] = 'Nuovo modulo';
$string['status_1'] = 'Trascrizione in corso';
$string['status_2'] = 'Trascrizione completata';
$string['status_3'] = 'Analisi in corso';
$string['status_4'] = 'Analisi completata';
$string['status_5'] = 'Revisione in corso';
$string['status_6'] = 'Revisione completata';
$string['status_7'] = 'Generazione attività in corso';
$string['status_8'] = 'Attività generate';
$string['status_9'] = 'Fallito';

// Notifications
$string['eventwebhookcalled'] = 'Il webhook di Nolej è stato chiamato.';
$string['messageprovider:transcription_ok'] = 'Trascrizione completata';
$string['messageprovider:transcription_ko'] = 'Trascrizione fallita';
$string['messageprovider:analysis_ok'] = 'Analisi completata';
$string['messageprovider:analysis_ko'] = 'Analisi fallita';
$string['messageprovider:activities_ok'] = 'Attività generate';
$string['messageprovider:activities_ko'] = 'Generazione attività fallita';
$string['action_transcription_ok'] = 'Trascrizione pronta';
$string['action_transcription_ok_body'] = 'La trascrizione del documento "{$a->title}" è stata completata il {$a->tstamp}, ora puoi controllarla e iniziare l\'analisi.';
$string['action_transcription_ko'] = 'Trascrizione fallita';
$string['action_transcription_ko_body'] = 'Purtroppo, la trascrizione del documento "{$a->title}" è fallita il {$a->tstamp}. Messaggio di errore: {$a->errormessage}';
$string['action_analysis_ok'] = 'Analisi pronta';
$string['action_analysis_ok_body'] = 'L\'analisi del documento "{$a->title}" è stata completata il {$a->tstamp}, ora puoi rivederla.';
$string['action_analysis_ko'] = 'Analisi fallita';
$string['action_analysis_ko_body'] = 'Purtroppo, l\'analisi del documento "{$a->title}" è fallita il {$a->tstamp}. Messaggio di errore: {$a->errormessage}';
$string['action_activities_ok'] = 'Attività generate con successo';
$string['action_activities_ok_body'] = 'Le attività del documento "{$a->title}" sono state generate il {$a->tstamp}.';
$string['action_activities_ko'] = 'Generazione attività fallita';
$string['action_activities_ko_body'] = 'Purtroppo, la generazione delle attività del documento "{$a->title}" è fallita il {$a->tstamp}. Messaggio di errore: {$a->errormessage}';

// Creation
$string['title'] = 'Titolo';
$string['titledesc'] = 'Scegli un titolo o lascialo vuoto e Nolej sceglierà un titolo per te.';
$string['source'] = 'Fonte';
$string['sourcetype'] = 'Tipo di fonte';
$string['sourcetypefile'] = 'File';
$string['sourcetypeweb'] = 'Risorsa Web';
$string['sourcetypetext'] = 'Scrivi testo direttamente';
$string['sourcefile'] = 'File';
$string['sourceurl'] = 'Indirizzo web';
$string['sourceurldesc'] = 'Scrivi un URL';
$string['sourceurltype'] = 'Tipo di contenuto';
$string['sourcefreetext'] = 'Testo';
$string['sourcedocument'] = 'Documento';
$string['sourceaudio'] = 'Audio';
$string['sourcevideo'] = 'Video';
$string['sourceweb'] = 'Contenuto Web';
$string['language'] = 'Lingua del contenuto';
$string['languagedesc'] = 'Scegliere la lingua corretta del supporto aiuta Nolej ad analizzarlo meglio.';
$string['create'] = 'Crea modulo';
$string['modulenotcreated'] = 'Modulo non creato';
$string['modulecreated'] = 'Modulo creato, trascrizione in corso...';
$string['modulenotfound'] = 'Modulo non trovato';
$string['errdocument'] = 'Si è verificato un errore durante la creazione del modulo Nolej:<br><pre>{$a}</pre><br>Si prega di riprovare o contattare un amministratore se questo errore persiste.';

// Content limits
$string['limitcontent'] = 'Limiti del contenuto';
$string['limitaudio'] = 'Limiti audio';
$string['limitvideo'] = 'Limiti video';
$string['limitdoc'] = 'Limiti documento';
$string['limitmaxduration'] = 'Durata massima';
$string['limitmaxpages'] = 'Numero massimo di pagine';
$string['limitmaxsize'] = 'Dimensione massima del file';
$string['limitmincharacters'] = 'Caratteri minimi';
$string['limitmaxcharacters'] = 'Caratteri massimi';
$string['limittype'] = 'Tipi consentiti';

// Analysis
$string['analyze'] = 'Avvia analisi';
$string['analysisconfirm'] = 'Attenzione: Prima di procedere, hai revisionato attentamente la trascrizione? Una volta avviata l\'analisi, non sarà possibile apportare modifiche. Assicurati dell\'accuratezza prima di continuare.';
$string['transcription'] = 'Trascrizione';
$string['missingtranscription'] = 'Trascrizione mancante';
$string['analysisstart'] = 'Analisi avviata';
$string['cannotwritetranscription'] = 'Impossibile salvare la trascrizione su disco, contatta un amministratore se questo errore persiste.';

// Summary
$string['savesummary'] = 'Salva riepilogo';
$string['summary'] = 'Riepilogo';
$string['abstract'] = 'Abstract';
$string['keypoints'] = 'Punti chiave';
$string['cannotwritesummary'] = 'Impossibile salvare il riepilogo su disco, contatta un amministratore se questo errore persiste.';
$string['summarynotsaved'] = 'Impossibile aggiornare il riepilogo. Contatta un amministratore se questo errore persiste.';
$string['summarysaved'] = 'Il riepilogo è stato salvato.';

// Questions
$string['questions'] = 'Domande';
$string['savequestions'] = 'Salva domande';
$string['questionssaved'] = 'Domande salvate.';
$string['questionsnotsaved'] = 'Impossibile aggiornare le domande. Contatta un amministratore se questo errore persiste.';
$string['cannotwritequestions'] = 'Impossibile salvare le domande su disco, contatta un amministratore se questo errore persiste.';
$string['questionn'] = 'Domanda n° {$a}';
$string['question'] = 'Domanda';
$string['questiontype'] = 'Tipo di domanda';
$string['questiontypeopen'] = 'Risposta aperta';
$string['questiontypeftb'] = 'Riempi gli spazi';
$string['questiontypetf'] = 'Vero o falso';
$string['questiontypemcq'] = 'Domanda a risposta multipla';
$string['questiontypehoq'] = 'Domanda di alto livello';
$string['questionenable'] = 'Abilita domanda';
$string['questionuseforgrading'] = 'Usa per valutazione';
$string['questionanswer'] = 'Risposta';
$string['questionanswertrue'] = 'Affermazione corretta';
$string['questionanswerfalse'] = 'Affermazione falsa';
$string['questiondistractor'] = 'Distrattore';
$string['questionusedistractor'] = 'Affermazione da visualizzare';

// Concepts
$string['concepts'] = 'Concetti';
$string['saveconcepts'] = 'Salva concetti';
$string['cannotwriteconcepts'] = 'Impossibile salvare i concetti su disco, contatta un amministratore se questo errore persiste.';
$string['conceptssaved'] = 'Concetti salvati.';
$string['conceptsnotsaved'] = 'Impossibile aggiornare i concetti. Contatta un amministratore se questo errore persiste.';
$string['conceptenable'] = 'Abilitato';
$string['conceptlabel'] = 'Etichetta';
$string['conceptdefinition'] = 'Definizione';
$string['conceptuseforgaming'] = 'Usa per giochi';
$string['conceptuseforcw'] = 'Parole crociate';
$string['conceptuseforftw'] = 'Trova la parola';
$string['conceptusefordtw'] = 'Trascina la parola';
$string['conceptuseingames'] = 'Giochi disponibili';
$string['conceptuseforpractice'] = 'Usa per esercitarsi';

// Activities
$string['settings'] = 'Genera';
$string['activities'] = 'Anteprima';
$string['generate'] = 'Genera attività';
$string['activitiescrossword'] = 'Parole crociate';
$string['activitiescwwords'] = 'Definisce quante parole utilizzare nell\'attività Parole crociate';
$string['activitiesdragtheword'] = 'Trascina la parola';
$string['activitiesdtwwords'] = 'Definisce quante parole utilizzare nell\'attività Trascina la parola';
$string['activitiesfindtheword'] = 'Trova la parola';
$string['activitiesflashcardsflashcards'] = 'Definisce quante schede flash dovrebbero apparire nell\'attività di valutazione concettuale';
$string['activitiesftwwords'] = 'Definisce quante parole utilizzare nell\'attività Trova la parola';
$string['activitiesglossary'] = 'Glossario';
$string['activitiesgrade'] = 'Valutazione concettuale';
$string['activitiesgradequestions'] = 'Definisce quante domande dovrebbero apparire nell\'attività di valutazione concettuale';
$string['activitiesgradeq'] = 'Valutazione contestuale';
$string['activitiesgradeqquestions'] = 'Definisce quante domande dovrebbero apparire nell\'attività di valutazione contestuale';
$string['activitieshoquestions'] = 'Domande di alto livello';
$string['activitiesibook'] = 'Libro interattivo';
$string['activitiesivideo'] = 'Video interattivo';
$string['activitiesivideoquestions'] = 'Definisce quante domande dovrebbero essere proposte per insieme nell\'attività Video interattivo. Ci sono 2 insiemi: uno a metà del video, uno alla fine del video';
$string['activitiesivideosummary'] = 'Definisce se il riepilogo verrà aggiunto alla fine dell\'attività Video interattivo';
$string['activitiespractice'] = 'Schede flash concettuali';
$string['activitiespracticeflashcards'] = 'Definisce quante domande dovrebbero apparire nell\'attività di schede flash concettuali';
$string['activitiespracticeq'] = 'Schede flash contestuali';
$string['activitiespracticeqflashcards'] = 'Definisce quante domande dovrebbero apparire nell\'attività di schede flash contestuali';
$string['activitiesselect'] = 'Seleziona un\'attività';
$string['activitiesselected'] = 'Attività Nolej: %s dal modulo "%s".';
$string['activitiessummary'] = 'Riepilogo';
$string['activitiesuseinibook'] = 'Usa questa attività nel Libro interattivo';
$string['activitiesenable'] = 'Genera {$a}';
$string['minvalue'] = 'Valore minimo';
$string['maxvalue'] = 'Valore massimo';
$string['cannotwritesettings'] = 'Impossibile salvare le impostazioni su disco, contatta un amministratore se questo errore persiste.';
$string['settingsnotsaved'] = 'Impossibile aggiornare le impostazioni. Contatta un amministratore se questo errore persiste.';
$string['generationstarted'] = 'Generazione avviata';
$string['erractivitiesdecode'] = 'Decodifica dati attività non riuscita';
$string['erractivitydownload'] = 'Impossibile salvare l\'attività su disco';
$string['errh5psave'] = 'Impossibile salvare il pacchetto h5p';
$string['errh5pvalidation'] = 'Il pacchetto h5p non è valido';
