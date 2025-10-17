<?php
// create_instituicao_table.php - Criar tabela instituicao e dados básicos

$host = '127.0.0.1';
$dbname = 'instituição';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>🔧 Configurando Banco de Dados</h2>";
    
    // 1. Primeiro, criar a tabela instituicao se não existir
    echo "<p>Criando tabela instituicao...</p>";
    
    $sql_instituicao = "
    CREATE TABLE IF NOT EXISTS `instituicao` (
      `id_unidade` int(11) NOT NULL AUTO_INCREMENT,
      `nome_unidade` varchar(100) DEFAULT NULL,
      `sigla` varchar(20) DEFAULT NULL,
      `endereco` varchar(200) DEFAULT NULL,
      `telefone` varchar(20) DEFAULT NULL,
      PRIMARY KEY (`id_unidade`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ";
    
    $pdo->exec($sql_instituicao);
    echo "<p style='color: green;'>✅ Tabela 'instituicao' criada/verificada com sucesso!</p>";
    
    // 2. Inserir unidades básicas
    echo "<p>Inserindo unidades básicas...</p>";
    
    $unidades = [
        [1, 'Prefeitura Municipal', 'PM', 'Praça da Matriz, 100', '(11) 1234-5678'],
        [2, 'Câmara Municipal', 'CM', 'Rua dos Vereadores, 50', '(11) 1234-5679'],
        [3, 'Secretaria de Educação', 'SEDU', 'Av. da Educação, 200', '(11) 1234-5680'],
        [4, 'Secretaria de Saúde', 'SES', 'Rua da Saúde, 300', '(11) 1234-5681'],
        [5, 'Secretaria de Finanças', 'SEFIN', 'Praça do Tesouro, 150', '(11) 1234-5682'],
        [6, 'Secretaria de Obras', 'SOBR', 'Av. das Construções, 400', '(11) 1234-5683'],
        [7, 'Secretaria do Meio Ambiente', 'SEMA', 'Rua Ecológica, 250', '(11) 1234-5684'],
        [8, 'Secretaria de Cultura', 'SECULT', 'Praça das Artes, 180', '(11) 1234-5685']
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO instituicao (id_unidade, nome_unidade, sigla, endereco, telefone) VALUES (?, ?, ?, ?, ?)");
    
    $unidades_inseridas = 0;
    foreach ($unidades as $unidade) {
        try {
            $stmt->execute($unidade);
            $unidades_inseridas++;
        } catch (PDOException $e) {
            // Ignora se já existir
        }
    }
    
    echo "<p style='color: green;'>✅ $unidades_inseridas unidades inseridas/verificadas!</p>";
    
    // 3. Verificar se a tabela legislacao existe e tem a chave estrangeira
    echo "<p>Verificando tabela legislacao...</p>";
    
    try {
        // Tentar adicionar a chave estrangeira se não existir
        $sql_fk = "ALTER TABLE legislacao 
                   ADD CONSTRAINT fk_legislacao_unidade 
                   FOREIGN KEY (id_unidade) 
                   REFERENCES instituicao(id_unidade) 
                   ON DELETE CASCADE 
                   ON UPDATE CASCADE";
        $pdo->exec($sql_fk);
        echo "<p style='color: green;'>✅ Chave estrangeira configurada com sucesso!</p>";
    } catch (PDOException $e) {
        echo "<p style='color: orange;'>ℹ️ Chave estrangeira já existe ou tabela legislacao não existe.</p>";
    }
    
    // 4. Mostrar unidades cadastradas
    $stmt = $pdo->query("SELECT * FROM instituicao ORDER BY id_unidade");
    $unidades_cadastradas = $stmt->fetchAll();
    
    echo "<h3>📊 Unidades Cadastradas no Sistema:</h3>";
    echo "<div style='overflow-x: auto;'>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
    echo "<tr style='background-color: #f8f9fa;'>
            <th>ID</th>
            <th>Nome da Unidade</th>
            <th>Sigla</th>
            <th>Endereço</th>
            <th>Telefone</th>
          </tr>";
    
    foreach ($unidades_cadastradas as $unidade) {
        echo "<tr>";
        echo "<td style='padding: 8px; text-align: center;'><strong>{$unidade['id_unidade']}</strong></td>";
        echo "<td style='padding: 8px;'>{$unidade['nome_unidade']}</td>";
        echo "<td style='padding: 8px; text-align: center;'>{$unidade['sigla']}</td>";
        echo "<td style='padding: 8px;'>{$unidade['endereco']}</td>";
        echo "<td style='padding: 8px;'>{$unidade['telefone']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
    
    // 5. Verificar se existem legislações cadastradas
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM legislacao");
        $total_legislacoes = $stmt->fetch()['total'];
        
        echo "<p><strong>📈 Total de legislações cadastradas:</strong> $total_legislacoes</p>";
    } catch (PDOException $e) {
        echo "<p style='color: orange;'>ℹ️ Tabela legislacao ainda não existe ou está vazia.</p>";
    }
    
    echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3 style='color: #155724; margin-top: 0;'>✅ Configuração Concluída!</h3>";
    echo "<p><strong>Próximo passo:</strong> Volte para a aplicação e use os IDs das unidades listadas acima (1 a 8) para cadastrar legislações.</p>";
    echo "</div>";
    
    echo "<div style='text-align: center; margin: 20px 0;'>";
    echo "<a href='index.php' style='background-color: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;'>";
    echo "➡️ Ir para a Aplicação Principal";
    echo "</a>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div style='background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;'>";
    echo "<h3>❌ Erro na Configuração</h3>";
    echo "<p><strong>Erro:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Soluções:</strong></p>";
    echo "<ol>
            <li>Verifique se o banco de dados 'instituição' existe</li>
            <li>Confirme as credenciais do MySQL no arquivo config.php</li>
            <li>Execute manualmente no phpMyAdmin: CREATE DATABASE `instituição`</li>
            <li>Verifique se o MySQL está rodando</li>
          </ol>";
    echo "</div>";
}
?>