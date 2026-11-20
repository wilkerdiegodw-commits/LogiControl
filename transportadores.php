
<?php
require_once "conexao.php";

$mensagem = "";
$tipoMensagem = "";

$nome = "";
$documento = "";
$telefone = "";
$placa_veiculo = "";

// CADASTRAR TRANSPORTADOR
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = trim($_POST["nome"] ?? "");
    $documento = trim($_POST["documento"] ?? "");
    $telefone = trim($_POST["telefone"] ?? "");
    $placa_veiculo = trim($_POST["placa_veiculo"] ?? "");

    if ($nome !== "" && $telefone !== "") {

        $sql = "INSERT INTO transportadores
                (nome, documento, telefone, placa_veiculo)
                VALUES (?, ?, ?, ?)";

        $stmt = $conexao->prepare($sql);

        if ($stmt) {

            $stmt->bind_param(
                "ssss",
                $nome,
                $documento,
                $telefone,
                $placa_veiculo
            );

            if ($stmt->execute()) {

                $mensagem = "Transportador cadastrado com sucesso!";
                $tipoMensagem = "success";

                $nome = "";
                $documento = "";
                $telefone = "";
                $placa_veiculo = "";

            } else {

                $mensagem = "Erro ao cadastrar o transportador.";
                $tipoMensagem = "danger";
            }

            $stmt->close();

        } else {

            $mensagem = "Erro ao preparar o cadastro.";
            $tipoMensagem = "danger";
        }

    } else {

        $mensagem = "Preencha o nome e o telefone.";
        $tipoMensagem = "warning";
    }
}

// LISTAR TRANSPORTADORES
$resultado = $conexao->query(
    "SELECT id, nome, documento, telefone, placa_veiculo
     FROM transportadores
     ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Transportadores - LogiControl</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="container mt-5 mb-5">

    <!-- CADASTRO DE TRANSPORTADORES -->
    <div class="card shadow p-4 mb-4">

        <h2 class="mb-3">🚚 Cadastro de Transportadores</h2>

        <p class="text-muted">
            Cadastre os responsáveis pelo transporte das mercadorias.
        </p>

        <!-- BOTÃO VOLTAR CORRIGIDO -->
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

            <!-- NOME -->
            <div class="mb-3">

                <label class="form-label">
                    Nome do Transportador
                </label>

                <input
                    type="text"
                    name="nome"
                    class="form-control"
                    placeholder="Digite o nome"
                    value="<?php echo htmlspecialchars($nome); ?>"
                    required
                >

            </div>

            <!-- CPF OU CNPJ -->
            <div class="mb-3">

                <label class="form-label">
                    CPF ou CNPJ
                </label>

                <input
                    type="text"
                    name="documento"
                    class="form-control"
                    placeholder="Digite o CPF ou CNPJ"
                    value="<?php echo htmlspecialchars($documento); ?>"
                >

            </div>

            <!-- TELEFONE -->
            <div class="mb-3">

                <label class="form-label">
                    Telefone
                </label>

                <input
                    type="text"
                    name="telefone"
                    class="form-control"
                    placeholder="Digite o telefone"
                    value="<?php echo htmlspecialchars($telefone); ?>"
                    required
                >

            </div>

            <!-- PLACA -->
            <div class="mb-3">

                <label class="form-label">
                    Placa do Veículo
                </label>

                <input
                    type="text"
                    name="placa_veiculo"
                    class="form-control"
                    placeholder="Exemplo: ABC1D23"
                    value="<?php echo htmlspecialchars($placa_veiculo); ?>"
                >

            </div>

            <!-- CADASTRAR -->
            <button
                type="submit"
                class="btn btn-primary"
            >
                Cadastrar Transportador
            </button>

        </form>

    </div>

    <!-- LISTAGEM DE TRANSPORTADORES -->
    <div class="card shadow p-4">

        <h4 class="mb-3">
            📋 Transportadores Cadastrados
        </h4>

        <div class="table-responsive">

            <table class="table table-bordered table-striped">

                <thead class="table-dark">

                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>CPF/CNPJ</th>
                        <th>Telefone</th>
                        <th>Placa</th>
                        <th>Ação</th>
                    </tr>

                </thead>

                <tbody>

                <?php if ($resultado && $resultado->num_rows > 0): ?>

                    <?php while ($transportador = $resultado->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?php echo (int) $transportador["id"]; ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($transportador["nome"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($transportador["documento"] ?? ""); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($transportador["telefone"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($transportador["placa_veiculo"] ?? ""); ?>
                            </td>

                            <td>

                                <a
                                    href="editar_transportador.php?id=<?php echo (int) $transportador["id"]; ?>"
                                    class="btn btn-warning btn-sm"
                                >
                                    Editar
                                </a>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>

                        <td colspan="6" class="text-center">
                            Nenhum transportador cadastrado.
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

