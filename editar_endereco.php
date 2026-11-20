
<?php
require_once "conexao.php";

$mensagem = "";

// Verificar o ID do endereço
if (
    !isset($_GET["id"]) ||
    !ctype_digit((string) $_GET["id"]) ||
    (int) $_GET["id"] <= 0
) {
    die("Endereço não informado.");
}

$id = (int) $_GET["id"];

// Buscar endereço cadastrado
$sql = $conexao->prepare(
    "SELECT id, tipo, rua, numero, bairro, cidade
     FROM enderecos
     WHERE id = ?"
);

$sql->bind_param("i", $id);
$sql->execute();

$resultado = $sql->get_result();
$endereco = $resultado->fetch_assoc();

$sql->close();

if (!$endereco) {
    die("Endereço não encontrado.");
}

// Atualizar endereço
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $tipo = $_POST["tipo"] ?? "";
    $rua = trim($_POST["rua"] ?? "");
    $numero = trim($_POST["numero"] ?? "");
    $bairro = trim($_POST["bairro"] ?? "");
    $cidade = trim($_POST["cidade"] ?? "");

    // Manter os valores digitados no formulário
    $endereco["tipo"] = $tipo;
    $endereco["rua"] = $rua;
    $endereco["numero"] = $numero;
    $endereco["bairro"] = $bairro;
    $endereco["cidade"] = $cidade;

    if (
        in_array($tipo, ["Origem", "Destino"], true) &&
        $rua !== "" &&
        $numero !== "" &&
        $bairro !== "" &&
        $cidade !== ""
    ) {

        $sql = $conexao->prepare(
            "UPDATE enderecos
             SET tipo = ?,
                 rua = ?,
                 numero = ?,
                 bairro = ?,
                 cidade = ?
             WHERE id = ?"
        );

        if ($sql) {
            $sql->bind_param(
                "sssssi",
                $tipo,
                $rua,
                $numero,
                $bairro,
                $cidade,
                $id
            );

            if ($sql->execute()) {
                $sql->close();

                header("Location: enderecos.php");
                exit;
            } else {
                $mensagem = "Erro ao atualizar endereço.";
            }

            $sql->close();
        } else {
            $mensagem = "Erro ao preparar atualização.";
        }

    } else {
        $mensagem = "Preencha todos os campos corretamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Editar Endereço - LogiControl</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="container mt-5 mb-5">

    <div class="card shadow p-4">

        <h2 class="mb-3">✏️ Editar Endereço</h2>

        <p class="text-secondary">
            Altere as informações do endereço cadastrado.
        </p>

        <?php if ($mensagem !== ""): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <!-- Tipo -->
            <div class="mb-3">
                <label class="form-label">
                    Tipo de endereço
                </label>

                <select
                    name="tipo"
                    class="form-select"
                    required
                >

                    <option value="Origem"
                        <?php
                        echo $endereco["tipo"] === "Origem"
                            ? "selected"
                            : "";
                        ?>>
                        Origem
                    </option>

                    <option value="Destino"
                        <?php
                        echo $endereco["tipo"] === "Destino"
                            ? "selected"
                            : "";
                        ?>>
                        Destino
                    </option>

                </select>
            </div>

            <!-- Rua -->
            <div class="mb-3">
                <label class="form-label">Rua</label>

                <input
                    type="text"
                    name="rua"
                    class="form-control"
                    value="<?php echo htmlspecialchars($endereco["rua"]); ?>"
                    required
                >
            </div>

            <!-- Número -->
            <div class="mb-3">
                <label class="form-label">Número</label>

                <input
                    type="text"
                    name="numero"
                    class="form-control"
                    value="<?php echo htmlspecialchars($endereco["numero"]); ?>"
                    required
                >
            </div>

            <!-- Bairro -->
            <div class="mb-3">
                <label class="form-label">Bairro</label>

                <input
                    type="text"
                    name="bairro"
                    class="form-control"
                    value="<?php echo htmlspecialchars($endereco["bairro"]); ?>"
                    required
                >
            </div>

            <!-- Cidade -->
            <div class="mb-3">
                <label class="form-label">Cidade</label>

                <input
                    type="text"
                    name="cidade"
                    class="form-control"
                    value="<?php echo htmlspecialchars($endereco["cidade"]); ?>"
                    required
                >
            </div>

            <!-- Botões -->
            <button
                type="submit"
                class="btn btn-primary"
            >
                Salvar Alterações
            </button>

            <a
                href="enderecos.php"
                class="btn btn-secondary"
            >
                Cancelar
            </a>

        </form>

    </div>

</div>

</body>
</html>
