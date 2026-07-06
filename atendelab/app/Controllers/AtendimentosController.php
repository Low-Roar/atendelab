<?php

require_once __DIR__ . '/../Middleware/auth.php';

class AtendimentosController
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

    public function listar(): void
    {
        $consulta = "
            SELECT
                a.id,
                p.nome AS pessoa_nome,
                t.nome AS tipo_nome,
                u.nome AS responsavel_nome,
                a.descricao,
                a.status,
                a.data_atendimento,
                a.horario_atendimento,
                a.observacao_final
            FROM atendimentos a
                INNER JOIN pessoas p
                    ON p.id = a.pessoa_id
                INNER JOIN tipos_atendimentos t
                    ON t.id = a.tipo_atendimento_id
                INNER JOIN usuarios u
                    ON u.id = a.usuario_id
            ORDER BY a.id DESC
        ";

        $resultado = $this->pdo->query($consulta);

        $this->json(
            $resultado->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public function buscar(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if ($id === false || $id === null) {
            $this->json(['erro' => 'ID inválido.'], 400);
            return;
        }

        $sql = "
            SELECT
                a.*,
                p.nome AS pessoa_nome,
                t.nome AS tipo_nome,
                u.nome AS responsavel_nome
            FROM atendimentos a
                INNER JOIN pessoas p
                    ON p.id = a.pessoa_id
                INNER JOIN tipos_atendimentos t
                    ON t.id = a.tipo_atendimento_id
                INNER JOIN usuarios u
                    ON u.id = a.usuario_id
            WHERE a.id = :id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $id
        ]);

        $registro = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($registro === false) {
            $this->json(['erro' => 'Atendimento não encontrado.'], 404);
            return;
        }

        $this->json($registro);
    }

    public function criar(): void
    {
        $usuario = usuarioAtual();

        $dados = [
            'pessoa_id' => filter_var($_POST['pessoa_id'] ?? null, FILTER_VALIDATE_INT),
            'tipo_id' => filter_var($_POST['tipo_atendimento_id'] ?? null, FILTER_VALIDATE_INT),
            'usuario_id' => $usuario['id'] ?? null,
            'descricao' => trim($_POST['descricao'] ?? ''),
            'status' => $_POST['status'] ?? 'aberto',
            'data_atendimento' => $_POST['data_atendimento'] ?? '',
            'horario_atendimento' => $_POST['horario_atendimento'] ?? ''
        ];

        if (
            !$dados['pessoa_id'] ||
            !$dados['tipo_id'] ||
            !$dados['usuario_id'] ||
            $dados['descricao'] === '' ||
            $dados['data_atendimento'] === '' ||
            $dados['horario_atendimento'] === ''
        ) {
            $this->json([
                'erro' => 'Preencha os campos obrigatórios.'
            ], 422);
            return;
        }

        if (!in_array($dados['status'], ['aberto', 'em_andamento'], true)) {
            $this->json([
                'erro' => 'Status inicial inválido.'
            ], 422);
            return;
        }

        $sql = "
            INSERT INTO atendimentos
            (
                pessoa_id,
                tipo_atendimento_id,
                usuario_id,
                descricao,
                status,
                data_atendimento,
                horario_atendimento
            )
            VALUES
            (
                :pessoa_id,
                :tipo_id,
                :usuario_id,
                :descricao,
                :status,
                :data_atendimento,
                :horario_atendimento
            )
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($dados);

        $this->json([
            'mensagem' => 'Atendimento registrado com sucesso.'
        ], 201);
    }

    public function AlterarStatus(): void
    {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        $status = $_POST['status'] ?? '';
        $observacao = trim($_POST['observacao_final'] ?? '');

        $statusPermitidos = [
            'aberto',
            'em_andamento',
            'concluido'
        ];

        if (!$id || !in_array($status, $statusPermitidos, true)) {
            $this->json([
                'erro' => 'Dados inválidos.'
            ], 422);
            return;
        }

        if ($status === 'concluido' && $observacao === '') {
            $this->json([
                'erro' => 'Informe a observação final para concluir.'
            ], 422);
            return;
        }

        $sql = "
            UPDATE atendimentos
            SET
                status = :status,
                observacao_final = :observacao
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'status' => $status,
            'observacao' => $observacao !== '' ? $observacao : null
        ]);

        $this->json([
            'mensagem' => 'Status atualizado com sucesso.'
        ]);
    }
}