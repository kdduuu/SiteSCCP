<?php
session_start(); // Inicia a sessão para poder limpar os dados de autenticação do usuário.

// Limpa todos os dados armazenados na sessão atual.
$_SESSION = [];

if (ini_get('session.use_cookies')) {
    // Remove o cookie de sessão do navegador se ele estiver sendo usado.
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

// Encerra a sessão no servidor.
session_destroy();

// Redireciona para a tela de login/cadastro após o logout.
header('Location: sessao.html?status=success&message=' . rawurlencode('Logout realizado com sucesso.'));
exit;
