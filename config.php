<?php
// config.php com autocorreção
$host = '127.0.0.1';
$dbname = 'instituição';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verificar se a coluna nome_unidade existe
    try {
        $pdo->query("SELECT nome_unidade FROM instituicao LIMIT 1");
    } catch (Exception $e) {
        // Se não existir, criar a coluna
        $pdo->exec("ALTER TABLE instituicao ADD COLUMN nome_unidade VARCHAR(100) NOT NULL DEFAULT 'Unidade' AFTER id_unidade");
        $pdo->exec("UPDATE instituicao SET nome_unidade = CONCAT('Unidade ', id_unidade)");
    }
    
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?>