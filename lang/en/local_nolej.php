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
$string['nolej:usenolej'] = 'Create activities with Nolej';

// Privacy API.
$string['privacy:metadata:nolej_module'] = 'Information about the author of the Nolej module.';
$string['privacy:metadata:nolej_module:user_id'] = 'The ID of the user that created the module.';
$string['privacy:metadata:nolej_module:tstamp'] = 'The timestamp when the user created the module.';
$string['privacy:metadata:nolej_activity'] = 'Information about the user that called Nolej API.';
$string['privacy:metadata:nolej_activity:user_id'] = 'The ID of the user that called the API.';
$string['privacy:metadata:nolej_activity:tstamp'] = 'The timestamp when the user called the API.';
$string['privacy:metadata:nolej_activity:action'] = 'The API that the user called.';
$string['privacy:metadata:endpoint'] = 'In order to integrate with Nolej, user data needs to be exchanged with that service.';
$string['privacy:metadata:endpoint:user_id'] = 'The user_id is sent from Moodle to allow you to access your data on the remote system.';
$string['privacy:metadata:core_files'] = 'Nolej plugin stores files which have been uploaded by the user to create modules and be analyzed by AI.';

// Settings.
$string['apikey'] = 'API Key';
$string['apikeyinfo'] = 'Your Nolej API Key.';
$string['apikeyhowto'] = 'To obtain an API key, you have to create an account on live.nolej.io first and then contact Nolej at moodle@nolej.io, requesting an API key for your registered email address.';
$string['apikeymissing'] = 'Nolej API key is missing. You need to set it in the plugin configuration.';

// Manage.
$string['library'] = 'Nolej library';
$string['modules'] = 'Your Nolej modules';
$string['status'] = 'Status';
$string['created'] = 'Created';
$string['lastupdate'] = 'Last update';
$string['editmodule'] = 'Edit';
$string['createmodule'] = 'Create a new Nolej module';
$string['deletemodule'] = 'Delete';
$string['deletemoduledescription'] = 'Are you sure you want to delete this Nolej module?';
$string['moduledeleted'] = 'The Nolej module has been deleted.';
$string['action'] = 'Action';
$string['documentinfo'] = 'Module info';
$string['genericerror'] = 'An error occurred: <pre>{$a->error}</pre>';
$string['moduleview'] = 'View module';

// Status.
$string['statuscreation'] = 'New module';
$string['statuscreationpending'] = 'Transcription in progress';
$string['statusanalysis'] = 'Transcription completed';
$string['statusanalysispending'] = 'Analysis in progress';
$string['statusrevision'] = 'Analysis completed';
$string['statusrevisionpending'] = 'Revision in progress';
$string['statusactivities'] = 'Revision completed';
$string['statusactivitiespending'] = 'Activities generation in progress';
$string['statuscompleted'] = 'Activities generated';
$string['statusfailed'] = 'Failed';

// Notifications.
$string['eventwebhookcalled'] = 'Nolej webhook has been called.';
$string['messageprovider:transcription_ok'] = 'Transcription completed';
$string['messageprovider:transcription_ko'] = 'Transcription failed';
$string['messageprovider:analysis_ok'] = 'Analysis completed';
$string['messageprovider:analysis_ko'] = 'Analysis failed';
$string['messageprovider:activities_ok'] = 'Activities generated';
$string['messageprovider:activities_ko'] = 'Activity generation failed';
$string['action_transcription_ok'] = 'Transcription is ready';
$string['action_transcription_ok_body'] = 'Transcription of document "{$a->title}" has been completed on {$a->tstamp}, you can now check it and start the analysis.';
$string['action_transcription_ko'] = 'Transcription failed';
$string['action_transcription_ko_body'] = 'Unfortunately, transcription of document "{$a->title}" has failed on {$a->tstamp}. Error message: {$a->errormessage}';
$string['action_analysis_ok'] = 'Analysis is ready';
$string['action_analysis_ok_body'] = 'Analysis of document "{$a->title}" has been completed on {$a->tstamp}, you can now review it.';
$string['action_analysis_ko'] = 'Analysis failed';
$string['action_analysis_ko_body'] = 'Unfortunately, analysis of document "{$a->title}" has failed on {$a->tstamp}. Error message: {$a->errormessage}';
$string['action_activities_ok'] = 'Activities successfully generated';
$string['action_activities_ok_body'] = 'Activities of document "{$a->title}" have been generated on {$a->tstamp}.';
$string['action_activities_ko'] = 'Activity generation failed';
$string['action_activities_ko_body'] = 'Unfortunately, activities generation of document "{$a->title}" has failed on {$a->tstamp}. Error message: {$a->errormessage}';

// Creation.
$string['title'] = 'Title';
$string['titledesc'] = 'Choose a title or leave it blank and Nolej will choose a title for you.';
$string['source'] = 'Source';
$string['sourcetype'] = 'Source type';
$string['sourcetypefile'] = 'File';
$string['sourcetypeweb'] = 'Web resource';
$string['sourcetypetext'] = 'Write text directly';
$string['sourcefile'] = 'File';
$string['sourceurl'] = 'Web URL';
$string['sourceurldesc'] = 'Write an URL';
$string['sourceurltype'] = 'Type of content';
$string['sourcefreetext'] = 'Text';
$string['sourcedocument'] = 'Document';
$string['sourceaudio'] = 'Audio';
$string['sourcevideo'] = 'Video';
$string['sourceweb'] = 'Web Content';
$string['language'] = 'Content language';
$string['languagedesc'] = 'Choosing the correct language of the media helps Nolej to better analyze it.';
$string['create'] = 'Create module';
$string['modulenotcreated'] = 'Module not created';
$string['modulecreated'] = 'Module created, transcription in progress. You will receive a notification when the transcription is completed.';
$string['modulenotfound'] = 'Module not found';
$string['errdatamissing'] = 'Some data missing';
$string['errdocument'] = 'An error accurred during the creation of Nolej module:<br><pre>{$a}</pre><br>Please try again or contact an administrator if this error persists.';

// Content limits.
$string['limitcontent'] = 'Content limitations';
$string['limitaudio'] = 'Audio limits';
$string['limitvideo'] = 'Video limits';
$string['limitdoc'] = 'Document limits';
$string['limitmaxduration'] = 'Max duration: {$a} minutes.';
$string['limitmaxpages'] = 'Maximum number of pages: {$a}.';
$string['limitmaxsize'] = 'File maximum size: {$a} GB.';
$string['limitmincharacters'] = 'Minimum characters: {$a}.';
$string['limitmaxcharacters'] = 'Maximum characters: {$a}.';
$string['limittype'] = 'Allowed types: {$a}.';

// Analysis.
$string['analyze'] = 'Start analysis';
$string['analysisconfirm'] = 'Warning: Before proceeding, have you thoroughly reviewed the transcription? Once the analysis begins, modifications cannot be made. Please ensure accuracy before continuing.';
$string['transcription'] = 'Transcription';
$string['missingtranscription'] = 'Missing transcription';
$string['analysisstart'] = 'Analysis started. You will receive a notification when the analysis is completed.';
$string['cannotwritetranscription'] = 'Cannot save transcription on disk, please contact an Administrator if this error persists.';

// Summary.
$string['savesummary'] = 'Save summary';
$string['summary'] = 'Summary';
$string['abstract'] = 'Abstract';
$string['keypoints'] = 'Keypoints';
$string['cannotwritesummary'] = 'Cannot save summary on disk, please contact an Administrator if this error persists.';
$string['summarynotsaved'] = 'Could not update the summary. Please contact an Administrator if this error persists.';
$string['summarysaved'] = 'Summary has been saved.';

// Questions.
$string['questions'] = 'Questions';
$string['savequestions'] = 'Save questions';
$string['questionssaved'] = 'Questions saved.';
$string['questionsnotsaved'] = 'Could not update the questions. Please contact an Administrator if this error persists.';
$string['cannotwritequestions'] = 'Cannot save questions on disk, please contact an Administrator if this error persists.';
$string['questionn'] = 'Question #{$a}';
$string['question'] = 'Question';
$string['questiontype'] = 'Question type';
$string['questiontypeopen'] = 'Open answer';
$string['questiontypeftb'] = 'Fill the blanks';
$string['questiontypeftbmissingblank'] = 'The "Fill the blanks" activity requires a placeholder for the missing word. Use 4 (four) underscore characters "____" to indicate the missing word.';
$string['questiontypetf'] = 'True or false';
$string['questiontypemcq'] = 'Multiple choice question';
$string['questiontypehoq'] = 'High order question';
$string['questionenable'] = 'Enable question';
$string['questionuseforgrading'] = 'Use for grading';
$string['questionanswer'] = 'Answer';
$string['questionanswertrue'] = 'Correct statement';
$string['questionanswerfalse'] = 'False statement';
$string['questiondistractor'] = 'Distractor';
$string['questionusedistractor'] = 'Statement to display';

// Concepts.
$string['concepts'] = 'Concepts';
$string['saveconcepts'] = 'Save concepts';
$string['cannotwriteconcepts'] = 'Cannot save concepts on disk, please contact an Administrator if this error persists.';
$string['conceptssaved'] = 'Concepts saved.';
$string['conceptsnotsaved'] = 'Could not update the concepts. Please contact an Administrator if this error persists.';
$string['conceptenable'] = 'Enabled';
$string['conceptlabel'] = 'Label';
$string['conceptdefinition'] = 'Definition';
$string['conceptuseforgaming'] = 'Use for games';
$string['conceptuseforcw'] = 'Crossword';
$string['conceptuseforftw'] = 'Find the word';
$string['conceptusefordtw'] = 'Drag the word';
$string['conceptuseingames'] = 'Available games';
$string['conceptuseforpractice'] = 'Use for practice';

// Activities.
$string['settings'] = 'Generate';
$string['activities'] = 'Preview';
$string['generate'] = 'Generate activities';
$string['activitiescrossword'] = 'Crossword';
$string['activitiescwwords'] = 'Defines how many words to use in the Crossword activity';
$string['activitiesdragtheword'] = 'Drag the word';
$string['activitiesdtwwords'] = 'Defines how many words to use in the Drag The Word activity';
$string['activitiesfindtheword'] = 'Find the word';
$string['activitiesflashcardsflashcards'] = 'Defines how many flashcards should appear in the conceptual assessment activity';
$string['activitiesftwwords'] = 'Defines how many words to use in the Find The Word activity';
$string['activitiesglossary'] = 'Glossary';
$string['activitiesgrade'] = 'Conceptual assessment';
$string['activitiesgradequestions'] = 'Defines how many questions should appear in the conceptual assessment activity';
$string['activitiesgradeq'] = 'Contextual assessment';
$string['activitiesgradeqquestions'] = 'Defines how many questions should appear in the contextual assessment activity';
$string['activitieshoquestions'] = 'High order questions';
$string['activitiesibook'] = 'Interactive book';
$string['activitiesivideo'] = 'Interactive video';
$string['activitiesivideoquestions'] = 'Defines how many questions should be proposed per set in the Interactive Video activity. There are 2 sets: one at the middle of the video, one at the end of the video';
$string['activitiesivideosummary'] = 'Defines if the summary will be added to the end of the Interactive Video activity';
$string['activitiespractice'] = 'Conceptual flashcards';
$string['activitiespracticeflashcards'] = 'Defines how many questions should appear in the conceptual flashcard activity';
$string['activitiespracticeq'] = 'Contextual flashcards';
$string['activitiespracticeqflashcards'] = 'Defines how many questions should appear in the contextual flashcard activity';
$string['activitiesselect'] = 'Select an activity';
$string['activitiesselected'] = 'Nolej activity: %s from module "%s".';
$string['activitiessummary'] = 'Summary';
$string['activitiesuseinibook'] = 'Use this activity in the Interactive book';
$string['activitiesenable'] = 'Generate {$a}';
$string['minvalue'] = 'Minimum value';
$string['maxvalue'] = 'Maximum value';
$string['cannotwritesettings'] = 'Cannot save settings on disk, please contact an Administrator if this error persists.';
$string['settingsnotsaved'] = 'Could not update the settings. Please contact an Administrator if this error persists.';
$string['generationstarted'] = 'Generation started. You will receive a notification when the activities are ready.';
$string['erractivitiesdecode'] = 'Failed to decode activities data';
$string['erractivitydownload'] = 'Failed to save activity on disk';
$string['errh5psave'] = 'Failed to save h5p package';
$string['errh5pvalidation'] = 'h5p package is not valid';
