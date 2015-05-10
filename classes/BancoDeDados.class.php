<?php

class BancoDeDados {

    private $conexao; // Conexão com o banco de dados
    private $servidor;
    private $porta;
    private $banco;
    private $usuario;
    private $senha;
    private $options;

    /*
     * Método construtor que inicia a conexão com o banco de dados
     */

    function __construct() {
        $this->servidor = 'localhost';
        $this->porta = 3306;
        $this->banco = "daw_yearbook";
        $this->usuario = "daw";
        $this->senha = "daw2014";
        $this->options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
        $this->conexao = new PDO("mysql:host=$this->servidor;port=$this->porta;dbname=$this->banco", $this->usuario, $this->senha, $this->options
        );
    }

    /*
     * Método que obtém todos os estados
     */

    function obterEstados() {
        $SQLEstados = 'SELECT * FROM estados';

        $operacao = $this->conexao->prepare($SQLEstados);

        $operacao->execute();

        return $operacao->fetchAll();
    }

    /*
     * Método que obtém as cidades de um determinado estado
     */

    function obterCidades($idEstado) {
        $SQLCidades = 'SELECT * FROM cidades WHERE idEstado = ?';

        $operacao = $this->conexao->prepare($SQLCidades);

        $operacao->execute(array($idEstado));

        return $operacao->fetchAll();
    }

    /*
     * Método que obtém todos os participantes
     */

    function obterTodosParticipantes() {
        $SQLSelect = 'SELECT * FROM participantes ';

        $operacao = $this->conexao->prepare($SQLSelect);

        $operacao->execute();

        return $operacao->fetchAll();
    }

    /*
     * Método que obtém participantes pelo nome completo
     */

    function obterParticipantesPeloNomeCompleto($nomeBusca) {
        $SQLSelect = 'SELECT * FROM participantes WHERE nomeCompleto like ?';

        $operacao = $this->conexao->prepare($SQLSelect);

        $operacao->execute(array($nomeBusca));

        return $operacao->fetchAll();
    }

    /*
     * Método que obtém participantes pelo login
     */

    function obterParticipantePeloLogin($login) {
        $SQLSelect = 'SELECT * FROM participantes WHERE login = ?';

        $operacao = $this->conexao->prepare($SQLSelect);

        $operacao->execute(array($login));

        return $operacao->fetchAll();
    }
    
    /*
     * Método que obtém participantes pelo login e senha
     */

    function obterParticipantePeloLoginSenha($login, $senha) {
        $SQLSelect = 'SELECT * FROM participantes LEFT JOIN cidades ON participantes.cidade = cidades.idCidade LEFT JOIN estados ON cidades.idEstado = estados.idEstado WHERE login = ? AND senha = ?';

        $operacao = $this->conexao->prepare($SQLSelect);

        $operacao->execute(array($login, $senha));

        return $operacao->fetchAll();
    }

    /*
     * Método que obtém o participante pelo login com todos os dados
     */

    function obterParticipanteCompletoPeloLogin($login) {
        $SQLLogin = 'SELECT * FROM participantes LEFT JOIN cidades ON participantes.cidade = cidades.idCidade LEFT JOIN estados ON cidades.idEstado = estados.idEstado WHERE login = ?';

        $operacao = $this->conexao->prepare($SQLLogin);

        $operacao->execute(array($login));

        return $operacao->fetchAll();
    }

    /*
     * Método que obtém o login, arquivoFoto e nomeCompleto de até 12 participantes aleatórios
     */
    function obterLoginFotoNomeDeParticipantes() {
        $SQLParticipantes = 'SELECT login, arquivoFoto, nomeCompleto FROM participantes ORDER BY RAND() LIMIT 12';

        $operacao = $this->conexao->prepare($SQLParticipantes);

        $operacao->execute();

        return $operacao->fetchAll();
    }

    /*
     * Método que obtém o arquivoFoto, nomeCompleto e email de um participante
     */
    function obterFNEDeParticipantePeloLogin($login) {
        $SQLParticipante = 'SELECT arquivoFoto, nomeCompleto, email FROM participantes WHERE login = ?';

        $operacao_visitado = $this->conexao->prepare($SQLParticipante);

        $operacao_visitado->execute(array($login));

        return $operacao_visitado->fetchAll();
    }

    /*
     * Método que obtém o login, arquivoFoto e nomeCompleto com login diferente do login e login visitado passados de até 6 participantes aleatórios
     */
    function obterLFNDeParticipantesDiferenteVisitado($login, $loginvisitado) {
        $SQLParticipantes = 'SELECT login, arquivoFoto, nomeCompleto FROM participantes WHERE login <> ? AND login <> ? ORDER BY RAND() LIMIT 6';

        $operacao = $this->conexao->prepare($SQLParticipantes);

        $operacao->execute(array($login, $loginvisitado));

        return $operacao->fetchAll();
    }
    
    /*
     * Método que obtém o login, arquivoFoto e nomeCompleto com login diferente do login passado de até 6 participantes aleatórios
     */
    function obterLFNDeParticipantesDiferente($login) {
        $SQLParticipantes = 'SELECT login, arquivoFoto, nomeCompleto FROM participantes WHERE login <> ? ORDER BY RAND() LIMIT 5';

        $operacao = $this->conexao->prepare($SQLParticipantes);

        $operacao->execute(array($login));

        return $operacao->fetchAll();
    }

    /*
     * Método que obtém participantes pelo login
     */

    function inserirParticipante($login, $senha, $nomecompleto, $nomeArquivo, $idCidade, $email, $descricao) {
        $SQLInsert = 'INSERT INTO participantes VALUES (?,?,?,?,?,?,?)';

        $operacao = $this->conexao->prepare($SQLInsert);

        return $operacao->execute(array($login, $senha, $nomecompleto, $nomeArquivo, $idCidade, $email, $descricao));
    }

    /*
     * Atualizações ------------------------------------------------------------
     */

    function atualizarParticipanteENDC($email, $nomecompleto, $descricao, $idcidade, $login) {
        $SQLAtualizarParticipante = 'UPDATE participantes SET email = ?, nomeCompleto = ?, descricao = ?, cidade = ? WHERE login = ?';

        $operacaoAtualizarParticipantes = $this->conexao->prepare($SQLAtualizarParticipante);

        return $operacaoAtualizarParticipantes->execute(array($email, $nomecompleto, $descricao, $idcidade, $login));
    }

    function atualizarParticipanteLENDC($novologin, $email, $nomecompleto, $descricao, $idcidade, $login) {
        $SQLAtualizarParticipante = 'UPDATE participantes SET login = ?, email = ?, nomeCompleto = ?, descricao = ?, cidade = ? WHERE login = ?';

        $operacaoAtualizarParticipantes = $this->conexao->prepare($SQLAtualizarParticipante);

        return $operacaoAtualizarParticipantes->execute(array($novologin, $email, $nomecompleto, $descricao, $idcidade, $login));
    }

    function atualizarParticipanteFENDC($nomeArquivo, $email, $nomecompleto, $descricao, $idcidade, $login) {
        $SQLAtualizarParticipante = 'UPDATE participantes SET arquivoFoto = ?, email = ?, nomeCompleto = ?, descricao = ?, cidade = ? WHERE login = ?';

        $operacaoAtualizarParticipantes = $this->conexao->prepare($SQLAtualizarParticipante);

        return $operacaoAtualizarParticipantes->execute(array($nomeArquivo, $email, $nomecompleto, $descricao, $idcidade, $login));
    }

    function atualizarParticipanteFLENDC($nomeArquivo, $novologin, $email, $nomecompleto, $descricao, $idcidade, $login) {
        $SQLAtualizarParticipante = 'UPDATE participantes SET arquivoFoto = ?, login = ?, email = ?, nomeCompleto = ?, descricao = ?, cidade = ? WHERE login = ?';

        $operacaoAtualizarParticipantes = $this->conexao->prepare($SQLAtualizarParticipante);

        return $operacaoAtualizarParticipantes->execute(array($nomeArquivo, $novologin, $email, $nomecompleto, $descricao, $idcidade, $login));
    }

    function atualizarParticipanteSENDC($senha, $email, $nomecompleto, $descricao, $idcidade, $login) {
        $SQLAtualizarParticipante = 'UPDATE participantes SET senha = ?, email = ?, nomeCompleto = ?, descricao = ?, cidade = ? WHERE login = ?';

        $operacaoAtualizarParticipantes = $this->conexao->prepare($SQLAtualizarParticipante);

        return $operacaoAtualizarParticipantes->execute(array($senha, $email, $nomecompleto, $descricao, $idcidade, $login));
    }

    function atualizarParticipanteS($senha, $login) {
        $SQLAtualizarParticipante = 'UPDATE participantes SET senha = ? WHERE login = ?';

        $operacaoAtualizarParticipantes = $this->conexao->prepare($SQLAtualizarParticipante);

        return $operacaoAtualizarParticipantes->execute(array($senha, $login));
    }

    function atualizarParticipanteSLENDC($senha, $novologin, $email, $nomecompleto, $descricao, $idcidade, $login) {
        $SQLAtualizarParticipante = 'UPDATE participantes SET senha = ?, login = ?, email = ?, nomeCompleto = ?, descricao = ?, cidade = ? WHERE login = ?';

        $operacaoAtualizarParticipantes = $this->conexao->prepare($SQLAtualizarParticipante);

        return $operacaoAtualizarParticipantes->execute(array($senha, $novologin, $email, $nomecompleto, $descricao, $idcidade, $login));
    }

    function atualizarParticipanteSFENDC($senha, $nomeArquivo, $email, $nomecompleto, $descricao, $idcidade, $login) {
        $SQLAtualizarParticipante = 'UPDATE participantes SET senha = ?, arquivoFoto = ?, email = ?, nomeCompleto = ?, descricao = ?, cidade = ? WHERE login = ?';

        $operacaoAtualizarParticipantes = $this->conexao->prepare($SQLAtualizarParticipante);

        return $operacaoAtualizarParticipantes->execute(array($senha, $nomeArquivo, $email, $nomecompleto, $descricao, $idcidade, $login));
    }

    function atualizarParticipanteSFLENDC($senha, $nomeArquivo, $novologin, $email, $nomecompleto, $descricao, $idcidade, $login) {
        $SQLAtualizarParticipante = 'UPDATE participantes SET senha = ?, arquivoFoto = ?, login = ?, email = ?, nomeCompleto = ?, descricao = ?, cidade = ? WHERE login = ?';

        $operacaoAtualizarParticipantes = $this->conexao->prepare($SQLAtualizarParticipante);

        return $operacaoAtualizarParticipantes->execute(array($senha, $nomeArquivo, $novologin, $email, $nomecompleto, $descricao, $idcidade, $login));
    }

    /*
     * Método que remove um participande
     */

    function removerParticipante($login) {
        $SQLRemoverParticipante = 'DELETE FROM participantes WHERE login = ?';

        $operacaoAtualizarParticipantes = $this->conexao->prepare($SQLRemoverParticipante);

        return $operacaoAtualizarParticipantes->execute(array($login));
    }

    /*
     * Método que fecha a conexão com o banco de dados
     */

    function fecharConexao() {
        $this->conexao = null;
    }

}
