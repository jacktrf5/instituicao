<?php
require_once __DIR__ . '/funcoes.php';

/**
 * Garante que a função conectarBanco exista, mesmo se o arquivo funcoes.php não definir.
 */
if (!function_exists('conectarBanco')) {
    function conectarBanco() {
        $host = 'localhost';
        $dbname = 'sistema_unidades';
        $usuario = 'root';
        $senha = '';

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $usuario, $senha);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("Erro ao conectar ao banco: " . $e->getMessage());
        }
    }
}

$pdo = conectarBanco();

/**
 * Consulta segura: garante nome da unidade e sigla mesmo se ausentes.
 */
try {
    $sql = "
        SELECT 
            l.id_legislacao,
            l.id_unidade,
            COALESCE(i.nome_unidade, CONCAT('Unidade ', l.id_unidade, ' (sem cadastro)')) AS nome_unidade,
            COALESCE(i.sigla, 'N/A') AS sigla,
            COALESCE(l.descricao_legislacao, 'Sem descrição informada') AS descricao_legislacao,
            COALESCE(l.data_legislacao, 'Data não informada') AS data_legislacao,
            l.url_legislacao
        FROM legislacao l
        LEFT JOIN instituicao i ON l.id_unidade = i.id_unidade
        ORDER BY l.data_legislacao DESC
    ";

    $legislacoes = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao buscar legislações: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Legislações Cadastradas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            margin-top: 40px;
            font-size: 2.3em;
            background: linear-gradient(45deg, #007bff, #6610f2);
            -webkit-background-clip: text;
            background-clip: text; /* ✅ compatibilidade padrão */
            -webkit-text-fill-color: transparent;
        }

        .container {
            width: 90%;
            max-width: 1100px;
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0px 2px 12px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #007bff;
            color: #fff;
            text-align: left;
            padding: 12px;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .alerta {
            text-align: center;
            color: #a94442;
            background: #f2dede;
            border: 1px solid #ebccd1;
            border-radius: 8px;
            padding: 15px;
            margin: 30px 0;
        }

        .incompleto {
            color: #d9534f;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <h1>Legislações Cadastradas</h1>

    <div class="container">
        <?php if (empty($legislacoes)): ?>
            <div class="alerta">⚠️ Nenhuma legislação cadastrada.</div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Unidade</th>
                        <th>Sigla</th>
                        <th>Descrição</th>
                        <th>Data</th>
                        <th>URL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($legislacoes as $leg): ?>
                        <?php
                        $descricao = $leg['descricao_legislacao'];
                        $unidadeIncompleta = strpos($leg['nome_unidade'], 'sem cadastro') !== false;
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($leg['id_legislacao']) ?></td>
                            <td class="<?= $unidadeIncompleta ? 'incompleto' : '' ?>">
                                <?= htmlspecialchars($leg['nome_unidade']) ?>
                            </td>
                            <td><?= htmlspecialchars($leg['sigla']) ?></td>
                            <td><?= htmlspecialchars($descricao) ?></td>
                            <td><?= htmlspecialchars($leg['data_legislacao']) ?></td>
                            <td>
                                <?php if (!empty($leg['url_legislacao'])): ?>
                                    <a href="<?= htmlspecialchars($leg['url_legislacao']) ?>" target="_blank">Acessar</a>
                                <?php else: ?>
                                    ⚠️ Sem link
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</body>
</html>
