
<?php
require_once "conexao.php";

$erro = "";
$dataSelecionada = $_GET["data"] ?? "";

// VERIFICAR SE O USUÁRIO SOLICITOU O DOWNLOAD
if (isset($_GET["baixar"])) {

    $data = $_GET["data"] ?? "";

    $dataValida = DateTime::createFromFormat("!Y-m-d", $data);

    if (
        !$dataValida ||
        $dataValida->format("Y-m-d") !== $data
    ) {
        die("Data inválida. Volte e selecione uma data correta.");
    }

    // BUSCAR OS FRETES DA DATA ESCOLHIDA
    $sql = "SELECT
                f.id,
                p.nome AS produto,
                eo.rua AS origem_rua,
                eo.numero AS origem_numero,
                eo.cidade AS origem_cidade,
                ed.rua AS destino_rua,
                ed.numero AS destino_numero,
                ed.cidade AS destino_cidade,
                t.nome AS transportador,
                f.data_frete
            FROM fretes f
            INNER JOIN produtos p
                ON f.produto_id = p.id
            INNER JOIN enderecos eo
                ON f.endereco_origem_id = eo.id
            INNER JOIN enderecos ed
                ON f.endereco_destino_id = ed.id
            INNER JOIN transportadores t
                ON f.transportador_id = t.id
            WHERE f.data_frete = ?
            ORDER BY f.id";

    $stmt = $conexao->prepare($sql);

    $stmt->bind_param("s", $data);
    $stmt->execute();

    $resultado = $stmt->get_result();

    // CONFIGURAR DOWNLOAD CSV
    $nomeArquivo = "relatorio_fretes_" . $data . ".csv";

    header("Content-Type: text/csv; charset=UTF-8");

    header(
        "Content-Disposition: attachment; filename=\"$nomeArquivo\""
    );

    // ABRIR ARQUIVO DE SAÍDA
    $arquivo = fopen("php://output", "w");

    // BOM PARA ACENTUAÇÃO NO EXCEL
    fwrite($arquivo, "\xEF\xBB\xBF");

    // CABEÇALHO DO RELATÓRIO
    fputcsv($arquivo, [
        "ID",
        "Produto",
        "Rua de Origem",
        "Número de Origem",
        "Cidade de Origem",
        "Rua de Destino",
        "Número de Destino",
        "Cidade de Destino",
        "Transportador",
        "Data do Frete"
    ], ";", '"', "");

    // PREENCHER RELATÓRIO
    while ($frete = $resultado->fetch_assoc()) {

        fputcsv($arquivo, [
            $frete["id"],
            $frete["produto"],
            $frete["origem_rua"],
            $frete["origem_numero"],
            $frete["origem_cidade"],
            $frete["destino_rua"],
            $frete["destino_numero"],
            $frete["destino_cidade"],
            $frete["transportador"],
            date(
                "d/m/Y",
                strtotime($frete["data_frete"])
            )
        ], ";", '"', "");
    }

    fclose($arquivo);
    $stmt->close();

    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Relatório de Fretes - LogiControl</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="container mt-5 mb-5">

    <div class="card shadow">

        <div class="card-body p-4">

            <h2 class="mb-3">
                📋 Relatório de Fretes
            </h2>

            <p class="text-muted">
                Escolha uma data para baixar o relatório
                dos fretes cadastrados.
            </p>

            <!-- BOTÃO VOLTAR CORRIGIDO -->
            <div class="mb-4">

                <a
                    href="/logicontrol/"
                    class="btn btn-secondary"
                >
                    🏠 Voltar ao início
                </a>

            </div>

            <!-- FORMULÁRIO DO RELATÓRIO -->
            <form method="GET" action="">

                <div class="mb-3">

                    <label class="form-label">
                        Data dos Fretes
                    </label>

                    <input
                        type="date"
                        name="data"
                        class="form-control"
                        value="<?php echo htmlspecialchars($dataSelecionada); ?>"
                        required
                    >

                </div>

                <button
                    type="submit"
                    name="baixar"
                    value="1"
                    class="btn btn-success"
                >
                    📥 Baixar Relatório
                </button>

            </form>

        </div>

    </div>

</div>

</body>
</html>
