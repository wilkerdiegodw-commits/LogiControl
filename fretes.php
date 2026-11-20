
<?php
require_once "conexao.php";

$mensagem = "";
$tipoMensagem = "";

// CADASTRAR FRETE
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $produto_id = (int) ($_POST["produto_id"] ?? 0);
    $origem_id = (int) ($_POST["origem_id"] ?? 0);
    $destino_id = (int) ($_POST["destino_id"] ?? 0);
    $transportador_id = (int) ($_POST["transportador_id"] ?? 0);
    $data_frete = $_POST["data_frete"] ?? "";

    if (
        $produto_id > 0 &&
        $origem_id > 0 &&
        $destino_id > 0 &&
        $origem_id !== $destino_id &&
        $transportador_id > 0 &&
        $data_frete !== ""
    ) {

        $sql = "INSERT INTO fretes
                (produto_id, endereco_origem_id,
                 endereco_destino_id, transportador_id,
                 data_frete)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $conexao->prepare($sql);

        if ($stmt) {

            $stmt->bind_param(
                "iiiis",
                $produto_id,
                $origem_id,
                $destino_id,
                $transportador_id,
                $data_frete
            );

            if ($stmt->execute()) {
                $mensagem = "Frete cadastrado com sucesso!";
                $tipoMensagem = "success";
            } else {
                $mensagem = "Erro ao cadastrar o frete.";
                $tipoMensagem = "danger";
            }

            $stmt->close();

        } else {
            $mensagem = "Erro ao preparar o cadastro.";
            $tipoMensagem = "danger";
        }

    } else {
        $mensagem = "Preencha todos os campos e escolha endereços diferentes.";
        $tipoMensagem = "warning";
    }
}

// BUSCAR PRODUTOS
$produtos = $conexao->query(
    "SELECT id, nome FROM produtos ORDER BY nome"
);

// BUSCAR ORIGENS
$origens = $conexao->query(
    "SELECT id, rua, numero, bairro, cidade
     FROM enderecos
     WHERE tipo = 'Origem'
     ORDER BY cidade, rua"
);

// BUSCAR DESTINOS
$destinos = $conexao->query(
    "SELECT id, rua, numero, bairro, cidade
     FROM enderecos
     WHERE tipo = 'Destino'
     ORDER BY cidade, rua"
);

// BUSCAR TRANSPORTADORES
$transportadores = $conexao->query(
    "SELECT id, nome
     FROM transportadores
     ORDER BY nome"
);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Cadastro de Fretes - LogiControl</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="container mt-5 mb-5">

    <div class="card shadow p-4">

        <h2 class="mb-3">🚚 Cadastro de Fretes</h2>

        <p class="text-muted">
            Gere um frete relacionando produto, origem,
            destino e transportador.
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

            <!-- PRODUTO -->
            <div class="mb-3">

                <label class="form-label">Produto</label>

                <select
                    name="produto_id"
                    class="form-select"
                    required
                >
                    <option value="">Selecione um produto</option>

                    <?php if ($produtos): ?>
                        <?php while ($produto = $produtos->fetch_assoc()): ?>

                            <option value="<?php echo (int) $produto["id"]; ?>">
                                <?php echo htmlspecialchars($produto["nome"]); ?>
                            </option>

                        <?php endwhile; ?>
                    <?php endif; ?>

                </select>
            </div>

            <!-- ORIGEM -->
            <div class="mb-3">

                <label class="form-label">
                    Endereço de Origem
                </label>

                <select
                    name="origem_id"
                    class="form-select"
                    required
                >
                    <option value="">Selecione a origem</option>

                    <?php if ($origens): ?>
                        <?php while ($origem = $origens->fetch_assoc()): ?>

                            <option value="<?php echo (int) $origem["id"]; ?>">
                                <?php
                                echo htmlspecialchars(
                                    $origem["rua"] . ", " .
                                    $origem["numero"] . " - " .
                                    $origem["bairro"] . " - " .
                                    $origem["cidade"]
                                );
                                ?>
                            </option>

                        <?php endwhile; ?>
                    <?php endif; ?>

                </select>
            </div>

            <!-- DESTINO -->
            <div class="mb-3">

                <label class="form-label">
                    Endereço de Destino
                </label>

                <select
                    name="destino_id"
                    class="form-select"
                    required
                >
                    <option value="">Selecione o destino</option>

                    <?php if ($destinos): ?>
                        <?php while ($destino = $destinos->fetch_assoc()): ?>

                            <option value="<?php echo (int) $destino["id"]; ?>">
                                <?php
                                echo htmlspecialchars(
                                    $destino["rua"] . ", " .
                                    $destino["numero"] . " - " .
                                    $destino["bairro"] . " - " .
                                    $destino["cidade"]
                                );
                                ?>
                            </option>

                        <?php endwhile; ?>
                    <?php endif; ?>

                </select>
            </div>

            <!-- TRANSPORTADOR -->
            <div class="mb-3">

                <label class="form-label">
                    Transportador
                </label>

                <select
                    name="transportador_id"
                    class="form-select"
                    required
                >
                    <option value="">
                        Selecione um transportador
                    </option>

                    <?php if ($transportadores): ?>
                        <?php while ($transportador = $transportadores->fetch_assoc()): ?>

                            <option value="<?php echo (int) $transportador["id"]; ?>">
                                <?php echo htmlspecialchars($transportador["nome"]); ?>
                            </option>

                        <?php endwhile; ?>
                    <?php endif; ?>

                </select>
            </div>

            <!-- DATA -->
            <div class="mb-3">

                <label class="form-label">
                    Data do Frete
                </label>

                <input
                    type="date"
                    name="data_frete"
                    class="form-control"
                    value="<?php echo htmlspecialchars(
                        $_POST["data_frete"] ?? date("Y-m-d")
                    ); ?>"
                    required
                >

            </div>

            <button type="submit" class="btn btn-primary">
                Gerar Frete
            </button>

            <a
                href="/logicontrol/listar_fretes.php"
                class="btn btn-outline-primary"
            >
                Consultar Fretes
            </a>

        </form>

    </div>

</div>

</body>
</html>
