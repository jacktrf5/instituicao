<?php
// setup_database.php - Script para configurar o banco de dados

echo "<h2>🔧 Configuração do Banco de Dados</h2>";

$host = '127.0.0.1';
$username = 'root';
$password = '';

// Tentar conectar sem selecionar o banco primeiro
try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color: green;'>✅ Conectado ao MySQL com sucesso!</p>";
    
    // Verificar se o banco existe
    $stmt = $pdo->query("SHOW DATABASES LIKE 'instituição'");
    $db_exists = $stmt->fetch();
    
    if (!$db_exists) {
        echo "<p>📋 Criando banco de dados 'instituição'...</p>";
        $pdo->exec("CREATE DATABASE `instituição` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
        echo "<p style='color: green;'>✅ Banco de dados criado com sucesso!</p>";
    } else {
        echo "<p style='color: green;'>✅ Banco de dados 'instituição' já existe!</p>";
    }
    
    // Selecionar o banco
    $pdo->exec("USE `instituição`");
    
    // Criar tabela legislacao
    $sql = "
    CREATE TABLE IF NOT EXISTS `legislacao` (
      `id_legislacao` int(11) NOT NULL AUTO_INCREMENT,
      `id_unidade` int(11) DEFAULT NULL,
      `descricao_legislacao` varchar(30) DEFAULT NULL,
      `data_legislacao` date DEFAULT NULL,
      `url_legislacao` varchar(50) DEFAULT NULL,
      PRIMARY KEY (`id_legislacao`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ";
    
    $pdo->exec($sql);
    echo "<p style='color: green;'>✅ Tabela 'legislacao' criada/verificada com sucesso!</p>";
    
    // Inserir dados de exemplo
    $dados_exemplo = [
        [1, 'Lei Orgânica Municipal', '2024-01-15', 'https://exemplo.com/lei-organica'],
        [1, 'Regimento Interno', '2024-02-20', 'https://exemplo.com/regimento'],
        [2, 'Portaria 001/2024', '2024-03-10', 'https://exemplo.com/portaria001']
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO legislacao (id_unidade, descricao_legislacao, data_legislacao, url_legislacao) VALUES (?, ?, ?, ?)");
    
    foreach ($dados_exemplo as $dado) {
        $stmt->execute($dado);
    }
    
    echo "<p style='color: green;'>✅ Dados de exemplo inseridos!</p>";
    echo "<p><a href='index.php' style='color: blue;'>➡️ Ir para a aplicação</a></p>";
    
} catch (PDOException $e) {
    echo "<div style='color: red; padding: 10px; background: #f8d7da;'>";
    echo "<h3>❌ Erro:</h3>";
    echo "<p><strong>" . $e->getMessage() . "</strong></p>";
    echo "<h4>🔧 Soluções:</h4>";
    echo "<ol>
            <li>Verifique se o MySQL/MariaDB está instalado e rodando</li>
            <li>No XAMPP/WAMP: usuário é 'root' e senha está vazia</li>
            <li>No MAMP: usuário 'root', senha 'root'</li>
            <li>No Linux: execute 'sudo mysql -u root -p' para testar</li>
            <li>Verifique a porta do MySQL (padrão: 3306)</li>
          </ol>";
    echo "</div>";
}
?>