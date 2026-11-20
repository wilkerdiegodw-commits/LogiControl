
<?php
require_once "conexao.php";

$nome = "";
$descricao = "";
$mensagem = "";
$tipoMensagem = "";

// CADASTRAR PRODUTO
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = trim($_POST["nome"] ?? "");
    $descricao = trim($_POST["descricao"] ?? "");

    if ($nome !== "" && $descricao !== "") {

        $sql = "INSERT INTO produtos (nome, descricao)
                VALUES (?, ?)";

        $stmt = $conexao->prepare($sql);

        if ($stmt) {

            $stmt->bind_param("ss", $nome, $descricao);

            if ($stmt->execute()) {

                $mensagem = "Produto cadastrado com sucesso!";
                $tipoMensagem = "success";

                $nome = "";
                $descricao = "";

            } else {

                $mensagem = "Erro ao cadastrar o produto.";
                $tipoMensagem = "danger";
            }

            $stmt->close();

        } else {

            $mensagem = "Erro ao preparar o cadastro.";
            $tipoMensagem = "danger";
        }

    } else {

        $mensagem = "Preencha todos os campos.";
        $tipoMensagem = "warning";
    }
}

// BUSCAR PRODUTOS CADASTRADOS
$resultado = $conexao->query(
    "SELECT id, nome, descricao
     FROM produtos
     ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Cadastro de Produtos - LogiControl</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="container mt-5 mb-5">

    <!-- CADASTRO DE PRODUTOS -->
    <div class="card shadow p-4">

        <h2 class="mb-4">📦 Cadastro de Produtos</h2>

        <!-- BOTÃO VOLTAR AO INÍCIO CORRIGIDO -->
        <div class="mb-3">

            <a
                href="/logicontrol/"
                class="btn btn-secondary"
            >
                🏠 Voltar ao início
            </a>

        </div>

        <?php if ($mensagem !== ""): ?>

            <div class="alert alert-<?php echo $tipoMensagem; ?>">

                <?php echo htmlspecialchars($mensagem); ?>

            </div>

        <?php endif; ?>

        <!-- FORMULÁRIO -->
        <form method="POST" action="">

            <div class="mb-3">

                <label for="nome" class="form-label">
                    Nome do produto
                </label>

                <input
                    type="text"
                    class="form-control"
                    id="nome"
                    name="nome"
                    placeholder="Digite o nome do produto"
                    value="<?php echo htmlspecialchars($nome); ?>"
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
                    placeholder="Descreva o produto"
                    required
                ><?php echo htmlspecialchars($descricao); ?></textarea>

            </div>

            <button type="submit" class="btn btn-primary">
                Cadastrar Produto
            </button>

        </form>

    </div>

    <!-- LISTAGEM DE PRODUTOS -->
    <div class="card shadow p-4 mt-4">

        <h4>📋 Produtos Cadastrados</h4>

        <div class="table-responsive">

            <table class="table table-bordered table-striped mt-3">

                <thead class="table-dark">

                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Ação</th>
                    </tr>

                </thead>

                <tbody>

                    <?php if ($resultado && $resultado->num_rows > 0): ?>

                        <?php while ($produto = $resultado->fetch_assoc()): ?>

                            <tr>

                                <td>
                                    <?php echo (int) $produto["id"]; ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($produto["nome"]); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($produto["descricao"]); ?>
                                </td>

                                <td>

                                    <a
                                        href="editar_produto.php?id=<?php echo (int) $produto["id"]; ?>"
                                        class="btn btn-warning btn-sm"
                                    >
                                        Editar
                                    </a>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="4" class="text-center">
                                Nenhum produto cadastrado.
                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

</body>
</html>

