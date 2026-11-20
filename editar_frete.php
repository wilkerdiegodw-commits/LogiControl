
<?php
require_once "conexao.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    die("Frete inválido.");
}

$mensagem = "";
$erro = "";

// Atualizar o frete
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $produto_id = filter_input(INPUT_POST, "produto_id", FILTER_VALIDATE_INT);
    $origem_id = filter_input(INPUT_POST, "origem_id", FILTER_VALIDATE_INT);
    $destino_id = filter_input(INPUT_POST, "destino_id", FILTER_VALIDATE_INT);
    $transportador_id = filter_input(INPUT_POST, "transportador_id", FILTER_VALIDATE_INT);
    $data_frete = $_POST["data_frete"] ?? "";

    $dataValida = DateTime::createFromFormat("!Y-m-d", $data_frete);

    if (
        !$produto_id || !$origem_id || !$destino_id ||
        !$transportador_id ||
        !$dataValida ||
        $dataValida->format("Y-m-d") !== $data_frete
    ) {
        $erro = "Preencha todos os campos corretamente.";
    } else {

        $sql = "UPDATE fretes SET
                    produto_id = ?,
                    endereco_origem_id = ?,
                    endereco_destino_id = ?,
                    transportador_id = ?,
                    data_frete = ?
                WHERE id = ?";

        $stmt = $conexao->prepare($sql);

        $stmt->bind_param(
            "iiiisi",
            $produto_id,
            $origem_id,
            $destino_id,
            $transportador_id,
            $data_frete,
            $id
        );

        if ($stmt->execute()) {
            $mensagem = "Frete atualizado com sucesso!";
        } else {
            $erro = "Erro ao atualizar: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Buscar os dados atuais do frete
$stmt = $conexao->prepare("SELECT * FROM fretes WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$frete = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$frete) {
    die("Frete não encontrado.");
}

// Buscar produtos
$produtos = $conexao->query(
    "SELECT id, nome FROM produtos ORDER BY nome"
);

// Buscar endereços de origem
$origens = $conexao->query(
    "SELECT id, rua, numero, cidade
     FROM enderecos
     WHERE tipo = 'Origem'
     ORDER BY rua"
);

// Buscar endereços de destino
$destinos = $conexao->query(
    "SELECT id, rua, numero, cidade
     FROM enderecos
     WHERE tipo = 'Destino'
     ORDER BY rua"
);

// Buscar transportadores
$transportadores = $conexao->query(
    "SELECT id, nome FROM transportadores ORDER BY nome"
);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Frete - LogiControl</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="card shadow">
        <div class="card-body">

            <h2 class="mb-4">Editar Frete #<?= $id ?></h2>

            <?php if ($mensagem): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($mensagem) ?>
                </div>
            <?php endif; ?>

            <?php if ($erro): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($erro) ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <div class="mb-3">
                    <label class="form-label">Produto</label>

                    <select name="produto_id" class="form-select" required>
                        <?php while ($produto = $produtos->fetch_assoc()): ?>
                            <option value="<?= $produto['id'] ?>"
                                <?= $produto['id'] == $frete['produto_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($produto['nome']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Endereço de Origem</label>

                    <select name="origem_id" class="form-select" required>
                        <?php while ($origem = $origens->fetch_assoc()): ?>
                            <option value="<?= $origem['id'] ?>"
                                <?= $origem['id'] == $frete['endereco_origem_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($origem['rua'] . ', ' . $origem['numero'] . ' - ' . $origem['cidade']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Endereço de Destino</label>

                    <select name="destino_id" class="form-select" required>
                        <?php while ($destino = $destinos->fetch_assoc()): ?>
                            <option value="<?= $destino['id'] ?>"
                                <?= $destino['id'] == $frete['endereco_destino_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($destino['rua'] . ', ' . $destino['numero'] . ' - ' . $destino['cidade']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Transportador</label>

                    <select name="transportador_id" class="form-select" required>
                        <?php while ($transportador = $transportadores->fetch_assoc()): ?>
                            <option value="<?= $transportador['id'] ?>"
                                <?= $transportador['id'] == $frete['transportador_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($transportador['nome']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Data do Frete</label>

                    <input
                        type="date"
                        name="data_frete"
                        class="form-control"
                        value="<?= htmlspecialchars($frete['data_frete']) ?>"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-success">
                    Salvar Alterações
                </button>

                <a href="listar_fretes.php" class="btn btn-secondary">
                    Voltar
                </a>

            </form>

        </div>
    </div>

</div>

</body>
</html>