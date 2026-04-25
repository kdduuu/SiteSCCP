<?php
/**
 * salvar_cadastro.php
 *
 * Este arquivo processa o envio do formulário de cadastro.
 * Ele recebe dados via POST, valida os valores, grava o usuário no banco
 * e redireciona de volta para a página de cadastro com sucesso ou erro.
 */

// Configuração de conexão com o banco de dados MySQL.
$host = 'localhost';          // servidor do banco de dados
$db   = 'site_sccp';          // nome do banco de dados
$user = 'root';               // usuário do banco de dados
$pass = '';                   // senha do banco de dados
$charset = 'utf8mb4';         // charset recomendado para suportar acentos e emojis

// DSN (Data Source Name) usado pelo PDO para conectar ao MySQL.
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,         // lançar exceção em caso de erro
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,    // retornar resultados como arrays associativos
    PDO::ATTR_EMULATE_PREPARES => false,                 // usar prepared statements reais
];

try {
    // Cria a conexão PDO com o banco de dados.
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Se a conexão falhar, interrompe o script e exibe uma mensagem segura.
    die('Erro de conexão: ' . htmlspecialchars($e->getMessage()));
}

// Garante que este script seja acessado apenas via POST.
// Se não for POST, redireciona para o formulário de cadastro.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cadastro.html');
    exit;
}

// Recebe os valores enviados pelo formulário.
// O operador null coalescing (?? '') evita aviso quando o campo não existe.
$nome      = trim($_POST['nome'] ?? '');
$cpfInput  = trim($_POST['cpf'] ?? '');
$email     = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$senha     = $_POST['senha'] ?? '';
$confirma  = $_POST['confirme'] ?? '';

// Remove tudo que não for dígito do CPF.
$cpfDigits = preg_replace('/\D+/', '', $cpfInput);
// Formata o CPF como 000.000.000-00 quando tiver exatamente 11 dígitos.
$cpf = (strlen($cpfDigits) === 11)
    ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpfDigits)
    : '';

// Validação básica dos campos obrigatórios.
if (!$nome || !$cpf || !$email || !$senha || !$confirma) {
    $erro = 'Preencha todos os campos corretamente.';
} elseif (strlen($cpfDigits) !== 11) {
    // Verifica se o CPF tem 11 dígitos numéricos.
    $erro = 'CPF inválido. Use 11 dígitos.';
} elseif ($senha !== $confirma) {
    // Verifica se a senha e a confirmação são iguais.
    $erro = 'As senhas não coincidem.';
} elseif (strlen($senha) < 6) {
    // Exige senha com ao menos 6 caracteres.
    $erro = 'A senha deve ter pelo menos 6 caracteres.';
} else {
    // Gera o hash da senha para não salvar a senha em texto simples.
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    // SQL com parâmetros nomeados para proteger contra SQL Injection.
    $sql = 'INSERT INTO usuarios (nome, cpf, email, senha_hash, criado_em) VALUES (:nome, :cpf, :email, :senha_hash, NOW())';
    $stmt = $pdo->prepare($sql);

    try {
        // Executa a inserção usando os valores recebidos.
        $stmt->execute([
            ':nome'       => $nome,
            ':cpf'        => $cpf,
            ':email'      => $email,
            ':senha_hash' => $senhaHash,
        ]);

        // Cadastro concluído com sucesso; redireciona para a página de cadastro.
        header('Location: cadastro.html?status=success');
        exit;
    } catch (PDOException $e) {
        // Se ocorrer erro no banco, trata duplicidade de valor e outros erros.
        if (isset($e->errorInfo[1]) && $e->errorInfo[1] === 1062) {
            $erro = 'CPF ou e-mail já cadastrado.';
        } else {
            $erro = 'Erro ao salvar: ' . htmlspecialchars($e->getMessage());
        }
    }
}

// Se chegou aqui, há uma mensagem de erro. Redireciona de volta para o formulário
// com o erro codificado na URL para o front-end exibir.
if (!empty($erro)) {
    header('Location: cadastro.html?status=error&message=' . rawurlencode($erro));
    exit;
}
