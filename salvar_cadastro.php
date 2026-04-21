<?php
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
    die('Erro de conexão: ' . htmlspecialchars($e->getMessage()));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cadastro.html');
    exit;
}

$nome      = trim($_POST['nome'] ?? '');
$cpfInput  = trim($_POST['cpf'] ?? '');
$cpfDigits = preg_replace('/\D+/', '', $cpfInput);
$cpf       = (strlen($cpfDigits) === 11)
    ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpfDigits)
    : '';
$email     = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$senha     = $_POST['senha'] ?? '';
$confirma  = $_POST['confirme'] ?? '';

if (!$nome || !$cpf || !$email || !$senha || !$confirma) {
    $erro = 'Preencha todos os campos corretamente.';
} elseif (strlen($cpfDigits) !== 11) {
    $erro = 'CPF inválido. Use 11 dígitos.';
} elseif ($senha !== $confirma) {
    $erro = 'As senhas não coincidem.';
} elseif (strlen($senha) < 6) {
    $erro = 'A senha deve ter pelo menos 6 caracteres.';
} else {
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    $sql = 'INSERT INTO usuarios (nome, cpf, email, senha_hash, criado_em) VALUES (:nome, :cpf, :email, :senha_hash, NOW())';
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([
            ':nome'      => $nome,
            ':cpf'       => $cpf,
            ':email'     => $email,
            ':senha_hash'=> $senhaHash,
        ]);
        header('Location: cadastro.html?status=success');
        exit;
    } catch (PDOException $e) {
        if ($e->errorInfo[1] === 1062) {
            $erro = 'CPF ou e-mail já cadastrado.';
        } else {
            $erro = 'Erro ao salvar: ' . htmlspecialchars($e->getMessage());
        }
    }
}

if (!empty($erro)) {
    header('Location: cadastro.html?status=error&message=' . rawurlencode($erro));
    exit;
}
