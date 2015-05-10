<?php
session_start();

// Verificar se tem permissão para executar
require_once("./utils/verificarPermissao.php");

// Classe de conexão com o banco
require_once("./classes/BancoDeDados.class.php");

// Configurações padrões
include_once("./utils/configuracoes.php");

include_once("./modelos/cabecalho_interno.html");

// Iniciar o objeto do banco de dados
$banco = new BancoDeDados();

$resultados = $banco->obterTodosParticipantes();
?>
<main>
    <?php
    if (count($resultados) > 0) {
        $contador = 0;
        echo '<div><table class="tabela">';
        echo '<thead><tr><th colspan="2">Todos os participantes</th></tr></thead>';
        echo '<thead><tr><th class="titulo">Foto</th><th class="colunaNome titulo">Nome</th></tr></thead>';
        echo '<tbody>';
        foreach ($resultados as $participante) {  //para cada elemento do vetor de resultados...
            $partesArquivo = explode(".", $participante['arquivoFoto']);
            $caminhoImagem = DIRIMAGENS . $participante['login'] . "/" . $partesArquivo[0] . "thumbnail." . $partesArquivo[1];
            if ($contador % 2 == 0) {
                echo '<tr class="linha_interna"><td>';
            } else {
                echo '<tr class="linha_interna linha_impar"><td>';
            }

            echo "<a href=\"perfil.php?usuario=" . $participante['login'] . "\">";
            echo "<figure class=\"mini\" ><img class=\"figura\" width=\"40\" height=\"40\" src=\"" . $caminhoImagem . "\"/></figure></a>";
            echo "</td>";
            echo "<td><a class=\"texto\" href=\"perfil.php?usuario=" . $participante['login'] . "\">" . $participante['nomeCompleto'] . "</a></td></tr>";
            $contador = $contador + 1;
        }
        echo '</table></div>';
    } else {
        echo "<div class=\"mensagemErro\"><label>Não foram encontrados perticipantes com os dados fornecidos.</label></div>";
    }
    ?>
</main>
<?php
// fecha a conexão (os resultados já estão capturados)
$banco->fecharConexao();
include_once("./modelos/rodape.html");
?>