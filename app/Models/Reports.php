<?php

namespace App\Models;

use mysqli;
use PDOStatement;
use PDO;
use PhpParser\Node\Expr\FuncCall;

class Reports
{
    public function __construct()
    {
    }

    public static function getReportsOrientador($id_orientador)
    {
        $conn = Connection::getConnection();

        //get nos Relatórios Ainda não Abertos e nos Abertos
        $query = "SELECT p.nome, e.dataEnvio, e.descricao, r.caminho, av.dataInicio, r.id_relatorio
        FROM avaliacao as av
            inner join relatorio as r
            on av.id_relatorio = r.id_relatorio
                inner join periodo as pe
                on r.id_periodo = pe.id_periodo
                inner join elaboracao as e
                on r.id_relatorio = e.id_relatorio
                    inner join orientando as a
                    on e.id_orientando = a.id_orientando
                        inner join pessoa as p
                        on a.id_pessoa = p.id_pessoa
                        inner join orientador as pr
                        on a.id_orientador = pr.id_orientador
        WHERE pr.id_orientador = {$id_orientador} AND av.dataAval IS NULL
        AND av.id_aval_pai IS NULL AND pe._aberto = 1
        ORDER BY av.dataInicio;";

        $report = "";
        $result = $conn->query($query);

        if ($result->rowCount()) {
            $report =  "<div class='main-container'>
                            <h1>Relatórios Pendentes</h1> 
                            <div class='relatorios-pendentes'> 
                                <ul>";
            while ($row = $result->fetch(PDO::FETCH_ASSOC))
                if ($row['dataInicio'] == null)
                    $report = $report . "<li class='modalbutton unread'>
                                            <i class='fas fa-envelope'></i>
                                                <p>
                                                     Relatório PPgSI - " . $row['nome'] . "
                                                </p>
                                                <p id='id_rel' style='display:none'>" . $row['id_relatorio'] . "</p>
                                                <p class='date'>
                                                        " . date_format(date_create($row['dataEnvio']), 'd/m/Y') . "
                                                </p>
                                        </li>" . "\n";
                else {
                    $report = $report . "<li class='modalbutton read'>
                                            <i class='fas fa-envelope-open'></i>
                                                <p>
                                                    Relatório PPgSI - " . $row['nome'] . "    
                                                </p>
                                                <p id='id_rel' style='display:none'>" . $row['id_relatorio'] . "</p>
                                                <p class='date'>
                                                     " . date_format(date_create($row['dataEnvio']), 'd/m/Y') . "
                                                </p>
                                         </li>" . "\n";
                }

            $report =  $report . "</ul> </div> </div>";
        }
        return $report;
    }

    public static function getReportsCCP($id_ccp)
    {
        $conn = Connection::getConnection();

        //get nos Relatórios CCP Ainda não Abertos e nos Abertos
        $query = " SELECT r.id_relatorio, pa.nome as 'nome_aluno', ppr.nome as 'nome_prof', e.dataEnvio, e.descricao as 'desc_aluno', r.caminho, 
                  av.descricao as 'desc_prof', av.dataInicio as 'dataInicio_prof', av.dataAval, avop.descricao as 'aval_prof',
                  cpp.dataInicio as 'dataInicio_cpp'
              FROM avaliacao as av
                  inner join avalOpcao as avop
                  on av.id_avalOpcao = avop.id_avalOpcao
                  inner join relatorio as r
                  on av.id_relatorio = r.id_relatorio
                      inner join periodo as pe
                      on r.id_periodo = pe.id_periodo
                      inner join elaboracao as e
                      on r.id_relatorio = e.id_relatorio
                          inner join orientando as a
                          on e.id_orientando = a.id_orientando
                              inner join pessoa as pa
                              on a.id_pessoa = pa.id_pessoa
                  inner join orientador as pr
                  on av.id_orientador = pr.id_orientador
                      inner join pessoa as ppr
                      on pr.id_pessoa = ppr.id_pessoa
                  inner join avaliacao as cpp
                  on av.id_aval = cpp.id_aval_pai
              WHERE av.id_avalOpcao != 4 AND ((cpp.dataInicio IS NULL) OR cpp.id_orientador = {$id_ccp}) 
              AND cpp.dataAval IS NULL AND pe._aberto = 1
              ORDER BY cpp.dataInicio;";

        $report = "";
        $result = $conn->query($query);

        if ($result->rowCount()) {
            $report =  "<div class='main-container'>
                            <h1>Relatórios Pendentes</h1> 
                            <div class='relatorios-pendentes'> 
                                <ul>";
            while ($row = $result->fetch(PDO::FETCH_ASSOC))
                if ($row['dataInicio_cpp'] == null)
                    $report = $report . "<li class='modalbutton unread'>
                                            <i class='fas fa-envelope'></i>
                                                <p>
                                                     Relatório PPgSI - " . $row['nome_aluno'] . "
                                                </p>
                                                <p id='id_rel' style='display:none'>" . $row['id_relatorio'] . "</p>
                                                <p class='date'>
                                                        " . date_format(date_create($row['dataAval']), 'd/m/Y') . "
                                                </p>
                                        </li>" . "\n";
                else {
                    $report = $report . "<li class='modalbutton read'>
                                            <i class='fas fa-envelope-open'></i>
                                                <p>
                                                    Relatório PPgSI - " . $row['nome_aluno'] . "    
                                                </p>
                                                <p id='id_rel' style='display:none'>" . $row['id_relatorio'] . "</p>
                                                <p class='date'>
                                                     " . date_format(date_create($row['dataAval']), 'd/m/Y') . "
                                                </p>
                                         </li>" . "\n";
                }

            $report =  $report . "</ul> </div> </div>";
        }
        return $report;
    }

    public static function getHistoryOrientador($id_orientador)
    {
        $conn = Connection::getConnection();

        //get no Histórico do Orientador
        $query = "SELECT p.nome, e.dataEnvio, r.id_relatorio
        FROM avaliacao as av
            inner join relatorio as r
            on av.id_relatorio = r.id_relatorio
                inner join elaboracao as e
                on r.id_relatorio = e.id_relatorio
                    inner join orientando as a
                    on e.id_orientando = a.id_orientando
                        inner join pessoa as p
                        on a.id_pessoa = p.id_pessoa
        WHERE av.id_orientador = {$id_orientador} AND av.id_aval_pai IS NULL
        ORDER BY p.nome, av.dataInicio, av.dataAval;";

        $report = "";
        $result = $conn->query($query);

        if ($result->rowCount()) {
            $report =  "<div class='main-container'>
                            <h1>Histórico de Relatórios</h1> 
                            <div class='historico-relatorios'> 
                                <ul>";
            while ($row = $result->fetch(PDO::FETCH_ASSOC))
            {
                    $report = $report . "<li class='modalbutton'>
                                                <i class='fas fa-file'></i>
                                                <p>
                                                    Relatório PPgSI - " . $row['nome'] . "    
                                                </p>
                                                <p id='id_rel' style='display:none'>" . $row['id_relatorio'] . "</p>
                                                <p class='date'>
                                                     " . date_format(date_create($row['dataEnvio']), 'd/m/Y') . "
                                                </p>
                                         </li>" . "\n";
            }

            $report =  $report . "</ul> </div> </div>";
        }
        return $report;
    }



    public static function saveReport($id_aval, $nota, $comment_prof){
        $conn = Connection::getConnection();

        $query = "CALL salvarAvaliacaoOrientador({$id_aval}, {$nota}, '{$comment_prof}');";
        
        $conn->query($query);
    }

    public static function saveReportCCP($id_aval, $nota, $comment_prof, $id_ccp){
        $conn = Connection::getConnection();

        $query = "CALL salvarAvaliacaoCPP({$id_aval}, {$nota}, '{$comment_prof}',  {$id_ccp} );";
        
        $conn->query($query);
        
    }

    public static function sendReport($id_aval, $nota, $comment_prof){
        $conn = Connection::getConnection();

        $query = "CALL enviarAvaliacaoOrientador({$id_aval}, {$nota}, '{$comment_prof}' );";
        
        $conn->query($query);
    }

    public static function sendReportCCP($id_aval, $nota, $comment_prof, $id_ccp){
        $conn = Connection::getConnection();

        $query = "CALL enviarAvaliacaoCPP({$id_aval}, {$nota}, '{$comment_prof}', {$id_ccp});";
        
        $conn->query($query);
    }
}
