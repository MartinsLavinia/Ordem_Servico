-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 16/05/2025 às 16:28
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

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
(28, 5, 'Em andamento', 'Serviço iniciado pelo colaborador', '2025-05-16 12:46:15'),
(29, 5, 'Em andamento', 'Serviço iniciado pelo colaborador', '2025-05-16 12:46:23'),
(30, 5, 'Em andamento', 'Serviço iniciado pelo colaborador', '2025-05-16 12:46:23'),
(31, 5, 'Em andamento', 'Serviço iniciado pelo colaborador', '2025-05-16 12:46:23'),
(32, 5, 'Em andamento', 'Serviço iniciado pelo colaborador', '2025-05-16 14:03:24');

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
(124, 'Lavinia', 'lavinia@gmail.com', '$2y$10$x26RMsgZzn.6KQyIxx7LxOTRZ0lEhpEWbKFuJRrYsZLFn05jlsNJy');

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
(1, 'Lavinia Adm', 4, 'lavinia@gmail.com', '$2y$10$prvRQUW4WVtTUwzhQJ/muu2sMQuR5LtRoNBLrNqOG/zHwWquQTQ42');

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
  `Status` varchar(50) DEFAULT 'Pendente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `os`
--

INSERT INTO `os` (`OS`, `NumeroOS`, `Data`, `Equipamento`, `Defeito`, `Servico`, `ValorTotal`, `CodigoColaborador`, `CodigoCliente`, `Status`) VALUES
(5, 'OS20250428005', '2025-04-28', 'Celular', 'Tela quebrada', '0', 0.00, 1, 123, 'Pendente'),
(6, 'OS20250428006', '2025-04-08', 'Notebook', 'travou', '0', 100.00, NULL, 123, 'Pendente'),
(7, 'OS20250428007', '2025-04-21', 'Computador', 'Tela quebrada', '0', 500.00, NULL, 123, 'Pendente'),
(8, 'OS20250516001', '2025-05-16', 'TV', 'Lento', '0', 400.00, NULL, 124, 'Pendente');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

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
  MODIFY `CodigoColaborador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `os`
--
ALTER TABLE `os`
  MODIFY `OS` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
