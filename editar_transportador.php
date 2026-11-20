
<?php
require_once "conexao.php";

$mensagem = "";

// VERIFICAR ID
if (
    !isset($_GET["id"]) ||
    !ctype_digit((string) $_GET["id"]) ||
    (int) $_GET["id"] <= 0
) {
    die("Transportador não informado.");
}

$id = (int) $_GET["id"];

// BUSCAR TRANSPORTADOR
$stmt = $conexao->prepare(
    "SELECT id, nome, documento, telefone, placa_veiculo
     FROM transportadores
     WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();
$transportador = $resultado->fetch_assoc();

$stmt->close();

if (!$transportador) {
    die("Transportador não encontrado.");
}

// ATUALIZAR TRANSPORTADOR
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = trim($_POST["nome"] ?? "");
    $documento = trim($_POST["documento"] ?? "");
    $telefone = trim($_POST["telefone"] ?? "");
    $placa_veiculo = trim($_POST["placa_veiculo"] ?? "");

    $transportador["nome"] = $nome;
    $transportador["documento"] = $documento;
    $transportador["telefone"] = $telefone;
    $transportador["placa_veiculo"] = $placa_veiculo;

    if ($nome !== "" && $telefone !== "") {

        $stmt = $conexao->prepare(
            "UPDATE transportadores
             SET nome = ?,
                 documento = ?,
                 telefone = ?,
                 placa_veiculo = ?
             WHERE id = ?"
        );

        if ($stmt) {
            $stmt->bind_param(
                "ssssi",
                $nome,
                $documento,
                $telefone,
                $placa_veiculo,
                $id
            );

            if ($stmt->execute()) {
                $stmt->close();

                header("Location: transportadores.php");
                exit;
            } else {
                $mensagem = "Erro ao atualizar transportador.";
            }

            $stmt->close();
        } else {
            $mensagem = "Erro ao preparar atualização.";
        }

    } else {
        $mensagem = "Preencha o nome e o telefone.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Editar Transportador - LogiControl</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="container mt-5 mb-5">

    <div class="card shadow p-4">

        <h2 class="mb-3">✏️ Editar Transportador</h2>

        <p class="text-secondary">
            Altere as informações do transportador.
        </p>

        <?php if ($mensagem !== ""): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">
                <label class="form-label">
                    Nome do Transportador
                </label>

                <input
                    type="text"
                    name="nome"
                    class="form-control"
                    value="<?php echo htmlspecialchars($transportador["nome"]); ?>"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label">CPF ou CNPJ</label>

                <input
                    type="text"
                    name="documento"
                    class="form-control"
                    value="<?php echo htmlspecialchars($transportador["documento"] ?? ""); ?>"
                >
            </div>

            <div class="mb-3">
                <label class="form-label">Telefone</label>

                <input
                    type="text"
                    name="telefone"
                    class="form-control"
                    value="<?php echo htmlspecialchars($transportador["telefone"]); ?>"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label">Placa do Veículo</label>

                <input
                    type="text"
                    name="placa_veiculo"
                    class="form-control"
                    value="<?php echo htmlspecialchars($transportador["placa_veiculo"] ?? ""); ?>"
                >
            </div>

            <button type="submit" class="btn btn-primary">
                Salvar Alterações
            </button>

            <a href="transportadores.php" class="btn btn-secondary">
                Cancelar
            </a>

        </form>
    </div>
</div>

</body>
</html>
