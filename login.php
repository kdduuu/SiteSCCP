<?php
session_start();
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
    header('Location: sessao.html?status=error&message=' . rawurlencode('Erro de conexão com o banco.'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: sessao.html');
    exit;
}

$login = trim($_POST['login'] ?? '');
$senha = $_POST['senha'] ?? '';
if ($login === '' || $senha === '') {
    header('Location: sessao.html?status=error&message=' . rawurlencode('Preencha e-mail/CPF e senha.'));
    exit;
}

$email = filter_var($login, FILTER_VALIDATE_EMAIL);
$cpfDigits = preg_replace('/\D+/', '', $login);

if ($email) {
    $sql = 'SELECT id, nome, cpf, email, senha_hash FROM usuarios WHERE email = :login LIMIT 1';
    $params = [':login' => $email];
} elseif (strlen($cpfDigits) === 11) {
    $cpf = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpfDigits);
    $sql = 'SELECT id, nome, cpf, email, senha_hash FROM usuarios WHERE cpf = :login LIMIT 1';
    $params = [':login' => $cpf];
} else {
    header('Location: sessao.html?status=error&message=' . rawurlencode('Informe um e-mail válido ou CPF com 11 dígitos.'));
    exit;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$userData = $stmt->fetch();

if (!$userData || !password_verify($senha, $userData['senha_hash'])) {
    header('Location: sessao.html?status=error&message=' . rawurlencode('E-mail/CPF ou senha inválidos.'));
    exit;
}

$_SESSION['user_id'] = $userData['id'];
$_SESSION['user_name'] = $userData['nome'];
header('Location: sessao.html?status=success');
exit;
