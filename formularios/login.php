<?php
/**
 * login.php
 *
 * Este arquivo processa o envio do formulário de login.
 * Ele verifica se o usuário existe no banco pelo e-mail ou CPF,
 * valida a senha e cria uma sessão para o usuário autenticado.
 */

session_start(); // Inicia a sessão para guardar dados do usuário entre páginas.

// Configuração de conexão com o banco de dados MySQL.
$host = 'localhost';
$db   = 'site_sccp';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Redireciona se não conseguir conectar ao banco.
    header('Location: sessao.html?status=error&message=' . rawurlencode('Erro de conexão com o banco.'));
    exit;
}

// Garante que esta página seja acessada apenas via POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: sessao.html');
    exit;
}

// Recebe o valor de login enviado pelo formulário.
// Pode ser e-mail ou CPF.
$login = trim($_POST['login'] ?? '');
$senha = $_POST['senha'] ?? '';

// Verifica campos obrigatórios.
if ($login === '' || $senha === '') {
    header('Location: sessao.html?status=error&message=' . rawurlencode('Preencha e-mail/CPF e senha.'));
    exit;
}

// Tenta reconhecer o login como e-mail.
$email = filter_var($login, FILTER_VALIDATE_EMAIL);
// Remove tudo que não for dígito para tentar reconhecer CPF.
$cpfDigits = preg_replace('/\D+/', '', $login);

if ($email) {
    // Consulta pelo e-mail. A coluna criado_em é convertida para data no formato YYYY-MM-DD.
    $sql = 'SELECT id, nome, cpf, email, senha_hash, DATE_FORMAT(criado_em, "%Y-%m-%d") AS data_cadastro FROM usuarios WHERE email = :login LIMIT 1';
    $params = [':login' => $email];
} elseif (strlen($cpfDigits) === 11) {
    // Formata CPF se tiver 11 dígitos e consulta pelo CPF.
    $cpf = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpfDigits);
    $sql = 'SELECT id, nome, cpf, email, senha_hash, DATE_FORMAT(criado_em, "%Y-%m-%d") AS data_cadastro FROM usuarios WHERE cpf = :login LIMIT 1';
    $params = [':login' => $cpf];
} else {
    // Se não for um e-mail válido nem um CPF válido, retorna erro.
    header('Location: sessao.html?status=error&message=' . rawurlencode('Informe um e-mail válido ou CPF com 11 dígitos.'));
    exit;
}

// Prepara e executa a consulta de usuário.
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$userData = $stmt->fetch();

// Verifica se o usuário existe e se a senha bate com o hash gravado.
if (!$userData || !password_verify($senha, $userData['senha_hash'])) {
    header('Location: sessao.html?status=error&message=' . rawurlencode('E-mail/CPF ou senha inválidos.'));
    exit;
}

// Se chegou até aqui, o login foi bem-sucedido.
$_SESSION['user_id'] = $userData['id'];       // guarda o id do usuário na sessão
$_SESSION['user_name'] = $userData['nome'];    // guarda o nome do usuário na sessão
$_SESSION['user_email'] = $userData['email'];
$_SESSION['user_cpf'] = $userData['cpf'];
$_SESSION['user_data_cadastro'] = $userData['data_cadastro'];

// Redireciona para a página exclusiva do torcedor.
header('Location: ../pages/fiel_torcedor.php');
exit;
