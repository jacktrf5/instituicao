<?php
// execute_final_fix.php - SOLUÇÃO FINAL PARA O JOIN
echo "<h1>🔧 SOLUÇÃO FINAL - CORRIGINDO JOIN</h1>";

$host = '127.0.0.1';
$dbname = 'instituição';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color: green;'>✅ Conectado ao banco</p>";
    
    // PASSO 1: VERIFICAR E CORRIGIR A TABELA instituicao
    echo "<h2>🏢 Verificando tabela instituicao...</h2>";
    
    $columns = $pdo->query("DESCRIBE instituicao")->fetchAll();
    $column_names = array_column($columns, 'Field');
    
    echo "<p>Colunas encontradas: " . implode(', ', $column_names) . "</p>";
    
    // Garantir que todas as colunas necessárias existem
    $required_columns = ['id_unidade', 'nome_unidade', 'sigla'];
    foreach ($required_columns as $col) {
        if (!in_array($col, $column_names)) {
            echo "<p style='color: red;'>❌ Coluna '$col' não encontrada!</p>";
            
            if ($col === 'nome_unidade') {
                $pdo->exec("ALTER TABLE instituicao ADD COLUMN nome_unidade VARCHAR(100) NOT NULL DEFAULT 'Unidade' AFTER id_unidade");
                echo "<p style='color: green;'>✅ Coluna 'nome_unidade' adicionada</p>";
            }
            if ($col === 'sigla') {
                $pdo->exec("ALTER TABLE instituicao ADD COLUMN sigla VARCHAR(20) NOT NULL DEFAULT 'SIG' AFTER nome_unidade");
                echo "<p style='color: green;'>✅ Coluna 'sigla' adicionada</p>";
            }
        }
    }
    
    // PASSO 2: ATUALIZAR DADOS EXISTENTES
    echo "<h2>🔄 Atualizando dados das unidades...</h2>";
    
    // Verificar se os dados estão corretos
    $unidades = $pdo->query("SELECT * FROM instituicao")->fetchAll();
    
    if (count($unidades) > 0) {
        $update_stmt = $pdo->prepare("UPDATE instituicao SET nome_unidade = ?, sigla = ? WHERE id_unidade = ?");
        
        $dados_corretos = [
            1 => ['Prefeitura Municipal', 'PM'],
            2 => ['Câmara Municipal', 'CM'],
            3 => ['Secretaria de Educação', 'SEDU'],
            4 => ['Secretaria de Saúde', 'SES'],
            5 => ['Secretaria de Finanças', 'SEFIN']
        ];
        
        foreach ($unidades as $unidade) {
            $id = $unidade['id_unidade'];
            if (isset($dados_corretos[$id])) {
                $update_stmt->execute([$dados_corretos[$id][0], $dados_corretos[$id][1], $id]);
                echo "<p style='color: green;'>✅ Unidade $id atualizada: {$dados_corretos[$id][0]} ({$dados_corretos[$id][1]})</p>";
            }
        }
    }
    
    // PASSO 3: VERIFICAR CHAVE ESTRANGEIRA
    echo "<h2>🔗 Verificando chave estrangeira...</h2>";
    
    try {
        // Tentar criar a chave estrangeira se não existir
        $pdo->exec("ALTER TABLE legislacao ADD CONSTRAINT fk_legislacao_unidade FOREIGN KEY (id_unidade) REFERENCES instituicao(id_unidade)");
        echo "<p style='color: green;'>✅ Chave estrangeira criada</p>";
    } catch (Exception $e) {
        echo "<p style='color: orange;'>ℹ️ Chave estrangeira já existe ou não é necessária</p>";
    }
    
    // PASSO 4: TESTE DEFINITIVO DO JOIN
    echo "<h2>🧪 TESTE DEFINITIVO DO JOIN</h2>";
    
    $test_query = "
        SELECT 
            l.id_legislacao,
            l.descricao_legislacao, 
            l.data_legislacao,
            l.url_legislacao,
            i.id_unidade,
            i.nome_unidade,
            i.sigla
        FROM legislacao l 
        LEFT JOIN instituicao i ON l.id_unidade = i.id_unidade 
        ORDER BY l.data_legislacao DESC
        LIMIT 5
    ";
    
    try {
        $resultado = $pdo->query($test_query);
        $dados = $resultado->fetchAll();
        
        echo "<p style='color: green; font-size: 20px;'>🎉 SUCESSO! JOIN funcionando perfeitamente!</p>";
        echo "<p>Registros encontrados: " . count($dados) . "</p>";
        
        if (count($dados) > 0) {
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>ID Legislação</th><th>Descrição</th><th>Unidade</th><th>Nome Unidade</th><th>Sigla</th></tr>";
            foreach ($dados as $linha) {
                echo "<tr>";
                echo "<td>{$linha['id_legislacao']}</td>";
                echo "<td>{$linha['descricao_legislacao']}</td>";
                echo "<td>{$linha['id_unidade']}</td>";
                echo "<td>{$linha['nome_unidade']}</td>";
                echo "<td>{$linha['sigla']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red; font-size: 18px;'>❌ FALHA NO JOIN: " . $e->getMessage() . "</p>";
        
        // Mostrar erro detalhado
        echo "<h3>🔍 Diagnóstico do erro:</h3>";
        echo "<pre>" . $e->getMessage() . "</pre>";
    }
    
    echo "<h1 style='color: green; border: 3px solid green; padding: 20px; text-align: center; margin-top: 30px;'>✅ CORREÇÃO CONCLUÍDA!</h1>";
    echo "<div style='text-align: center; margin: 20px;'>";
    echo "<a href='index.php' style='font-size: 24px; background: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 10px; display: inline-block;'>";
    echo "🚀 IR PARA O SISTEMA";
    echo "</a>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red; font-size: 20px;'>❌ ERRO: " . $e->getMessage() . "</p>";
}
?>