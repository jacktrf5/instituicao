<?php
// remove_foreign_key.php - Remover a chave estrangeira temporariamente

$host = '127.0.0.1';
$dbname = 'instituição';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>🔧 Removendo Chave Estrangeira Temporariamente</h2>";
    
    // Remover a chave estrangeira
    $sql = "ALTER TABLE legislacao DROP FOREIGN KEY fk_legislacao_unidade";
    $pdo->exec($sql);
    
    echo "<p style='color: green;'>✅ Chave estrangeira removida com sucesso!</p>";
    echo "<p style='color: orange;'>⚠️ Agora você pode cadastrar legislações com qualquer ID unidade.</p>";
    
    echo "<p style='margin-top: 20px;'><a href='index.php' style='color: blue; text-decoration: none; font-weight: bold;'>➡️ Ir para a aplicação</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
    echo "<p>Tentando criar a tabela instituicao automaticamente...</p>";
    
    // Se der erro, tentar criar a tabela instituicao
    include 'create_instituicao_table.php';
}
?>