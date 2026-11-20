
<?php
require_once "conexao.php";

// BUSCAR TODOS OS FRETES CADASTRADOS
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
        ORDER BY f.data_frete DESC";

$resultado = $conexao->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Fretes - LogiControl</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="container mt-5 mb-5">

    <!-- CABEÇALHO -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>🚚 Fretes Cadastrados</h2>

        <a
            href="/logicontrol/fretes.php"
            class="btn btn-primary"
        >
            + Novo Frete
        </a>

    </div>

    <!-- BOTÃO VOLTAR AO INÍCIO CORRIGIDO -->
    <div class="mb-4">

        <a
            href="/logicontrol/"
            class="btn btn-secondary"
        >
            🏠 Voltar ao início
        </a>

    </div>

    <!-- LISTAGEM DE FRETES -->
    <div class="card shadow-sm">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-striped table-bordered align-middle">

                    <thead class="table-dark">

                        <tr>
                            <th>ID</th>
                            <th>Produto</th>
                            <th>Origem</th>
                            <th>Destino</th>
                            <th>Transportador</th>
                            <th>Data</th>
                            <th>Ação</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php if ($resultado && $resultado->num_rows > 0): ?>

                            <?php while ($frete = $resultado->fetch_assoc()): ?>

                                <tr>

                                    <td>
                                        <?php echo (int) $frete["id"]; ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($frete["produto"]); ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $frete["origem_rua"] . ", " .
                                            $frete["origem_numero"] . " - " .
                                            $frete["origem_cidade"]
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $frete["destino_rua"] . ", " .
                                            $frete["destino_numero"] . " - " .
                                            $frete["destino_cidade"]
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($frete["transportador"]); ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo date(
                                            "d/m/Y",
                                            strtotime($frete["data_frete"])
                                        );
                                        ?>
                                    </td>

                                    <td>

                                        <a
                                            href="editar_frete.php?id=<?php echo (int) $frete["id"]; ?>"
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
                                    Nenhum frete cadastrado.
                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

</body>
</html>
