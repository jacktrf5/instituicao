<?php
// CONFIGURAÇÃO DO BANCO
$host = '127.0.0.1';
$dbname = 'instituição';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ ERRO NA CONEXÃO: " . $e->getMessage());
}

// VARIÁVEIS
$sucesso = '';
$erro = '';

// BUSCAR UNIDADES
try {
    $unidades = $pdo->query("SELECT id_unidade, nome_unidade FROM instituicao ORDER BY id_unidade")->fetchAll();
} catch (Exception $e) {
    $erro = "❌ Erro ao carregar unidades: " . $e->getMessage();
    $unidades = [];
}

// PROCESSAR FORMULÁRIO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cadastrar'])) {
    $id_unidade = (int)$_POST['id_unidade'];
    $descricao = trim(substr($_POST['descricao_legislacao'], 0, 30));
    $data = $_POST['data_legislacao'];
    $url = trim(substr($_POST['url_legislacao'], 0, 50));

    if (!$id_unidade || !$descricao || !$data || !$url) {
        $erro = "⚠️ Preencha todos os campos obrigatórios.";
    } else {
        try {
            $sql = "INSERT INTO legislacao (id_unidade, descricao_legislacao, data_legislacao, url_legislacao) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_unidade, $descricao, $data, $url]);
            $sucesso = "✅ Legislação cadastrada com sucesso!";
        } catch (Exception $e) {
            $erro = "❌ Erro ao cadastrar: " . $e->getMessage();
        }
    }
}

// BUSCAR LEGISLAÇÕES
try {
    $legislacoes = $pdo->query("
        SELECT l.*, i.nome_unidade 
        FROM legislacao l 
        LEFT JOIN instituicao i ON l.id_unidade = i.id_unidade 
        ORDER BY l.data_legislacao DESC
    ")->fetchAll();
} catch (Exception $e) {
    $legislacoes = [];
    $erro = "❌ Erro ao carregar legislações: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sistema Legislação</title>
    <style>
        body { font-family: Arial; margin: 20px; background: #f0f2f5; }
        .container { max-width: 1200px; margin: auto; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .alert { padding: 15px; margin: 15px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background: #343a40; color: white; }
        .form-group { margin: 15px 0; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        button { background: #007bff; color: white; padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📋 Sistema de Legislação</h1>
        
        <?php if ($sucesso): ?>
            <div class="alert success"><?= $sucesso ?></div>
        <?php endif; ?>
        
        <?php if ($erro): ?>
            <div class="alert error"><?= $erro ?></div>
        <?php endif; ?>

        <h2>📝 Cadastrar Legislação</h2>
        <form method="POST">
            <div class="form-group">
                <label>Unidade:</label>
                <select name="id_unidade" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($unidades as $u): ?>
                        <option value="<?= $u['id_unidade'] ?>">
                            <?= $u['id_unidade'] ?> - <?= $u['nome_unidade'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Descrição:</label>
                <input type="text" name="descricao_legislacao" maxlength="30" required>
            </div>

            <div class="form-group">
                <label>Data:</label>
                <input type="date" name="data_legislacao" required value="<?= date('Y-m-d') ?>">
            </div>

            <div class="form-group">
                <label>URL:</label>
                <input type="url" name="url_legislacao" maxlength="50" required>
            </div>

            <button type="submit" name="cadastrar">Cadastrar</button>
        </form>

        <h2>📜 Legislações Cadastradas</h2>
        <?php if ($legislacoes): ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Unidade</th>
                    <th>Descrição</th>
                    <th>Data</th>
                    <th>URL</th>
                </tr>
                <?php foreach ($legislacoes as $l): ?>
                <tr>
                    <td><?= $l['id_legislacao'] ?></td>
                    <td><?= $l['id_unidade'] ?> - <?= $l['nome_unidade'] ?></td>
                    <td><?= $l['descricao_legislacao'] ?></td>
                    <td><?= date('d/m/Y', strtotime($l['data_legislacao'])) ?></td>
                    <td><a href="<?= $l['url_legislacao'] ?>" target="_blank">🔗 Acessar</a></td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>Nenhuma legislação cadastrada.</p>
        <?php endif; ?>
    </div>
</body>
</html>