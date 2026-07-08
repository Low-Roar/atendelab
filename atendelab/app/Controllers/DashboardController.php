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

            $sqlPessoas = "SELECT COUNT(*) FROM pessoas";
            $totalPessoas = (int) $this->pdo->query($sqlPessoas)->fetchColumn();

            $sqlTipos = "SELECT COUNT(*) FROM tipos_atendimentos";
            $totalTipos = (int) $this->pdo->query($sqlTipos)->fetchColumn();

            $sqlAtendimentos = "SELECT COUNT(*) FROM atendimentos";
            $totalAtendimentos = (int) $this->pdo->query($sqlAtendimentos)->fetchColumn();

            $sqlRecentes = "
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

            $stmt = $this->pdo->query($sqlRecentes);

            $recentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $resultado = [
                'indicadores' => [
                    'total_pessoas' => $totalPessoas,
                    'total_tipos' => $totalTipos,
                    'total_atendimentos' => $totalAtendimentos
                ],
                'atendimentos_recentes' => $recentes
            ];

            $this->json($resultado);

        } catch (PDOException $e) {

            $this->json([
                'erro' => 'Erro ao carregar o dashboard.'
            ], 500);

        }
    }
}