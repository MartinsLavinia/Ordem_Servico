-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 12/05/2025 às 19:58
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
  `DataAtualizacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `NumeroOS` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `andamentoos`
--

INSERT INTO `andamentoos` (`id`, `OS`, `Situacao`, `Descricao`, `DataAtualizacao`, `NumeroOS`) VALUES
(1, 6, 'Em andamento', 'Serviço iniciado pelo colaborador', '2025-05-12 13:51:52', NULL),
(2, 6, 'Em andamento', 'Serviço iniciado pelo colaborador', '2025-05-12 13:52:47', NULL),
(3, 4, 'Em andamento', 'Serviço iniciado pelo colaborador', '2025-05-12 13:55:56', NULL),
(4, 4, 'Em andamento', 'Serviço iniciado pelo colaborador', '2025-05-12 14:03:44', NULL),
(5, 4, 'Em andamento', 'Serviço iniciado pelo colaborador', '2025-05-12 14:20:36', NULL),
(6, 6, 'Em andamento', 'Serviço iniciado pelo colaborador', '2025-05-12 14:20:49', NULL),
(7, 5, 'Em andamento', 'Serviço iniciado pelo colaborador', '2025-05-12 14:20:58', NULL),
(8, 5, 'Em andamento', 'Serviço iniciado pelo colaborador', '2025-05-12 14:30:26', NULL),
(9, 5, 'Em andamento', 'Serviço iniciado pelo colaborador', '2025-05-12 14:41:43', NULL),
(10, 5, 'Em andamento', 'Serviço iniciado pelo colaborador', '2025-05-12 14:54:01', NULL),
(11, 5, 'Em andamento', 'Serviço iniciado pelo colaborador', '2025-05-12 14:54:39', NULL),
(12, 5, 'Em andamento', 'serviço preste a terminar', '2025-05-12 14:59:53', NULL),
(13, 5, 'Em andamento', 'serviço preste a terminar', '2025-05-12 15:01:32', NULL),
(14, 4, 'Em andamento', 'Peça chegou quase finalizando', '2025-05-12 15:02:46', NULL),
(15, 7, 'Em andamento', 'Serviço iniciado pelo colaborador', '2025-05-12 16:47:18', NULL);

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
(123, 'amanda', '', ''),
(124, '', 'nicolas.hrsantos2007@gmail.com', '$2y$10$Z459RvJ.3BZ5ungMjp2OkuUwp.Ye3xGX.DuvrNENgTHILk/p9SvZC');

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
(1, 'Amanda', 3, 'amandamesquita818@gmail.com', '$2y$10$E0loiNSDuJa9XGrwQuOdiusg3dKUGwxW14eZKlolJF1c5FHgmbfdm'),
(2, 'Nicolas', 5, 'nicolas.hrsantos2007@gmail.com', '$2y$10$sm.3M1w6F6emwALLgXB37uQ2vtsWUxG5OeuhcUQic/oPjAXqMooRC');

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
  `Servico` text NOT NULL,
  `ValorTotal` decimal(10,2) NOT NULL,
  `CodigoColaborador` int(11) DEFAULT NULL,
  `CodigoCliente` int(11) DEFAULT NULL,
  `Status` enum('Em andamento','Finalizada') DEFAULT 'Em andamento'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `os`
--

INSERT INTO `os` (`OS`, `NumeroOS`, `Data`, `Equipamento`, `Defeito`, `Servico`, `ValorTotal`, `CodigoColaborador`, `CodigoCliente`, `Status`) VALUES
(4, 'OS20250428004', '2025-04-28', 'Celular', 'Tela quebrada', '0', 0.00, 1, 123, 'Finalizada'),
(5, 'OS20250428005', '2025-04-28', 'Celular', 'Tela quebrada', '0', 0.00, 1, 123, 'Finalizada'),
(6, 'OS20250428006', '2025-04-08', 'Notebook', 'travou', '0', 100.00, 1, 123, 'Em andamento'),
(7, 'OS20250428007', '2025-04-21', 'Computador', 'Tela quebrada', '0', 500.00, 2, 123, 'Em andamento');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `cargo`
--
ALTER TABLE `cargo`
  MODIFY `CodigoCargo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `cliente`
--
ALTER TABLE `cliente`
  MODIFY `CodigoCliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;

--
-- AUTO_INCREMENT de tabela `colaborador`
--
ALTER TABLE `colaborador`
  MODIFY `CodigoColaborador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `os`
--
ALTER TABLE `os`
  MODIFY `OS` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `andamentoos`
--
ALTER TABLE `andamentoos`
  ADD CONSTRAINT `andamentoos_ibfk_1` FOREIGN KEY (`OS`) REFERENCES `os` (`OS`);

--
-- Restrições para tabelas `colaborador`
--
ALTER TABLE `colaborador`
  ADD CONSTRAINT `colaborador_ibfk_1` FOREIGN KEY (`CodigoCargo`) REFERENCES `cargo` (`CodigoCargo`);

--
-- Restrições para tabelas `os`
--
ALTER TABLE `os`
  ADD CONSTRAINT `os_ibfk_1` FOREIGN KEY (`CodigoColaborador`) REFERENCES `colaborador` (`CodigoColaborador`),
  ADD CONSTRAINT `os_ibfk_2` FOREIGN KEY (`CodigoCliente`) REFERENCES `cliente` (`CodigoCliente`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
