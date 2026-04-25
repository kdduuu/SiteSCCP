<?php
session_start(); // Inicia a sessão para permitir o acesso aos dados de usuário armazenados durante o login.

if (empty($_SESSION['user_id'])) {
    header('Location: ../formularios/sessao.html?status=error&message=' . rawurlencode('Acesso restrito. Faça login para continuar.'));
    exit;
}

$userName = $_SESSION['user_name'] ?? 'Fiel Torcedor';
$userEmail = $_SESSION['user_email'] ?? '';
$userCpf = $_SESSION['user_cpf'] ?? '';
$userDataCadastro = $_SESSION['user_data_cadastro'] ?? ''; // Data de cadastro enviada pelo login.php.

$displayCpf = '';
if ($userCpf !== '') {
    if (preg_match('/^(\d{3}\.\d{3}\.\d{3}-\d{2})$/', $userCpf)) {
        $displayCpf = $userCpf;
    } else {
        // Se o CPF não estiver no formato completo, exibe apenas os dígitos iniciais e finais.
        $displayCpf = substr($userCpf, 0, 3) . '.***.***-' . substr($userCpf, -2);
    }
}

$membershipDate = '';
if ($userDataCadastro !== '') {
    // Converte a data de cadastro enviada pela sessão para o formato brasileiro de exibição.
    $date = DateTime::createFromFormat('Y-m-d', $userDataCadastro);
    if ($date) {
        $membershipDate = $date->format('d/m/Y');
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poderoso Timão | Área do Torcedor</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/estilo.css">
    <link rel="icon" href="../img/hino.png" type="image/png">
</head>
<body class="bg-light">
    <header>
        <nav class="navbar navbar-expand-lg bg-dark navbar-dark fixed-top">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.html">
                    <img src="../img/logo_header.png" alt="Logo do site" width="60" height="60" class="d-inline-block align-text-top">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="menu">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="index.html">Timão!</a></li>
                        <li class="nav-item"><a class="nav-link" href="historia.html">História</a></li>
                        <li class="nav-item"><a class="nav-link" href="conquistas.html">Conquistas</a></li>
                        <li class="nav-item"><a class="nav-link" href="elenco.html">Elenco</a></li>
                        <li class="nav-item"><a class="nav-link" href="../formularios/sessao.html">Cadastro</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

<main>
        <section class="hero-section text-white d-flex align-items-center">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-6 offset-lg-1 p-4">
                        <h1 class="display-4 fw-bold">Bem-vindo à Área do Torcedor</h1>
                        <p class="lead mt-3">Esta é a sua conexão direta com o Corinthians dentro do site, feita para quem já faz parte da Fiel Torcida.</p>
                        <p class="lead">Nosso espaço reúne seus dados com segurança e mantém o mesmo estilo, a mesma paleta de cores e o mesmo clima de unidade do restante do site.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="container conteudo my-5">
            <div class="row align-items-center gap-4">
                <div class="col-lg-7">
                    <h2 class="fw-bold">Uma área que conversa com o site</h2>
                    <p class="text-muted">A Área do Torcedor foi pensada para fazer parte do mesmo universo visual do site. Aqui você encontra o seu perfil com a mesma clareza, simplicidade e organização presentes nas páginas de história, conquistas e elenco.</p>
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item bg-transparent border-0 px-0 py-2"><strong>Perfil seguro:</strong> nome, CPF e e-mail confirmados com login seguro.</li>
                        <li class="list-group-item bg-transparent border-0 px-0 py-2"><strong>Cadastro registrado:</strong> sua data de entrada como torcedor é exibida com clareza.</li>
                        <li class="list-group-item bg-transparent border-0 px-0 py-2"><strong>Integração com o site:</strong> navegação simples para voltar ao conteúdo principal do Corinthians.</li>
                    </ul>
                    <p class="text-muted">Tudo aqui foi organizado para que você continue sentindo que está dentro do mesmo projeto e não em uma página separada.</p>
                </div>
                <div class="col-lg-4">
                    <div class="card border-0 shadow-lg rounded-4">
                        <div class="card-body bg-white p-4">
                            <h2 class="card-title fw-bold">Seu Cartão de Perfil</h2>
                            <p class="text-muted mb-3">Os dados usados para confirmar seu acesso estão aqui, do jeito que o site também espera.</p>
                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item bg-transparent border-0 px-0 py-2"><strong>Nome:</strong> <?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?></li>
                                <?php if ($displayCpf !== ''): ?>
                                <li class="list-group-item bg-transparent border-0 px-0 py-2"><strong>CPF:</strong> <?php echo htmlspecialchars($displayCpf, ENT_QUOTES, 'UTF-8'); ?></li>
                                <?php endif; ?>
                                <?php if ($userEmail !== ''): ?>
                                <li class="list-group-item bg-transparent border-0 px-0 py-2"><strong>E-mail:</strong> <?php echo htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8'); ?></li>
                                <?php endif; ?>
                                <?php if ($membershipDate !== ''): ?>
                                <li class="list-group-item bg-transparent border-0 px-0 py-2"><strong>Cadastro:</strong> <?php echo htmlspecialchars($membershipDate, ENT_QUOTES, 'UTF-8'); ?></li>
                                <?php endif; ?>
                            </ul>
                            <a href="../formularios/logout.php" class="btn btn-danger w-100">Sair da Área do Torcedor</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="container conteudo my-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-lg rounded-4 p-4">
                        <div class="card-body">
                            <h2 class="fw-bold">Ouça a vibração da Fiel</h2>
                            <p class="text-muted mb-3">Aqui está um player de áudio para você sentir a atmosfera do Corinthians enquanto estiver na Área do Torcedor.</p>
                            <audio controls class="w-100">
                                <source src="../mp3/ssstik.io_1776911903080.mp3" type="audio/mpeg">
                                Seu navegador não suporta o player de áudio.
                            </audio>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="container mb-5">
            <h3 class="text-center fw-bold mb-4">Navegue pelo resto do site</h3>
            <div class="row row-cols-1 row-cols-md-3 g-3">
                <div class="col">
                    <a href="index.html" class="text-decoration-none text-dark">
                        <div class="p-4 bg-white rounded-4 shadow-sm h-100">
                            <h5 class="fw-semibold">Timão!</h5>
                            <p class="text-muted mb-0">Volte à página inicial e continue sentindo a força da Fiel.</p>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="historia.html" class="text-decoration-none text-dark">
                        <div class="p-4 bg-white rounded-4 shadow-sm h-100">
                            <h5 class="fw-semibold">História</h5>
                            <p class="text-muted mb-0">Reviva a trajetória do clube e da torcida mais apaixonada do Brasil.</p>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="conquistas.html" class="text-decoration-none text-dark">
                        <div class="p-4 bg-white rounded-4 shadow-sm h-100">
                            <h5 class="fw-semibold">Conquistas</h5>
                            <p class="text-muted mb-0">Veja os títulos que fizeram o Corinthians grande e invicto na memória da Fiel.</p>
                        </div>
                    </a>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-dark text-white pt-5 pb-5 mt-5 footer-full-width">
        <div class="container-fluid px-0">
            <div class="row align-items-center g-0">
                <div class="col-md-3 text-start ps-4">
                    <img src="../img/antigo2.png" alt="Símbolo Corinthians" style="max-height: 40px; width: 40px; height: auto; filter: invert(100%);">
                </div>
                <div class="col-md-9 text-end pe-4">
                    <p class="mb-0 small footer-text">Site desenvolvido por Kadu Almeida e Fernando Almeida &copy; 2025</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
