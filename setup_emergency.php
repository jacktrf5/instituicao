<?php
// setup_emergency.php - SOLUÇÃO DEFINITIVA
echo "<h1>🚨 SOLUÇÃO DE EMERGÊNCIA</h1>";

$host = '127.0.0.1';
$dbname = 'instituição';
$username = 'root';
$password = '';

try {
    // Conectar ao MySQL
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color: green;'>✅ Conectado ao MySQL</p>";
    
    // Criar banco se não existir
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `instituição` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
    $pdo->exec("USE `instituição`");
    echo "<p style='color: green;'>✅ Banco 'instituição' pronto</p>";
    
    // CRIAR TABELA instituicao COM FORÇA
    $pdo->exec("DROP TABLE IF EXISTS instituicao");
    
    $sql_instituicao = "
    CREATE TABLE instituicao (
      id_unidade INT PRIMARY KEY AUTO_INCREMENT,
      nome_unidade VARCHAR(100) NOT NULL,
      sigla VARCHAR(20) NOT NULL,
      endereco VARCHAR(200),
      telefone VARCHAR(20)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    
    $pdo->exec($sql_instituicao);
    echo "<p style='color: green;'>✅ Tabela 'instituicao' CRIADA COM SUCESSO!</p>";
    
    // INSERIR DADOS NA FORÇA
    $unidades = [
        [1, 'Prefeitura Municipal', 'PM', 'Praça Central, 100', '(11) 1111-1111'],
        [2, 'Câmara Municipal', 'CM', 'Rua dos Vereadores, 50', '(11) 2222-2222'],
        [3, 'Secretaria de Educação', 'SEDU', 'Av. Educação, 200', '(11) 3333-3333'],
        [4, 'Secretaria de Saúde', 'SES', 'Rua Saúde, 300', '(11) 4444-4444'],
        [5, 'Secretaria de Finanças', 'SEFIN', 'Praça Finanças, 150', '(11) 5555-5555']
    ];
    
    foreach ($unidades as $unidade) {
        $pdo->exec("INSERT INTO instituicao (id_unidade, nome_unidade, sigla, endereco, telefone) 
                   VALUES ($unidade[0], '$unidade[1]', '$unidade[2]', '$unidade[3]', '$unidade[4]')");
    }
    
    echo "<p style='color: green;'>✅ 5 unidades inseridas com força!</p>";
    
    // CRIAR TABELA legislacao
    $pdo->exec("DROP TABLE IF EXISTS legislacao");
    
    $sql_legislacao = "
    CREATE TABLE legislacao (
      id_legislacao INT PRIMARY KEY AUTO_INCREMENT,
      id_unidade INT NOT NULL,
      descricao_legislacao VARCHAR(30) NOT NULL,
      data_legislacao DATE NOT NULL,
      url_legislacao VARCHAR(50) NOT NULL,
      FOREIGN KEY (id_unidade) REFERENCES instituicao(id_unidade)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    
    $pdo->exec($sql_legislacao);
    echo "<p style='color: green;'>✅ Tabela 'legislacao' criada com chave estrangeira!</p>";
    
    // VERIFICAR
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll();
    
    echo "<h2>📊 TABELAS CRIADAS:</h2>";
    foreach ($tables as $table) {
        echo "<p style='color: blue;'>📁 " . $table[0] . "</p>";
    }
    
    echo "<h2>🎉 SISTEMA CONFIGURADO COM SUCESSO!</h2>";
    echo "<p><a href='index.php' style='font-size: 20px; color: green;'>👉 CLIQUE AQUI PARA IR PARA A APLICAÇÃO</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red; font-size: 20px;'>❌ ERRO: " . $e->getMessage() . "</p>";
}
?>