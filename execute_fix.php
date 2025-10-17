<?php
// execute_fix.php - CORREÇÃO URGENTE DAS TABELAS
echo "<h1>🔧 CORREÇÃO URGENTE DO BANCO DE DADOS</h1>";

$host = '127.0.0.1';
$dbname = 'instituição';
$username = 'root';
$password = '';

try {
    // Conectar ao MySQL
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color: green;'>✅ Conectado ao MySQL</p>";
    
    // Usar o banco
    $pdo->exec("USE `instituição`");
    
    // PASSO 1: EXCLUIR TABELAS EXISTENTES
    echo "<h2>🗑️ Removendo tabelas antigas...</h2>";
    
    try {
        $pdo->exec("DROP TABLE IF EXISTS legislacao");
        echo "<p style='color: green;'>✅ Tabela legislacao removida</p>";
    } catch (Exception $e) {
        echo "<p style='color: orange;'>ℹ️ " . $e->getMessage() . "</p>";
    }
    
    try {
        $pdo->exec("DROP TABLE IF EXISTS instituicao");
        echo "<p style='color: green;'>✅ Tabela instituicao removida</p>";
    } catch (Exception $e) {
        echo "<p style='color: orange;'>ℹ️ " . $e->getMessage() . "</p>";
    }
    
    // PASSO 2: CRIAR TABELA instituicao COM TODAS AS COLUNAS
    echo "<h2>🏗️ Criando tabela instituicao...</h2>";
    
    $sql_instituicao = "
    CREATE TABLE instituicao (
        id_unidade INT PRIMARY KEY AUTO_INCREMENT,
        nome_unidade VARCHAR(100) NOT NULL,
        sigla VARCHAR(20) NOT NULL,
        endereco VARCHAR(200),
        telefone VARCHAR(20),
        email VARCHAR(100),
        responsavel VARCHAR(100)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    
    $pdo->exec($sql_instituicao);
    echo "<p style='color: green;'>✅ Tabela instituicao criada com SUCESSO!</p>";
    
    // PASSO 3: INSERIR DADOS NA instituicao
    echo "<h2>📥 Inserindo unidades...</h2>";
    
    $unidades = [
        [1, 'Prefeitura Municipal', 'PM', 'Praça Central, 100', '(11) 1111-1111', 'prefeitura@municipio.gov.br', 'Prefeito Municipal'],
        [2, 'Câmara Municipal', 'CM', 'Rua dos Vereadores, 50', '(11) 2222-2222', 'camara@municipio.gov.br', 'Presidente da Câmara'],
        [3, 'Secretaria de Educação', 'SEDU', 'Av. Educação, 200', '(11) 3333-3333', 'educacao@municipio.gov.br', 'Secretário de Educação'],
        [4, 'Secretaria de Saúde', 'SES', 'Rua Saúde, 300', '(11) 4444-4444', 'saude@municipio.gov.br', 'Secretário de Saúde'],
        [5, 'Secretaria de Finanças', 'SEFIN', 'Praça Finanças, 150', '(11) 5555-5555', 'financas@municipio.gov.br', 'Secretário de Finanças']
    ];
    
    $stmt = $pdo->prepare("INSERT INTO instituicao (id_unidade, nome_unidade, sigla, endereco, telefone, email, responsavel) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($unidades as $unidade) {
        $stmt->execute($unidade);
    }
    
    echo "<p style='color: green;'>✅ " . count($unidades) . " unidades inseridas!</p>";
    
    // PASSO 4: CRIAR TABELA legislacao
    echo "<h2>📚 Criando tabela legislacao...</h2>";
    
    $sql_legislacao = "
    CREATE TABLE legislacao (
        id_legislacao INT PRIMARY KEY AUTO_INCREMENT,
        id_unidade INT NOT NULL,
        descricao_legislacao VARCHAR(30) NOT NULL,
        data_legislacao DATE NOT NULL,
        url_legislacao VARCHAR(50) NOT NULL,
        data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_unidade) REFERENCES instituicao(id_unidade)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    
    $pdo->exec($sql_legislacao);
    echo "<p style='color: green;'>✅ Tabela legislacao criada com SUCESSO!</p>";
    
    // PASSO 5: VERIFICAR ESTRUTURAS
    echo "<h2>🔍 Verificando estruturas...</h2>";
    
    // Verificar colunas da instituicao
    $stmt = $pdo->query("DESCRIBE instituicao");
    $colunas_instituicao = $stmt->fetchAll();
    
    echo "<h3>Colunas da tabela instituicao:</h3>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
    foreach ($colunas_instituicao as $coluna) {
        echo "<tr>";
        echo "<td>{$coluna['Field']}</td>";
        echo "<td>{$coluna['Type']}</td>";
        echo "<td>{$coluna['Null']}</td>";
        echo "<td>{$coluna['Key']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Verificar colunas da legislacao
    $stmt = $pdo->query("DESCRIBE legislacao");
    $colunas_legislacao = $stmt->fetchAll();
    
    echo "<h3>Colunas da tabela legislacao:</h3>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
    foreach ($colunas_legislacao as $coluna) {
        echo "<tr>";
        echo "<td>{$coluna['Field']}</td>";
        echo "<td>{$coluna['Type']}</td>";
        echo "<td>{$coluna['Null']}</td>";
        echo "<td>{$coluna['Key']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // PASSO 6: TESTAR CONSULTA
    echo "<h2>🧪 Testando consulta...</h2>";
    
    try {
        $teste = $pdo->query("
            SELECT l.*, i.nome_unidade, i.sigla 
            FROM legislacao l 
            LEFT JOIN instituicao i ON l.id_unidade = i.id_unidade
        ")->fetchAll();
        
        echo "<p style='color: green;'>✅ Consulta JOIN funcionando PERFEITAMENTE!</p>";
        echo "<p>Legislações encontradas: " . count($teste) . "</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Erro na consulta: " . $e->getMessage() . "</p>";
    }
    
    echo "<h1 style='color: green;'>🎉 BANCO DE DADOS CORRIGIDO COM SUCESSO!</h1>";
    echo "<p><a href='index.php' style='font-size: 20px; color: blue;'>👉 CLIQUE AQUI PARA TESTAR O SISTEMA</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red; font-size: 18px;'>❌ ERRO CRÍTICO: " . $e->getMessage() . "</p>";
}
?>