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
$string['nolej:usenolej'] = 'Criar atividades com Nolej';

// Privacy API.
$string['privacy:metadata:nolej_module'] = 'Informações sobre o autor do módulo Nolej.';
$string['privacy:metadata:nolej_module:user_id'] = 'A ID do usuário que criou o módulo.';
$string['privacy:metadata:nolej_module:tstamp'] = 'A data e hora em que o usuário criou o módulo.';
$string['privacy:metadata:nolej_activity'] = 'Informações sobre o usuário que chamou a API Nolej.';
$string['privacy:metadata:nolej_activity:user_id'] = 'A ID do usuário que chamou a API.';
$string['privacy:metadata:nolej_activity:tstamp'] = 'A data e hora em que o usuário chamou a API.';
$string['privacy:metadata:nolej_activity:action'] = 'A API que o usuário chamou.';
$string['privacy:metadata:endpoint'] = 'Para se integrar com o Nolej, os dados do usuário precisam ser trocados com esse serviço.';
$string['privacy:metadata:endpoint:user_id'] = 'A ID do usuário é enviada do Moodle para permitir que você acesse seus dados no sistema remoto.';
$string['privacy:metadata:core_files'] = 'O plugin Nolej armazena arquivos que foram carregados pelo usuário para criar módulos e serem analisados pela IA.';

// Settings.
$string['apikey'] = 'Chave da API';
$string['apikeyinfo'] = 'Sua chave da API do Nolej.';
$string['apikeyhowto'] = 'Para obter uma chave de API, você deve primeiro criar uma conta em live.nolej.io e depois entrar em contato com Nolej em moodle@nolej.io, solicitando uma chave de API para seu endereço de e-mail registrado.';
$string['apikeymissing'] = 'A chave da API do Nolej está ausente. Você precisa defini-la na configuração do plugin.';

// Manage.
$string['library'] = 'Biblioteca Nolej';
$string['modules'] = 'Seus módulos Nolej';
$string['status'] = 'Status';
$string['created'] = 'Criado';
$string['lastupdate'] = 'Última atualização';
$string['editmodule'] = 'Editar';
$string['createmodule'] = 'Criar um novo módulo Nolej';
$string['deletemodule'] = 'Excluir';
$string['deletemoduledescription'] = 'Tem certeza de que deseja excluir este módulo Nolej?';
$string['moduledeleted'] = 'O módulo Nolej foi excluído.';
$string['action'] = 'Ação';
$string['documentinfo'] = 'Informações do módulo';
$string['genericerror'] = 'Ocorreu um erro: <pre>{$a->error}</pre>';
$string['moduleview'] = 'Visualizar módulo';

// Status.
$string['statuscreation'] = 'Novo módulo';
$string['statuscreationpending'] = 'Transcrição em andamento';
$string['statusanalysis'] = 'Transcrição concluída';
$string['statusanalysispending'] = 'Análise em andamento';
$string['statusrevision'] = 'Análise concluída';
$string['statusrevisionpending'] = 'Revisão em andamento';
$string['statusactivities'] = 'Revisão concluída';
$string['statusactivitiespending'] = 'Geração de atividades em andamento';
$string['statuscompleted'] = 'Atividades geradas';
$string['statusfailed'] = 'Falha';

// Notifications.
$string['eventwebhookcalled'] = 'O webhook do Nolej foi chamado.';
$string['messageprovider:transcription_ok'] = 'Transcrição concluída';
$string['messageprovider:transcription_ko'] = 'Transcrição falhou';
$string['messageprovider:analysis_ok'] = 'Análise concluída';
$string['messageprovider:analysis_ko'] = 'Análise falhou';
$string['messageprovider:activities_ok'] = 'Atividades geradas';
$string['messageprovider:activities_ko'] = 'Geração de atividade falhou';
$string['action_transcription_ok'] = 'Transcrição está pronta';
$string['action_transcription_ok_body'] = 'A transcrição do documento "{$a->title}" foi concluída em {$a->tstamp}, você pode agora verificá-la e iniciar a análise.';
$string['action_transcription_ko'] = 'Transcrição falhou';
$string['action_transcription_ko_body'] = 'Infelizmente, a transcrição do documento "{$a->title}" falhou em {$a->tstamp}. Mensagem de erro: {$a->errormessage}';
$string['action_analysis_ok'] = 'Análise está pronta';
$string['action_analysis_ok_body'] = 'A análise do documento "{$a->title}" foi concluída em {$a->tstamp}, você pode agora revisá-la.';
$string['action_analysis_ko'] = 'Análise falhou';
$string['action_analysis_ko_body'] = 'Infelizmente, a análise do documento "{$a->title}" falhou em {$a->tstamp}. Mensagem de erro: {$a->errormessage}';
$string['action_activities_ok'] = 'Atividades geradas com sucesso';
$string['action_activities_ok_body'] = 'As atividades do documento "{$a->title}" foram geradas em {$a->tstamp}.';
$string['action_activities_ko'] = 'Geração de atividade falhou';
$string['action_activities_ko_body'] = 'Infelizmente, a geração de atividade do documento "{$a->title}" falhou em {$a->tstamp}. Mensagem de erro: {$a->errormessage}';

// Creation.
$string['title'] = 'Título';
$string['titledesc'] = 'Escolha um título ou deixe em branco e o Nolej escolherá um título para você.';
$string['source'] = 'Fonte';
$string['sourcetype'] = 'Tipo de fonte';
$string['sourcetypefile'] = 'Arquivo';
$string['sourcetypeweb'] = 'Recurso da web';
$string['sourcetypetext'] = 'Escrever texto diretamente';
$string['sourcefile'] = 'Arquivo';
$string['sourceurl'] = 'URL da web';
$string['sourceurldesc'] = 'Escreva uma URL';
$string['sourceurltype'] = 'Tipo de conteúdo';
$string['sourcefreetext'] = 'Texto';
$string['sourcedocument'] = 'Documento';
$string['sourceaudio'] = 'Áudio';
$string['sourcevideo'] = 'Vídeo';
$string['sourceweb'] = 'Conteúdo da Web';
$string['language'] = 'Idioma do conteúdo';
$string['languagedesc'] = 'Escolher o idioma correto do conteúdo ajuda o Nolej a analisá-lo melhor.';
$string['create'] = 'Criar módulo';
$string['modulenotcreated'] = 'Módulo não criado';
$string['modulecreated'] = 'Módulo criado, transcrição em andamento. Você receberá uma notificação quando a transcrição for concluída.';
$string['modulenotfound'] = 'Módulo não encontrado';
$string['errdatamissing'] = 'Faltam alguns dados';
$string['errdocument'] = 'Ocorreu um erro durante a criação do módulo Nolej:<br><pre>{$a}</pre><br>Por favor, tente novamente ou entre em contato com um administrador se esse erro persistir.';

// Content limits.
$string['limitcontent'] = 'Limitações de conteúdo';
$string['limitaudio'] = 'Limites de áudio';
$string['limitvideo'] = 'Limites de vídeo';
$string['limitdoc'] = 'Limites do documento';
$string['limitmaxduration'] = 'Duração máxima: {$a} minutos.';
$string['limitmaxpages'] = 'Número máximo de páginas: {$a}.';
$string['limitmaxsize'] = 'Tamanho máximo do arquivo: {$a} GB.';
$string['limitmincharacters'] = 'Caracteres mínimos: {$a}.';
$string['limitmaxcharacters'] = 'Caracteres máximos: {$a}.';
$string['limittype'] = 'Tipos permitidos: {$a}.';

// Analysis.
$string['analyze'] = 'Iniciar análise';
$string['analysisconfirm'] = 'Aviso: Antes de prosseguir, você revisou minuciosamente a transcrição? Uma vez que a análise comece, modificações não podem ser feitas. Por favor, garanta a precisão antes de continuar.';
$string['transcription'] = 'Transcrição';
$string['missingtranscription'] = 'Transcrição ausente';
$string['analysisstart'] = 'Análise iniciada. Você receberá uma notificação quando a análise for concluída.';
$string['cannotwritetranscription'] = 'Não é possível salvar a transcrição no disco, entre em contato com um Administrador se esse erro persistir.';

// Summary.
$string['savesummary'] = 'Salvar resumo';
$string['summary'] = 'Resumo';
$string['abstract'] = 'Resumo';
$string['keypoints'] = 'Pontos-chave';
$string['cannotwritesummary'] = 'Não é possível salvar o resumo no disco, entre em contato com um Administrador se esse erro persistir.';
$string['summarynotsaved'] = 'Não foi possível atualizar o resumo. Entre em contato com um Administrador se esse erro persistir.';
$string['summarysaved'] = 'O resumo foi salvo.';

// Questions.
$string['questions'] = 'Perguntas';
$string['savequestions'] = 'Salvar perguntas';
$string['questionssaved'] = 'Perguntas salvas.';
$string['questionsnotsaved'] = 'Não foi possível atualizar as perguntas. Entre em contato com um Administrador se esse erro persistir.';
$string['cannotwritequestions'] = 'Não é possível salvar as perguntas no disco, entre em contato com um Administrador se esse erro persistir.';
$string['questionn'] = 'Pergunta nº{$a}';
$string['question'] = 'Pergunta';
$string['questiontype'] = 'Tipo de pergunta';
$string['questiontypeopen'] = 'Resposta aberta';
$string['questiontypeftb'] = 'Preencher lacunas';
$string['questiontypeftbmissingblank'] = 'A atividade "Preencher lacunas" requer um espaço reservado para a palavra que falta. Utilize 4 (quatro) caracteres sublinhados “____” para indicar a palavra que falta.';
$string['questiontypetf'] = 'Verdadeiro ou falso';
$string['questiontypemcq'] = 'Pergunta de múltipla escolha';
$string['questiontypehoq'] = 'Pergunta de alto nível';
$string['questionenable'] = 'Habilitar pergunta';
$string['questionuseforgrading'] = 'Usar para classificação';
$string['questionanswer'] = 'Resposta';
$string['questionanswertrue'] = 'Afirmação correta';
$string['questionanswerfalse'] = 'Afirmação falsa';
$string['questiondistractor'] = 'Distractor';
$string['questionusedistractor'] = 'Declaração a ser exibida';

// Concepts.
$string['concepts'] = 'Conceitos';
$string['saveconcepts'] = 'Salvar conceitos';
$string['cannotwriteconcepts'] = 'Não é possível salvar os conceitos no disco, entre em contato com um Administrador se esse erro persistir.';
$string['conceptssaved'] = 'Conceitos salvos.';
$string['conceptsnotsaved'] = 'Não foi possível atualizar os conceitos. Entre em contato com um Administrador se esse erro persistir.';
$string['conceptenable'] = 'Habilitado';
$string['conceptlabel'] = 'Rótulo';
$string['conceptdefinition'] = 'Definição';
$string['conceptuseforgaming'] = 'Usar para jogos';
$string['conceptuseforcw'] = 'Palavras cruzadas';
$string['conceptuseforftw'] = 'Encontre a palavra';
$string['conceptusefordtw'] = 'Arraste a palavra';
$string['conceptuseingames'] = 'Jogos disponíveis';
$string['conceptuseforpractice'] = 'Usar para praticar';

// Activities.
$string['settings'] = 'Gerar';
$string['activities'] = 'Visualização prévia';
$string['generate'] = 'Gerar atividades';
$string['activitiescrossword'] = 'Palavras cruzadas';
$string['activitiescwwords'] = 'Define quantas palavras usar na atividade de palavras cruzadas';
$string['activitiesdragtheword'] = 'Arraste a palavra';
$string['activitiesdtwwords'] = 'Define quantas palavras usar na atividade Arraste a Palavra';
$string['activitiesfindtheword'] = 'Encontre a palavra';
$string['activitiesflashcardsflashcards'] = 'Define quantos flashcards devem aparecer na atividade de avaliação conceitual';
$string['activitiesftwwords'] = 'Define quantas palavras usar na atividade Encontre a Palavra';
$string['activitiesglossary'] = 'Glossário';
$string['activitiesgrade'] = 'Avaliação conceitual';
$string['activitiesgradequestions'] = 'Define quantas perguntas devem aparecer na atividade de avaliação conceitual';
$string['activitiesgradeq'] = 'Avaliação contextual';
$string['activitiesgradeqquestions'] = 'Define quantas perguntas devem aparecer na atividade de avaliação contextual';
$string['activitieshoquestions'] = 'Perguntas de alto nível';
$string['activitiesibook'] = 'Livro interativo';
$string['activitiesivideo'] = 'Vídeo interativo';
$string['activitiesivideoquestions'] = 'Define quantas perguntas devem ser propostas por conjunto na atividade de Vídeo Interativo. Há 2 conjuntos: um no meio do vídeo, outro no final do vídeo';
$string['activitiesivideosummary'] = 'Define se o resumo será adicionado ao final da atividade de Vídeo Interativo';
$string['activitiespractice'] = 'Cartões de memória conceituais';
$string['activitiespracticeflashcards'] = 'Define quantas perguntas devem aparecer na atividade de cartões de memória conceituais';
$string['activitiespracticeq'] = 'Cartões de memória contextual';
$string['activitiespracticeqflashcards'] = 'Define quantas perguntas devem aparecer na atividade de cartões de memória contextual';
$string['activitiesselect'] = 'Selecionar uma atividade';
$string['activitiesselected'] = 'Atividade Nolej: %s do módulo "%s".';
$string['activitiessummary'] = 'Resumo';
$string['activitiesuseinibook'] = 'Usar esta atividade no livro interativo';
$string['activitiesenable'] = 'Gerar {$a}';
$string['minvalue'] = 'Valor mínimo';
$string['maxvalue'] = 'Valor máximo';
$string['cannotwritesettings'] = 'Não é possível salvar as configurações no disco, entre em contato com um Administrador se esse erro persistir.';
$string['settingsnotsaved'] = 'Não foi possível atualizar as configurações. Entre em contato com um Administrador se esse erro persistir.';
$string['generationstarted'] = 'Geração iniciada. Você receberá uma notificação quando as atividades foram geradas.';
$string['erractivitiesdecode'] = 'Falha ao decodificar os dados das atividades';
$string['erractivitydownload'] = 'Falha ao salvar a atividade no disco';
$string['errh5psave'] = 'Falha ao salvar o pacote h5p';
$string['errh5pvalidation'] = 'O pacote h5p não é válido';
