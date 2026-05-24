<?php
// Evita acesso direto ao arquivo
defined('MOODLE_INTERNAL') || die();

// Carrega a biblioteca do Google que você colocou na pasta vendor
require_once(__DIR__ . '/vendor/autoload.php');

function get_google_client() {
    $client = new \Google\Client();
    
    // COLE SUAS CREDENCIAIS AQUI
    $client->setClientId('COLOQUE_SEU_CLIENT_ID_AQUI');
    $client->setClientSecret('COLOQUE_SEU_SECRET_CLIENT_ID_AQUI');
    
    // A URL exata que você configurou no painel do Google
    $client->setRedirectUri('https://moodle.vaba-dev.com.br/local/plugin_forms/callback.php');
    
    // Pedimos permissão apenas para LER arquivos do Drive (mais seguro)
    $client->addScope(\Google\Service\Drive::DRIVE_READONLY);
    
    // Isso garante que o Google nos dê um "Refresh Token" para o Moodle atualizar os dados em segundo plano depois
    $client->setAccessType('offline');
    $client->setPrompt('consent');
    
    return $client;
}