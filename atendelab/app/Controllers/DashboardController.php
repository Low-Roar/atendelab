<?php

class DashboardController
{
    private PDO $pdo;

    public function __construct()
    {
        require __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    private function json(array $dados, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    }

    public function resumo(): void
    {
        try {

            $consultas = [
                'total_pessoas' => 'SELECT COUNT(*) FROM pessoas',
                'total_tipos' => 'SELECT COUNT(*) FROM tipos_atendimentos',
                'total_atendimentos' => 'SELECT COUNT(*) FROM atendimentos'
            ];

            $indicadores = [];

            foreach ($consultas as $indice => $sql) {
                $indicadores[$indice] = (int) $this->pdo
                    ->query($sql)
                    ->fetchColumn();
            }

            $consultaRecentes = "
                SELECT
                    a.id,
                    p.nome AS pessoa,
                    t.nome AS tipo,
                    u.nome AS responsavel,
                    a.status,
                    a.data_atendimento,
                    a.horario_atendimento
                FROM atendimentos a
                    INNER JOIN pessoas p
                        ON p.id = a.pessoa_id
                    INNER JOIN tipos_atendimentos t
                        ON t.id = a.tipo_atendimento_id
                    INNER JOIN usuarios u
                        ON u.id = a.usuario_id
                ORDER BY
                    a.data_atendimento DESC,
                    a.horario_atendimento DESC,
                    a.id DESC
                LIMIT 5
            ";

            $atendimentos = $this->pdo
                ->query($consultaRecentes)
                ->fetchAll(PDO::FETCH_ASSOC);

            $resposta = [
                'indicadores' => $indicadores,
                'atendimentos_recentes' => $atendimentos
            ];

            $this->json($resposta);

        } catch (PDOException $erro) {

            $this->json(
                [
                    'erro' => 'Erro ao carregar o dashboard.'
                ],
                500
            );

        }
    }
}