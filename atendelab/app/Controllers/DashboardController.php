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

            $totalPessoas = (int) $this->pdo
                ->query('SELECT COUNT(*) FROM pessoas')
                ->fetchColumn();

            $totalTipos = (int) $this->pdo
                ->query('SELECT COUNT(*) FROM tipos_atendimentos')
                ->fetchColumn();

            $totalAtendimentos = (int) $this->pdo
                ->query('SELECT COUNT(*) FROM atendimentos')
                ->fetchColumn();

            $sql = "
                SELECT
                    a.id,
                    p.nome AS pessoa,
                    t.nome AS tipo,
                    u.nome AS responsavel,
                    a.status,
                    a.data_atendimento,
                    a.horario_atendimento
                FROM atendimentos a
                INNER JOIN pessoas p ON p.id = a.pessoa_id
                INNER JOIN tipos_atendimentos t ON t.id = a.tipo_atendimento_id
                INNER JOIN usuarios u ON u.id = a.usuario_id
                ORDER BY a.data_atendimento DESC, a.horario_atendimento DESC, a.id DESC
                LIMIT 5
            ";

            $recentes = $this->pdo
                ->query($sql)
                ->fetchAll(PDO::FETCH_ASSOC);

            $this->json([
                'indicadores' => [
                    'total_pessoas' => $totalPessoas,
                    'total_tipos' => $totalTipos,
                    'total_atendimentos' => $totalAtendimentos,
                ],
                'atendimentos_recentes' => $recentes
            ]);

        } catch (PDOException $e) {

            $this->json([
                'erro' => 'Erro ao carregar o dashboard.'
            ], 500);

        }
    }
}