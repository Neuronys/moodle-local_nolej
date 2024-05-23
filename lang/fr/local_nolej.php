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
 * @author      2024 Vincenzo Padula <vincenzo@oc-group.eu>
 * @copyright   2024 OC Open Consulting SB Srl
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Plugin
$string['pluginname'] = 'Nolej';
$string['nolej:usenolej'] = 'Créez vos activités avec Nolej';

// Settings
$string['apikey'] = 'Clé API';
$string['apikeyinfo'] = 'Votre clé API Nolej.';
$string['apikeyhowto'] = 'Pour obtenir une clé API, vous devez d\'abord créer un compte sur live.nolej.io, puis contacter Nolej à info@nolej.io, en demandant une clé API pour votre adresse e-mail enregistrée.';
$string['apikeymissing'] = 'La clé API Nolej est manquante. Rendez-vous dans les paramètres du plugin pour renseigner votre clé.';

// Manage
$string['library'] = 'Bibliothèque Nolej';
$string['modules'] = 'Vos modules Nolej';
$string['status'] = 'État';
$string['created'] = 'Créé';
$string['lastupdate'] = 'Dernière mise à jour';
$string['editmodule'] = 'Modifier';
$string['createmodule'] = 'Créer un nouveau module Nolej';
$string['deletemodule'] = 'Supprimer';
$string['deletemoduledescription'] = 'Êtes vous certain de vouloir supprimer ce module Nolej?';
$string['moduledeleted'] = 'Le module Nolej a été supprimé.';
$string['action'] = 'Action';
$string['documentinfo'] = 'Informations du Module';
$string['genericerror'] = 'Une erreur est survenue: <pre>{$a->error}</pre>';
$string['moduleview'] = 'Voir le module';

// Status
$string['status_0'] = 'Nouveau module';
$string['status_1'] = 'Transcription en cours';
$string['status_2'] = 'Transcription terminée';
$string['status_3'] = 'Analyse en cours';
$string['status_4'] = 'Analysis terminée';
$string['status_5'] = 'Révision en cours';
$string['status_6'] = 'Révision terminée';
$string['status_7'] = 'Génération de l\'activité en cours';
$string['status_8'] = 'Activité générée';
$string['status_9'] = 'Échec';

// Notifications
$string['eventwebhookcalled'] = 'Le webhook Nolej a été appelé.';
$string['messageprovider:transcription_ok'] = 'Transcription terminée';
$string['messageprovider:transcription_ko'] = 'Transcription échouée';
$string['messageprovider:analysis_ok'] = 'Analyse complétée';
$string['messageprovider:analysis_ko'] = 'Analysis échouée';
$string['messageprovider:activities_ok'] = 'Activités générée';
$string['messageprovider:activities_ko'] = 'Génération d\'activité échouée';
$string['action_transcription_ok'] = 'Transcription prête';
$string['action_transcription_ok_body'] = 'Transcription du document "{$a->title}" complétée le {$a->tstamp}, vous pouvez le consulter et commencer l\'analyse.';
$string['action_transcription_ko'] = 'Transcription échouée';
$string['action_transcription_ko_body'] = 'Malheureusement, la transcription du document "{$a->title}" a échouée le {$a->tstamp}. Message d\'erreur: {$a->errormessage}';
$string['action_analysis_ok'] = 'Analyse prête';
$string['action_analysis_ok_body'] = 'Analyse du document "{$a->title}" complétée le {$a->tstamp}, vous pouvez désormais y accéder.';
$string['action_analysis_ko'] = 'Analyse échouée';
$string['action_analysis_ko_body'] = 'Malheureusement, l\'analysis du document "{$a->title}" a échoué le {$a->tstamp}. Message d\'erreur: {$a->errormessage}';
$string['action_activities_ok'] = 'Activitées générées avec succès';
$string['action_activities_ok_body'] = 'Les activitées du document "{$a->title}" ont été générées le {$a->tstamp}.';
$string['action_activities_ko'] = 'La génération d\'activités a échoué';
$string['action_activities_ko_body'] = 'Malheureusement, la génération des activités du document "{$a->title}" a échoué le {$a->tstamp}. Message d\'erreur: {$a->errormessage}';

// Creation
$string['title'] = 'Titre';
$string['titledesc'] = 'Choisissez un titre ou laissez le champ vide pour que Nolej s\'en charge.';
$string['source'] = 'Source';
$string['sourcetype'] = 'Type de source';
$string['sourcetypefile'] = 'Fichier';
$string['sourcetypeweb'] = 'Resource web';
$string['sourcetypetext'] = 'Ecrire du texte directement';
$string['sourcefile'] = 'Fichier';
$string['sourceurl'] = 'Lien URL';
$string['sourceurldesc'] = 'Écrivez une URL';
$string['sourceurltype'] = 'Type de contenu';
$string['sourcefreetext'] = 'Texte';
$string['sourcedocument'] = 'Document';
$string['sourceaudio'] = 'Audio';
$string['sourcevideo'] = 'Video';
$string['sourceweb'] = 'Contenu Web';
$string['language'] = 'Langue du contenu';
$string['languagedesc'] = 'Choisissez la langue du média pour permettre à Nolej de mieux vous aider.';
$string['create'] = 'Créer un module';
$string['modulenotcreated'] = 'Module non créé';
$string['modulecreated'] = 'Module créé, transcription en cours...';
$string['modulenotfound'] = 'Aucun module trouvé';
$string['errdatamissing'] = 'Certaines données manquent';
$string['errdocument'] = 'Une erreur est arrivée au cours de la création du module Nolej:<br><pre>{$a}</pre><br>Veuillez réessayer ou contacter un administrateur si l\'erreur persiste.';

// Content limits
$string['limitcontent'] = 'Limitations de contenu';
$string['limitaudio'] = 'Limites Audio';
$string['limitvideo'] = 'Limites Video';
$string['limitdoc'] = 'Limites de Document';
$string['limitmaxduration'] = 'Durée maximale';
$string['limitmaxpages'] = 'Nombre maximal de pages';
$string['limitmaxsize'] = 'Taille maximale de fichier';
$string['limitmincharacters'] = 'Nombre minimal de caractères';
$string['limitmaxcharacters'] = 'Nombre maximal de caractères';
$string['limittype'] = 'Types permis';

// Analysis
$string['analyze'] = 'Commencer l\'analyse';
$string['analysisconfirm'] = 'Attention: Avant de continuer, avez-vous bien vérifié la transcription? Once the analysis begins, modifications cannot be made. Please ensure accuracy before continuing.';
$string['transcription'] = 'Transcription';
$string['missingtranscription'] = 'Transcription manquante';
$string['analysisstart'] = 'Analyse commencée';
$string['cannotwritetranscription'] = 'Impossible de sauvegarder la transcription sur le disque, veuillez contacter un Administrateur si l\'erreur persiste.';

// Summary
$string['savesummary'] = 'Enregistrer le résumé';
$string['summary'] = 'Résumé';
$string['abstract'] = 'Abstrait';
$string['keypoints'] = 'Points clé';
$string['cannotwritesummary'] = 'Impossible de sauvegarder le résumé sur le disque, veuillez contacter un Administrateur si l\'erreur persiste.';
$string['summarynotsaved'] = 'Échec de la mise à jour du résumé, veuillez contacter un Administrateur si l\'erreur persiste';
$string['summarysaved'] = 'Résumé enregistré.';

// Questions
$string['questions'] = 'Questions';
$string['savequestions'] = 'Enregistrer les questions';
$string['questionssaved'] = 'Questions enregistrées.';
$string['questionsnotsaved'] = 'Échec de la mise à jour des questions, veuillez contacter un Administrateur si l\'erreur persiste.';
$string['cannotwritequestions'] = 'Impossible de sauvegarder les questions sur le disque, veuillez contacter un Administrateur si l\'erreur persiste.';
$string['questionn'] = 'Question #{$a}';
$string['question'] = 'Question';
$string['questiontype'] = 'Type de question';
$string['questiontypeopen'] = 'Réponse ouverte';
$string['questiontypeftb'] = 'Texte à trou';
$string['questiontypetf'] = 'Vrai ou Faux';
$string['questiontypemcq'] = 'QCM';
$string['questiontypehoq'] = 'Question de haut niveau';
$string['questionenable'] = 'Activer la question';
$string['questionuseforgrading'] = 'Compte dans la notation';
$string['questionanswer'] = 'Réponse';
$string['questionanswertrue'] = 'Affirmation correcte';
$string['questionanswerfalse'] = 'Affirmation erronée';
$string['questiondistractor'] = 'Fausse piste';
$string['questionusedistractor'] = 'Affirmation à afficher';

// Concepts
$string['concepts'] = 'Concepts';
$string['saveconcepts'] = 'Enregistrer les concepts';
$string['cannotwriteconcepts'] = 'Impossible de sauvegarder les concepts sur le disque, veuillez contacter un Administrateur si l\'erreur persiste.';
$string['conceptssaved'] = 'Concepts enregistrés.';
$string['conceptsnotsaved'] = 'Échec de la mise à jour des concepts, veuillez contacter un Administrateur si l\'erreur persiste.';
$string['conceptenable'] = 'Activé';
$string['conceptlabel'] = 'Nom';
$string['conceptdefinition'] = 'Définition';
$string['conceptuseforgaming'] = 'Utiliser dans des jeux';
$string['conceptuseforcw'] = 'Mots croisés';
$string['conceptuseforftw'] = 'Mots-mêlés';
$string['conceptusefordtw'] = 'Glisser-déposer';
$string['conceptuseingames'] = 'Jeux disponibles';
$string['conceptuseforpractice'] = 'Utiliser comme entraînement';

// Activities
$string['settings'] = 'Générer';
$string['activities'] = 'Aperçu';
$string['generate'] = 'Générer les activités';
$string['activitiescrossword'] = 'Mots croisés';
$string['activitiescwwords'] = 'Détermine le nombre de mots à utiliser dans l\'activité Mots Croisés';
$string['activitiesdragtheword'] = 'Glisser-déposer';
$string['activitiesdtwwords'] = 'Détermine le nombre de mots à utiliser dans l\'activité Glisser-déposer';
$string['activitiesfindtheword'] = 'Mots-mêlés';
$string['activitiesflashcardsflashcards'] = 'Détermine le nombre de flashcards devant apparaître dans l\'activité évaluation conceptuelle';
$string['activitiesftwwords'] = 'Détermine le nombre de mots à utiliser dans l\'activité Mots-mêlés';
$string['activitiesglossary'] = 'Glossaire';
$string['activitiesgrade'] = 'Évaluation conceptuelle';
$string['activitiesgradequestions'] = 'Détermine le nombre de questions devant apparaître dans l\'activité évaluation conceptuelle';
$string['activitiesgradeq'] = 'Évaluation contextuelle';
$string['activitiesgradeqquestions'] = 'Détermine le nombre de questions devant apparaître dans l\'activité évaluation contextuelle';
$string['activitieshoquestions'] = 'Questions de haut niveau';
$string['activitiesibook'] = 'Livre Interactif';
$string['activitiesivideo'] = 'Vidéo Interactive';
$string['activitiesivideoquestions'] = 'Détermine le nombre de questions à proposer par ensemble dans l\'activité Vidéo Interactive. Il existe 2 ensembles: un au milieu et l\'autre à la fin de la vidéo';
$string['activitiesivideosummary'] = 'Détermine si le résumé sera ajouté à la fin de l\'activité Vidéo Interactive';
$string['activitiespractice'] = 'Flashcards conceptuelles';
$string['activitiespracticeflashcards'] = 'Détermine le nombre de questions devant apparaître dans l\'activité flashcard conceptuelle';
$string['activitiespracticeq'] = 'Flashcards contextuelles';
$string['activitiespracticeqflashcards'] = 'Détermine le nombre de questions devant apparaître dans l\'activité flashcard contextuelle';
$string['activitiesselect'] = 'Selectionnez une activité';
$string['activitiesselected'] = 'Activité Nolej: %s du module "%s".';
$string['activitiessummary'] = 'Résumé';
$string['activitiesuseinibook'] = 'Utiliser cette activité dans un Livre Interactif';
$string['activitiesenable'] = 'Générer {$a}';
$string['minvalue'] = 'Valeur minimale';
$string['maxvalue'] = 'Valeur maximale';
$string['cannotwritesettings'] = 'Impossible de sauvegarder les paramètres sur le disque, veuillez contacter un Administrateur si l\'erreur persiste.';
$string['settingsnotsaved'] = 'Échec de la mise à jour des paramètres, veuillez contacter un Administrateur si l\'erreur persiste.';
$string['generationstarted'] = 'Génération commencée';
$string['erractivitiesdecode'] = 'Échec du décodage des données des activités';
$string['erractivitydownload'] = 'Échec de la sauvegarde de l\'activité sur le disque';
$string['errh5psave'] = 'Échec de la sauvegarde du paquet H5P';
$string['errh5pvalidation'] = 'Paquet H5P invalide';
