DROP DATABASE IF EXISTS atendelab;

CREATE DATABASE atendelab
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

USE atendelab;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    perfil ENUM('admin', 'atendente') DEFAULT 'atendente',
    status ENUM('ativo', 'inativo') DEFAULT 'ativo',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE pessoas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    documento VARCHAR(30) NOT NULL UNIQUE,
    telefone VARCHAR(20),
    email VARCHAR(150) NOT NULL,
    curso VARCHAR(120),
    periodo VARCHAR(20),
    observacoes TEXT,
    status ENUM('ativo', 'inativo') DEFAULT 'ativo',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE tipos_atendimentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    status ENUM('ativo', 'inativo') DEFAULT 'ativo',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE atendimentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pessoa_id INT NOT NULL,
    tipo_atendimento_id INT NOT NULL,
    usuario_id INT NOT NULL,
    descricao TEXT NOT NULL,
    status ENUM('aberto', 'em_andamento', 'concluido')
        DEFAULT 'aberto',
    data_atendimento DATE NOT NULL,
    horario_atendimento TIME NOT NULL,
    observacao_final TEXT,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_atendimentos_pessoa
        FOREIGN KEY (pessoa_id) REFERENCES pessoas(id),
    CONSTRAINT fk_atendimentos_tipo
        FOREIGN KEY (tipo_atendimento_id)
        REFERENCES tipos_atendimentos(id),
    CONSTRAINT fk_atendimentos_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

INSERT INTO usuarios (nome, email, senha, perfil, status)
VALUES (
    'Administrador',
    'admin@atendelab.com',
    '$2y$10$JP2kU2BAMZ3TZcuxTsw4e1D/lka8EocYHzvyo0ZmCNcWDQz3RuVc',
    'admin',
    'ativo'
);

INSERT INTO tipos_atendimentos (nome, descricao, status) VALUES
('Dúvida acadêmica', 'Dúvidas sobre disciplinas, conteúdos e atividades.', 'ativo'),
('Orientação de atividade', 'Orientações sobre trabalhos, TCC e projetos.', 'ativo'),
('Suporte técnico', 'Problemas com sistemas, equipamentos e acessos.', 'ativo'),
('Matrícula e documentação', 'Solicitações de matrícula, declarações e históricos.', 'ativo'),
('Acesso ao laboratório', 'Liberação de uso e agendamento de laboratórios.', 'ativo');

INSERT INTO pessoas
(nome, documento, telefone, email, curso, periodo, status)
VALUES
('João da Silva', '123.456.789-00', '(47) 99999-0001',
'joao.silva@exemplo.com', 'Engenharia de Software', '5º', 'ativo'),
('Ana Carolina', '987.654.321-00', '(47) 99999-0002',
'ana.carolina@exemplo.com', 'Sistemas de Informação', '7º', 'ativo');