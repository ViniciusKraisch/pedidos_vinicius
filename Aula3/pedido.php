<?php
$host = 'localhost'; 
$db = 'biblioteca_vini';
$user = 'root';
$pass = 'root'; 

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$tableCheckSql = "SHOW TABLES LIKE 'pedidos'";
$tableCheckResult = $conn->query($tableCheckSql);

if ($tableCheckResult->num_rows == 0) {
    die("Erro: A tabela 'pedidos' não existe no banco de dados.");
}

$columns = ['nome_cliente', 'nome_produto', 'quantidade', 'data_pedido'];
$selectedColumn = isset($_POST['column']) ? $_POST['column'] : '';

$validColumn = in_array($selectedColumn, $columns);

if ($validColumn) {
    $dataSql = "SELECT id, nome_cliente, nome_produto, quantidade, data_pedido FROM pedidos";
    $dataResult = $conn->query($dataSql);

    if (!$dataResult) {
        die("Erro ao executar a consulta para obter os dados: " . $conn->error);
    }
} else {
    $dataResult = null;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes dos Pedidos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Detalhes dos Pedidos</h1>
    <form method="POST">
        <label for="column">Escolha uma coluna para exibir:</label>
        <select name="column" id="column" onchange="this.form.submit()">
            <option value="">-- Selecione --</option>
            <?php
            foreach ($columns as $column) {
                $selected = ($column == $selectedColumn) ? ' selected' : '';
                $displayName = str_replace('_', ' ', ucfirst($column)); 
                echo "<option value='" . htmlspecialchars($column) . "'" . $selected . ">" . htmlspecialchars($displayName) . "</option>";
            }
            ?>
        </select>
    </form>

    <?php if ($dataResult && $validColumn): ?>
        <h2>Detalhes da Coluna Selecionada</h2>
        <table> border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome do Cliente</th>
                    <th>Nome do Produto</th>
                    <th>Quantidade</th>
                    <th>Data do Pedido</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $dataResult->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nome_cliente']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nome_produto']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['quantidade']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['data_pedido']) . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    <?php elseif ($selectedColumn): ?>
        <p>Nenhum dado encontrado para a coluna selecionada.</p>
    <?php endif; ?>

    <?php
    if ($dataResult) {
        $dataResult->free();
    }
    ?>

</body>
</html>

<?php
$conn->close();
?>