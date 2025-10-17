<?php
// execute_setup.php - Script principal para configurar todo o banco
echo "<!DOCTYPE html>
<html>
<head>
    <title>🔧 Configuração do Sistema</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { background: #cce7ff; color: #004085; padding: 15px; border-radius: 5px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f8f9fa; }
        .btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 5px; }
        .btn-success { background: #28a745; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔧 Configuração Completa do Sistema</h1>";

// Configurações do banco
$host = '127.0.0.1';
$dbname = 'instituição';
$username = 'root';
$password = '';

try {
    // Conectar ao MySQL (sem selecionar banco primeiro)
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<div class='success'>✅ Conectado ao MySQL com sucesso!</div>";
    
    // PASSO 1: Criar banco de dados se não existir
    echo "<h2>📁 Passo 1: Verificando banco de dados</h2>";
    
    $stmt = $pdo->query("SHOW DATABASES LIKE 'instituição'");
    if (!$stmt->fetch()) {
        $pdo->exec("CREATE DATABASE `instituição` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
        echo "<div class='success'>✅ Banco de dados 'instituição' criado com sucesso!</div>";
    } else {
        echo "<div class='info'>ℹ️ Banco de dados 'instituição' já existe.</div>";
    }
    
    // Selecionar o banco
    $pdo->exec("USE `instituição`");
    
    // PASSO 2: Criar tabela instituicao
    echo "<h2>🏢 Passo 2: Criando tabela instituicao</h2>";
    
    $sql_instituicao = "
    CREATE TABLE IF NOT EXISTS `instituicao` (
      `id_unidade` int(11) NOT NULL AUTO_INCREMENT,
      `nome_unidade` varchar(100) NOT NULL,
      `sigla` varchar(20) NOT NULL,
      `endereco` varchar(200) DEFAULT NULL,
      `telefone` varchar(20) DEFAULT NULL,
      `email` varchar(100) DEFAULT NULL,
      `responsavel` varchar(100) DEFAULT NULL,
      PRIMARY KEY (`id_unidade`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ";
    
    $pdo->exec($sql_instituicao);
    echo "<div class='success'>✅ Tabela 'instituicao' criada/verificada com sucesso!</div>";
    
    // PASSO 3: Inserir unidades
    echo "<h2>📥 Passo 3: Inserindo unidades básicas</h2>";
    
    $unidades = [
        [1, 'Prefeitura Municipal', 'PM', 'Praça da Matriz, 100', '(11) 1234-5678', 'prefeitura@municipio.gov.br', 'Prefeito Municipal'],
        [2, 'Câmara Municipal', 'CM', 'Rua dos Vereadores, 50', '(11) 1234-5679', 'camara@municipio.gov.br', 'Presidente da Câmara'],
        [3, 'Secretaria de Educação', 'SEDU', 'Av. da Educação, 200', '(11) 1234-5680', 'educacao@municipio.gov.br', 'Secretário de Educação'],
        [4, 'Secretaria de Saúde', 'SES', 'Rua da Saúde, 300', '(11) 1234-5681', 'saude@municipio.gov.br', 'Secretário de Saúde'],
        [5, 'Secretaria de Finanças', 'SEFIN', 'Praça do Tesouro, 150', '(11) 1234-5682', 'financas@municipio.gov.br', 'Secretário de Finanças'],
        [6, 'Secretaria de Obras', 'SOBR', 'Av. das Construções, 400', '(11) 1234-5683', 'obras@municipio.gov.br', 'Secretário de Obras'],
        [7, 'Secretaria do Meio Ambiente', 'SEMA', 'Rua Ecológica, 250', '(11) 1234-5684', 'meioambiente@municipio.gov.br', 'Secretário do Meio Ambiente'],
        [8, 'Secretaria de Cultura', 'SECULT', 'Praça das Artes, 180', '(11) 1234-5685', 'cultura@municipio.gov.br', 'Secretário de Cultura'],
        [9, 'Secretaria de Esportes', 'SESP', 'Av. dos Esportes, 220', '(11) 1234-5686', 'esportes@municipio.gov.br', 'Secretário de Esportes'],
        [10, 'Secretaria de Transportes', 'SETR', 'Rua das Mobilidades, 190', '(11) 1234-5687', 'transportes@municipio.gov.br', 'Secretário de Transportes']
    ];
    
    $unidades_inseridas = 0;
    $unidades_atualizadas = 0;
    
    foreach ($unidades as $unidade) {
        // Verificar se a unidade já existe
        $stmt = $pdo->prepare("SELECT id_unidade FROM instituicao WHERE id_unidade = ?");
        $stmt->execute([$unidade[0]]);
        
        if ($stmt->fetch()) {
            // Atualizar se existir
            $sql_update = "UPDATE instituicao SET 
                          nome_unidade = ?, sigla = ?, endereco = ?, telefone = ?, email = ?, responsavel = ?
                          WHERE id_unidade = ?";
            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->execute([$unidade[1], $unidade[2], $unidade[3], $unidade[4], $unidade[5], $unidade[6], $unidade[0]]);
            $unidades_atualizadas++;
        } else {
            // Inserir se não existir
            $sql_insert = "INSERT INTO instituicao (id_unidade, nome_unidade, sigla, endereco, telefone, email, responsavel) 
                          VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = $pdo->prepare($sql_insert);
            $stmt_insert->execute($unidade);
            $unidades_inseridas++;
        }
    }
    
    echo "<div class='success'>✅ Unidades processadas: $unidades_inseridas inseridas, $unidades_atualizadas atualizadas!</div>";
    
    // PASSO 4: Criar tabela legislacao
    echo "<h2>📚 Passo 4: Criando tabela legislacao</h2>";
    
    // Primeiro, remover a chave estrangeira se existir (para recriação)
    try {
        $pdo->exec("ALTER TABLE legislacao DROP FOREIGN KEY fk_legislacao_unidade");
        echo "<div class='info'>ℹ️ Chave estrangeira anterior removida.</div>";
    } catch (Exception $e) {
        // Ignora se não existir
    }
    
    $sql_legislacao = "
    CREATE TABLE IF NOT EXISTS `legislacao` (
      `id_legislacao` int(11) NOT NULL AUTO_INCREMENT,
      `id_unidade` int(11) NOT NULL,
      `descricao_legislacao` varchar(30) NOT NULL,
      `data_legislacao` date NOT NULL,
      `url_legislacao` varchar(50) NOT NULL,
      `data_cadastro` timestamp DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id_legislacao`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ";
    
    $pdo->exec($sql_legislacao);
    echo "<div class='success'>✅ Tabela 'legislacao' criada/verificada com sucesso!</div>";
    
    // PASSO 5: Adicionar chave estrangeira
    echo "<h2>🔗 Passo 5: Configurando chave estrangeira</h2>";
    
    $sql_fk = "ALTER TABLE legislacao 
               ADD CONSTRAINT fk_legislacao_unidade 
               FOREIGN KEY (id_unidade) 
               REFERENCES instituicao(id_unidade) 
               ON DELETE CASCADE 
               ON UPDATE CASCADE";
    
    $pdo->exec($sql_fk);
    echo "<div class='success'>✅ Chave estrangeira configurada com sucesso!</div>";
    
    // PASSO 6: Mostrar resumo
    echo "<h2>📊 Passo 6: Resumo do Sistema</h2>";
    
    // Contar unidades
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM instituicao");
    $total_unidades = $stmt->fetch()['total'];
    
    // Contar legislações
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM legislacao");
    $total_legislacoes = $stmt->fetch()['total'];
    
    // Mostrar unidades
    $stmt = $pdo->query("SELECT id_unidade, nome_unidade, sigla FROM instituicao ORDER BY id_unidade");
    $unidades = $stmt->fetchAll();
    
    echo "<div class='info'>";
    echo "<p><strong>Unidades cadastradas:</strong> $total_unidades</p>";
    echo "<p><strong>Legislações cadastradas:</strong> $total_legislacoes</p>";
    echo "</div>";
    
    echo "<h3>🏢 Unidades Disponíveis:</h3>";
    echo "<table>";
    echo "<tr><th>ID</th><th>Nome</th><th>Sigla</th></tr>";
    foreach ($unidades as $unidade) {
        echo "<tr>";
        echo "<td><strong>{$unidade['id_unidade']}</strong></td>";
        echo "<td>{$unidade['nome_unidade']}</td>";
        echo "<td>{$unidade['sigla']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // CONCLUSÃO
    echo "<div class='success' style='text-align: center; padding: 30px;'>";
    echo "<h2>🎉 Configuração Concluída com Sucesso!</h2>";
    echo "<p>O sistema está pronto para uso. Você pode usar os IDs de 1 a 10 para cadastrar legislações.</p>";
    echo "</div>";
    
    echo "<div style='text-align: center; margin: 20px 0;'>";
    echo "<a href='index.php' class='btn btn-success'>🚀 Ir para a Aplicação Principal</a>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div class='error'>";
    echo "<h3>❌ Erro na Configuração</h3>";
    echo "<p><strong>Erro:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Possíveis soluções:</strong></p>";
    echo "<ul>
            <li>Verifique se o MySQL está rodando</li>
            <li>Confirme o usuário e senha no arquivo config.php</li>
            <li>No XAMPP: usuário 'root', senha vazia</li>
            <li>No MAMP: usuário 'root', senha 'root'</li>
            <li>Execute manualmente: CREATE DATABASE `instituição`</li>
          </ul>";
    echo "</div>";
}

echo "</div></body></html>";
?>