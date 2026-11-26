CREATE DATABASE IF NOT EXISTS helpdesk_facil
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE helpdesk_facil;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS chamados;
DROP TABLE IF EXISTS categorias;
DROP TABLE IF EXISTS usuarios;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE usuarios (
  id_usuario INT PRIMARY KEY AUTO_INCREMENT,
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  senha VARCHAR(100) NOT NULL,
  permissao ENUM ('Administrador', 'Usuario') NOT NULL,
  telefone VARCHAR(20)
) ENGINE=InnoDB;

CREATE TABLE categorias (
  id_categoria INT PRIMARY KEY AUTO_INCREMENT,
  nome VARCHAR(50) NOT NULL,
  descricao VARCHAR(150)
) ENGINE=InnoDB;

CREATE TABLE chamados (
  id_chamado INT PRIMARY KEY AUTO_INCREMENT,
  titulo VARCHAR(100) NOT NULL,
  descricao TEXT NOT NULL,
  prioridade ENUM ('Alta', 'Media', 'Baixa') NOT NULL,
  status ENUM ('Aberto', 'Em andamento', 'Resolvido') DEFAULT 'Aberto',
  data_abertura DATETIME DEFAULT CURRENT_TIMESTAMP,
  id_usuario INT NOT NULL,
  id_categoria INT NOT NULL,
  id_criador INT NULL,
  CONSTRAINT fk_chamados_usuarios FOREIGN KEY (id_usuario)
    REFERENCES usuarios (id_usuario),
  CONSTRAINT fk_chamados_categorias FOREIGN KEY (id_categoria)
    REFERENCES categorias (id_categoria),
  CONSTRAINT fk_chamados_criador FOREIGN KEY (id_criador)
    REFERENCES usuarios (id_usuario) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO usuarios (nome, email, senha, permissao, telefone) VALUES
('Carlos Silva', 'carlos@email.com', '12345678', 'Administrador', '(48) 99999-1001'),
('Ana Souza', 'ana@email.com', '12345678', 'Usuario', '(48) 99999-1002'),
('Pedro Lima', 'pedro@email.com', '12345678', 'Usuario', '(48) 99999-1003');

INSERT INTO categorias (nome, descricao) VALUES
('Hardware', 'Problemas com computadores e equipamentos'),
('Rede', 'Problemas de internet e conexao'),
('Software', 'Instalacao e erros de programas'),
('Acesso', 'Contas, senhas e permissoes'),
('Outro', 'Outras solicitacoes de suporte');

INSERT INTO chamados (titulo, descricao, prioridade, status, id_usuario, id_categoria, id_criador) VALUES
('Computador nao liga', 'Equipamento nao inicia apos pressionar o botao.', 'Alta', 'Aberto', 1, 1, 1),
('Internet indisponivel', 'Computador sem acesso a rede.', 'Media', 'Em andamento', 2, 2, 2),
('Instalacao de programa', 'Solicitacao de instalacao de um editor de texto.', 'Baixa', 'Resolvido', 3, 3, 3);