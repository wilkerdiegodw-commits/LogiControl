
<?php
require_once "conexao.php";

$mensagem = "";

// Verificar se o ID foi informado
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("Produto não informado.");
}

$id = (int) $_GET["id"];

// Atualizar produto
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = trim($_POST["nome"] ?? "");
    $descricao = trim($_POST["descricao"] ?? "");

    if ($nome != "" && $descricao != "") {

        $sql = $conexao->prepare(
            "UPDATE produtos SET nome = ?, descricao = ? WHERE id = ?"
        );

        $sql->bind_param("ssi", $nome, $descricao, $id);

        if ($sql->execute()) {
            $sql->close();

            header("Location: produtos.php");
            exit;
        } else {
            $mensagem = "Erro ao atualizar o produto.";
        }

        $sql->close();

    } else {
        $mensagem = "Preencha todos os campos.";
    }
}

// Buscar os dados atuais do produto
$sql = $conexao->prepare(
    "SELECT nome, descricao FROM produtos WHERE id = ?"
);

$sql->bind_param("i", $id);
$sql->execute();

$resultado = $sql->get_result();
$produto = $resultado->fetch_assoc();

$sql->close();

// Verificar se o produto existe
if (!$produto) {
    die("Produto não encontrado.");
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar Produto - LogiControl</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="card shadow p-4">

        <h2 class="mb-3">✏️ Editar Produto</h2>

        <p class="text-secondary">
            Altere as informações do produto abaixo.
        </p>

        <?php if ($mensagem != ""): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">

                <label for="nome" class="form-label">
                    Nome do produto
                </label>

                <input
                    type="text"
                    class="form-control"
                    id="nome"
                    name="nome"
                    value="<?php echo htmlspecialchars($produto["nome"]); ?>"
                    required
                >

            </div>

            <div class="mb-3">

                <label for="descricao" class="form-label">
                    Descrição do produto
                </label>

                <textarea
                    class="form-control"
                    id="descricao"
                    name="descricao"
                    rows="4"
                    required
                ><?php echo htmlspecialchars($produto["descricao"]); ?></textarea>

            </div>

            <button type="submit" class="btn btn-primary">
                Salvar Alterações
            </button>

            <a href="produtos.php" class="btn btn-secondary">
                Cancelar
            </a>

        </form>

    </div>

</div>

</body>
</html>
