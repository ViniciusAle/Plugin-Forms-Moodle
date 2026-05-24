<?php
define('CLI_SCRIPT', false);
require(__DIR__ . '/../../config.php');

global $DB, $CFG;

require_login();
is_siteadmin() || die('Acesso negado');

// Isso vai te mostrar exatamente o nome que o Moodle espera
$nome_esperado = $CFG->prefix . 'local_plugin_forms';

echo "O Moodle está procurando pela tabela: <b>{$nome_esperado}</b><br>";

$dados = new stdClass();
$dados->filename = 'teste_prefixo.csv';
$dados->timecreated = time();

try {
    $id = $DB->insert_record('local_plugin_forms', $dados);
    echo "<h1 style='color:green'>Sucesso! ID: $id</h1>";
} catch (Exception $e) {
    echo "<h1 style='color:red'>Erro no banco:</h1>" . $e->getMessage();
}