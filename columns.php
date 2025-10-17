<?php
// fix_missing_columns.php - Script para corrigir colunas faltantes
echo "<h1>🔧 Corrigindo Estrutura do Banco</h1>";

$host = '127.0.0.1';
$dbname = 'instituição';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color: green;'>✅ Conectado ao banco 'instituição'</p>";
    
    // PASSO 1: Verificar colunas atuais
    echo "<h2>🔍 Verificando estrutura atual...</h2>";
    $colunas = $pdo->query("DESCRIBE instituicao")->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Coluna</th><th>Tipo</th><th>Nulo</th></tr>";
    foreach ($colunas as $coluna) {
        echo "<tr>";
        echo "<td>{$coluna['Field']}</td>";
        echo "<td>{$coluna['Type']}</td>";
        echo "<td>{$coluna['Null']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // PASSO 2: Adicionar coluna sigla se não existir
    echo "<h2>➕ Adicionando coluna sigla...</h2>";
    
    $coluna_existe = $pdo->query("SHOW COLUMNS FROM instituicao LIKE 'sigla'")->fetch();
    if (!$coluna_existe) {
        $pdo->exec("ALTER TABLE instituicao ADD COLUMN sigla VARCHAR(20) NOT NULL DEFAULT 'N/A' AFTER nome_unidade");
        echo "<p style='color: green;'>✅ Coluna 'sigla' adicionada com sucesso!</p>";
    } else {
        echo "<p style='color: orange;'>ℹ️ Coluna 'sigla' já existe</p>";
    }
    
    // PASSO 3: Atualizar siglas das unidades existentes
    echo "<h2>🔄 Atualizando siglas...</h2>";
    
    $siglas = [
        1 => 'PM',
        2 => 'CM', 
        3 => 'SEDU',
        4 => 'SES',
        5 => 'SEFIN'
    ];
    
    $stmt = $pdo->prepare("UPDATE instituicao SET sigla = ? WHERE id_unidade = ?");
    $atualizadas = 0;
    
    foreach ($siglas as $id => $sigla) {
        try {
            $stmt->execute([$sigla, $id]);
            $atualizadas++;
            echo "<p style='color: green;'>✅ Unidade $id: sigla '$sigla'</p>";
        } catch (Exception $e) {
            echo "<p style='color: orange;'>ℹ️ Unidade $id não encontrada</p>";
        }
    }
    
    echo "<p style='color: green;'>✅ $atualizadas unidades atualizadas!</p>";
    
    // PASSO 4: Verificação final
    echo "<h2>✅ Verificação final</h2>";
    
    $unidades = $pdo->query("SELECT id_unidade, nome_unidade, sigla FROM instituicao ORDER BY id_unidade")->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #007bff; color: white;'><th>ID</th><th>Nome</th><th>Sigla</th></tr>";
    foreach ($unidades as $unidade) {
        echo "<tr>";
        echo "<td>{$unidade['id_unidade']}</td>";
        echo "<td>{$unidade['nome_unidade']}</td>";
        echo "<td>{$unidade['sigla']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h1 style='color: green; border: 3px solid green; padding: 20px; text-align: center; margin-top: 30px;'>";
    echo "🎉 ESTRUTURA CORRIGIDA COM SUCESSO!";
    echo "</h1>";
    
    echo "<div style='text-align: center; margin: 30px;'>";
    echo "<a href='index.php' style='font-size: 24px; background: #28a745; color: white; padding: 15px 40px; text-decoration: none; border-radius: 10px; display: inline-block;'>";
    echo "🚀 VOLTAR PARA O SISTEMA";
    echo "</a>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red; font-size: 20px;'>❌ ERRO: " . $e->getMessage() . "</p>";
}
?>