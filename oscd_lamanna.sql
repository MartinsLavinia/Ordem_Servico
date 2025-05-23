-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 23/05/2025 às 15:41
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `oscd_lamanna`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `andamentoos`
--

CREATE TABLE `andamentoos` (
  `id` int(11) NOT NULL,
  `OS` int(11) DEFAULT NULL,
  `Situacao` varchar(255) DEFAULT NULL,
  `Descricao` text DEFAULT NULL,
  `DataAtualizacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `andamentoos`
--

INSERT INTO `andamentoos` (`id`, `OS`, `Situacao`, `Descricao`, `DataAtualizacao`) VALUES
(1, 8, 'Em andamento', 'Serviço iniciado pelo colaborador', '2025-05-19 13:02:29'),
(2, 19, 'Em andamento', 'Serviço iniciado pelo colaborador', '2025-05-23 11:44:30'),
(3, 19, 'quase acabando', 'teste2', '2025-05-23 11:45:12'),
(4, 19, 'quase acabando', 'teste2', '2025-05-23 11:48:44'),
(5, 19, 'quase acabando', 'teste2', '2025-05-23 12:11:18'),
(6, 20, 'Em andamento', 'Serviço iniciado pelo colaborador', '2025-05-23 12:12:36'),
(7, 21, 'Em andamento', 'Serviço iniciado pelo colaborador', '2025-05-23 12:34:28');

-- --------------------------------------------------------

--
-- Estrutura para tabela `cargo`
--

CREATE TABLE `cargo` (
  `CodigoCargo` int(11) NOT NULL,
  `NomeCargo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cargo`
--

INSERT INTO `cargo` (`CodigoCargo`, `NomeCargo`) VALUES
(1, 'Técnico de Suporte'),
(2, 'Técnico em Redes'),
(3, 'Técnico de Informática'),
(4, 'Técnico de Sistemas'),
(5, 'Técnico de Manutenção'),
(6, 'Técnico de Software'),
(7, 'Técnico em Telecomunicações'),
(8, 'Técnico de Hardware'),
(9, 'Técnico em Reparação de Celulares'),
(10, 'Técnico de Reparo em Dispositivos Móveis'),
(11, 'Técnico em Reparo de Equipamentos Móveis');

-- --------------------------------------------------------

--
-- Estrutura para tabela `cliente`
--

CREATE TABLE `cliente` (
  `CodigoCliente` int(11) NOT NULL,
  `NomeCliente` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cliente`
--

INSERT INTO `cliente` (`CodigoCliente`, `NomeCliente`, `email`, `senha`) VALUES
(126, 'Amanda', 'amandamesquita@gmail.com', '$2y$10$vsOul51DYBo3Iqt9n5bgYuDhcYLUQ2fYroV9d5NVahVOliX0FBQ1u'),
(127, 'Sofia', 'sofia@gmail.com', '$2y$10$7egjVpqtjV921.HGBheSg.YXf0gPxs7Oox3pA6.zXiGjc2plFUb1u');

-- --------------------------------------------------------

--
-- Estrutura para tabela `colaborador`
--

CREATE TABLE `colaborador` (
  `CodigoColaborador` int(11) NOT NULL,
  `NomeColaborador` varchar(255) NOT NULL,
  `CodigoCargo` int(11) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `colaborador`
--

INSERT INTO `colaborador` (`CodigoColaborador`, `NomeColaborador`, `CodigoCargo`, `email`, `senha`) VALUES
(3, 'amanda', 4, 'amandamesquita@gmail.com', '$2y$10$AgLvCB9GTu6OUrhjKb/Nr.EwXkligyukHXHmjEGu9dA9Be651fnTm');

-- --------------------------------------------------------

--
-- Estrutura para tabela `os`
--

CREATE TABLE `os` (
  `OS` int(11) NOT NULL,
  `NumeroOS` varchar(50) NOT NULL,
  `Data` date NOT NULL,
  `Equipamento` varchar(255) NOT NULL,
  `Defeito` text NOT NULL,
  `ValorDefeito` decimal(10,2) DEFAULT NULL,
  `ValorTotal` decimal(10,2) NOT NULL,
  `CodigoColaborador` int(11) DEFAULT NULL,
  `CodigoCliente` int(11) DEFAULT NULL,
  `status` enum('ativo','inativo','pendente') DEFAULT 'ativo',
  `Servico` varchar(50) DEFAULT NULL,
  `ValorServico` decimal(10,2) DEFAULT NULL,
  `SituacaoAtual` varchar(255) DEFAULT NULL,
  `DescricaoAtual` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `os`
--

INSERT INTO `os` (`OS`, `NumeroOS`, `Data`, `Equipamento`, `Defeito`, `ValorDefeito`, `ValorTotal`, `CodigoColaborador`, `CodigoCliente`, `status`, `Servico`, `ValorServico`, `SituacaoAtual`, `DescricaoAtual`) VALUES
(19, 'OS20250523001', '2025-05-23', 'Computador', 'Travando', NULL, 85.00, 3, 126, 'ativo', 'Outros', NULL, 'quase acabando', 'teste2'),
(20, 'OS20250523002', '2025-05-23', 'Computador', 'Fios soltos', NULL, 100.00, 3, 126, 'ativo', 'Reparo', NULL, NULL, NULL),
(21, 'OS20250523003', '2025-05-23', 'Computador', 'Tela quebrada', NULL, 150.00, 3, 126, 'ativo', 'Troca de peça', NULL, NULL, NULL),
(22, 'OS20250523004', '2025-05-23', 'Computador', 'Instalação de software', NULL, 200.00, NULL, 127, 'ativo', '0', NULL, NULL, NULL),
(23, 'OS20250523005', '2025-05-23', 'Computador', 'Instalação de software', NULL, 200.00, NULL, 127, 'ativo', '0', NULL, NULL, NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `andamentoos`
--
ALTER TABLE `andamentoos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `OS` (`OS`);

--
-- Índices de tabela `cargo`
--
ALTER TABLE `cargo`
  ADD PRIMARY KEY (`CodigoCargo`);

--
-- Índices de tabela `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`CodigoCliente`);

--
-- Índices de tabela `colaborador`
--
ALTER TABLE `colaborador`
  ADD PRIMARY KEY (`CodigoColaborador`),
  ADD KEY `CodigoCargo` (`CodigoCargo`);

--
-- Índices de tabela `os`
--
ALTER TABLE `os`
  ADD PRIMARY KEY (`OS`),
  ADD UNIQUE KEY `NumeroOS` (`NumeroOS`),
  ADD KEY `CodigoColaborador` (`CodigoColaborador`),
  ADD KEY `CodigoCliente` (`CodigoCliente`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `andamentoos`
--
ALTER TABLE `andamentoos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `cargo`
--
ALTER TABLE `cargo`
  MODIFY `CodigoCargo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `cliente`
--
ALTER TABLE `cliente`
  MODIFY `CodigoCliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

--
-- AUTO_INCREMENT de tabela `colaborador`
--
ALTER TABLE `colaborador`
  MODIFY `CodigoColaborador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `os`
--
ALTER TABLE `os`
  MODIFY `OS` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
