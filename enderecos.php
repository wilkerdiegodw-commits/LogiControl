
<?php
require_once "conexao.php";

$mensagem = "";
$tipoMensagem = "";

$tipo = "";
$rua = "";
$numero = "";
$bairro = "";
$cidade = "";

// CADASTRAR ENDEREÇO
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $tipo = $_POST["tipo"] ?? "";
    $rua = trim($_POST["rua"] ?? "");
    $numero = trim($_POST["numero"] ?? "");
    $bairro = trim($_POST["bairro"] ?? "");
    $cidade = trim($_POST["cidade"] ?? "");

    if (
        in_array($tipo, ["Origem", "Destino"], true) &&
        $rua !== "" &&
        $numero !== "" &&
        $bairro !== "" &&
        $cidade !== ""
    ) {

        $sql = "INSERT INTO enderecos
                (tipo, rua, numero, bairro, cidade)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $conexao->prepare($sql);

        if ($stmt) {

            $stmt->bind_param(
                "sssss",
                $tipo,
                $rua,
                $numero,
                $bairro,
                $cidade
            );

            if ($stmt->execute()) {

                $mensagem = "Endereço cadastrado com sucesso!";
                $tipoMensagem = "success";

                $tipo = "";
                $rua = "";
                $numero = "";
                $bairro = "";
                $cidade = "";

            } else {

                $mensagem = "Erro ao cadastrar endereço.";
                $tipoMensagem = "danger";
            }

            $stmt->close();

        } else {

            $mensagem = "Erro ao preparar o cadastro.";
            $tipoMensagem = "danger";
        }

    } else {

        $mensagem = "Preencha todos os campos corretamente.";
        $tipoMensagem = "warning";
    }
}

// LISTAR ENDEREÇOS CADASTRADOS
$resultado = $conexao->query(
    "SELECT id, tipo, rua, numero, bairro, cidade
     FROM enderecos
     ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Endereços - LogiControl</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="container mt-5 mb-5">

    <!-- CADASTRO DE ENDEREÇOS -->
    <div class="card shadow p-4 mb-4">

        <h2 class="mb-3">📍 Cadastro de Endereços</h2>

        <p class="text-muted">
            Cadastre os endereços de origem e destino das entregas.
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

        <form method="POST" action="">

            <!-- TIPO DE ENDEREÇO -->
            <div class="mb-3">

                <label class="form-label">
                    Tipo de endereço
                </label>

                <select
                    name="tipo"
                    class="form-select"
                    required
                >

                    <option value="">Selecione</option>

                    <option
                        value="Origem"
                        <?php echo $tipo === "Origem" ? "selected" : ""; ?>
                    >
                        Origem
                    </option>

                    <option
                        value="Destino"
                        <?php echo $tipo === "Destino" ? "selected" : ""; ?>
                    >
                        Destino
                    </option>

                </select>
            </div>

            <!-- RUA -->
            <div class="mb-3">

                <label class="form-label">Rua</label>

                <input
                    type="text"
                    name="rua"
                    class="form-control"
                    placeholder="Digite o nome da rua"
                    value="<?php echo htmlspecialchars($rua); ?>"
                    required
                >

            </div>

            <!-- NÚMERO -->
            <div class="mb-3">

                <label class="form-label">Número</label>

                <input
                    type="text"
                    name="numero"
                    class="form-control"
                    placeholder="Digite o número"
                    value="<?php echo htmlspecialchars($numero); ?>"
                    required
                >

            </div>

            <!-- BAIRRO -->
            <div class="mb-3">

                <label class="form-label">Bairro</label>

                <input
                    type="text"
                    name="bairro"
                    class="form-control"
                    placeholder="Digite o bairro"
                    value="<?php echo htmlspecialchars($bairro); ?>"
                    required
                >

            </div>

            <!-- CIDADE -->
            <div class="mb-3">

                <label class="form-label">Cidade</label>

                <input
                    type="text"
                    name="cidade"
                    class="form-control"
                    placeholder="Digite a cidade"
                    value="<?php echo htmlspecialchars($cidade); ?>"
                    required
                >

            </div>

            <!-- CADASTRAR -->
            <button
                type="submit"
                class="btn btn-primary"
            >
                Cadastrar Endereço
            </button>

        </form>
    </div>

    <!-- LISTAGEM DE ENDEREÇOS -->
    <div class="card shadow p-4">

        <h4 class="mb-3">📋 Endereços Cadastrados</h4>

        <div class="table-responsive">

            <table class="table table-bordered table-striped">

                <thead class="table-dark">

                    <tr>
                        <th>ID</th>
                        <th>Tipo</th>
                        <th>Rua</th>
                        <th>Número</th>
                        <th>Bairro</th>
                        <th>Cidade</th>
                        <th>Ação</th>
                    </tr>

                </thead>

                <tbody>

                <?php if ($resultado && $resultado->num_rows > 0): ?>

                    <?php while ($endereco = $resultado->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?php echo (int) $endereco["id"]; ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($endereco["tipo"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($endereco["rua"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($endereco["numero"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($endereco["bairro"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($endereco["cidade"]); ?>
                            </td>

                            <td>

                                <a
                                    href="editar_endereco.php?id=<?php echo (int) $endereco["id"]; ?>"
                                    class="btn btn-warning btn-sm"
                                >
                                    Editar
                                </a>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>

                        <td colspan="7" class="text-center">
                            Nenhum endereço cadastrado.
                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
