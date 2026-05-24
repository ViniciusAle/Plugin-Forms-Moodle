<?php
define('CLI_SCRIPT', false);
require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/google_helper.php');

global $DB, $USER, $PAGE, $OUTPUT;

// Exige que o professor esteja logado no Moodle
require_login();

$PAGE->set_url(new moodle_url('/local/plugin_forms/callback.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Autenticação do Google Drive');

echo $OUTPUT->header();

$client = get_google_client();

// 1. Se o Google mandou um código de volta, vamos processá-lo!
if (isset($_GET['code'])) {
    try {
        // Troca o código temporário pelo Token de Acesso real
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        
        if (isset($token['error'])) {
            throw new Exception("Erro do Google: " . $token['error_description']);
        }

        // Prepara os dados para salvar na tabela que você criou
        $record = new stdClass();
        $record->userid = $USER->id;
        $record->token = json_encode($token); // Salva como texto JSON
        $record->timemodified = time();

        // Verifica se o professor já tem um token salvo
        $existing = $DB->get_record('local_plugin_tokens', ['userid' => $USER->id]);
        
        if ($existing) {
            $record->id = $existing->id;
            $DB->update_record('local_plugin_tokens', $record);
            echo $OUTPUT->notification('Acesso ao Google Drive atualizado com sucesso!', 'success');
        } else {
            $DB->insert_record('local_plugin_tokens', $record);
            echo $OUTPUT->notification('Google Drive conectado com sucesso!', 'success');
        }

    } catch (Exception $e) {
        echo $OUTPUT->notification('Falha ao autenticar: ' . $e->getMessage(), 'error');
    }
} 
// 2. Se não tem código na URL, mostramos o botão para o professor ir pro Google
else {
    $authUrl = $client->createAuthUrl();
    echo "<h3>Conectar ao Google Drive</h3>";
    echo "<p>Para importar suas planilhas de logs, precisamos de acesso de leitura ao seu Drive.</p>";
    echo "<a href='" . htmlspecialchars($authUrl) . "' class='btn btn-primary'>Autorizar Google Drive</a>";
}

echo $OUTPUT->footer();