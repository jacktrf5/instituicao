<?php
// test_connection.php - Descobrir credenciais do MySQL

echo "<h2>🔍 Teste de Conexão MySQL</h2>";

$hosts = ['127.0.0.1', 'localhost'];
$usuarios = ['root', 'admin'];
$senhas = ['', 'root', '123456', 'password'];
$portas = [3306, 3307];

foreach ($hosts as $host) {
    foreach ($usuarios as $usuario) {
        foreach ($senhas as $senha) {
            foreach ($portas as $porta) {
                try {
                    $dsn = "mysql:host=$host;port=$porta;charset=utf8mb4";
                    $pdo = new PDO($dsn, $usuario, $senha);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
                    echo "<div style='background: #d4edda; color: #155724; padding: 10px; margin: 5px;'>";
                    echo "✅ <strong>Conexão bem-sucedida!</strong><br>";
                    echo "Host: $host | Porta: $porta | Usuário: $usuario | Senha: " . ($senha ? '***' : '(vazia)');
                    echo "</div>";
                    
                } catch (PDOException $e) {
                    // Ignora erros, apenas mostra os acertos
                }
            }
        }
    }
}
?>