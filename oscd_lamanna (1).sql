
CREATE TABLE `andamentoos` (
  `id` int(11) NOT NULL,
  `OS` int(11) DEFAULT NULL,
  `Situacao` varchar(255) DEFAULT NULL,
  `Descricao` text DEFAULT NULL,
  `DataAtualizacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `cargo` (
  `CodigoCargo` int(11) NOT NULL,
  `NomeCargo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


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

CREATE TABLE `cliente` (
  `CodigoCliente` int(11) NOT NULL,
  `NomeCliente` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `cliente` (`CodigoCliente`, `NomeCliente`, `email`, `senha`) VALUES
(123, 'amanda', '', '');

CREATE TABLE `colaborador` (
  `CodigoColaborador` int(11) NOT NULL,
  `NomeColaborador` varchar(255) NOT NULL,
  `CodigoCargo` int(11) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `os` (
  `OS` int(11) NOT NULL,
  `NumeroOS` varchar(50) NOT NULL,
  `Data` date NOT NULL,
  `Equipamento` varchar(255) NOT NULL,
  `Defeito` text NOT NULL,
  `Servico` text NOT NULL,
  `ValorTotal` decimal(10,2) NOT NULL,
  `CodigoColaborador` int(11) DEFAULT NULL,
  `CodigoCliente` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `os` (`OS`, `NumeroOS`, `Data`, `Equipamento`, `Defeito`, `Servico`, `ValorTotal`, `CodigoColaborador`, `CodigoCliente`) VALUES
(3, 'OS20250428003', '2025-04-28', 'Celular', 'Tela quebrada', '0', 0.00, NULL, 123),
(4, 'OS20250428004', '2025-04-28', 'Celular', 'Tela quebrada', '0', 0.00, NULL, 123),
(5, 'OS20250428005', '2025-04-28', 'Celular', 'Tela quebrada', '0', 0.00, NULL, 123),
(6, 'OS20250428006', '2025-04-08', 'Notebook', 'travou', '0', 100.00, NULL, 123),
(7, 'OS20250428007', '2025-04-21', 'Computador', 'Tela quebrada', '0', 500.00, NULL, 123);

ALTER TABLE `andamentoos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `OS` (`OS`);

ALTER TABLE `cargo`
  ADD PRIMARY KEY (`CodigoCargo`);

ALTER TABLE `cliente`
  ADD PRIMARY KEY (`CodigoCliente`);

ALTER TABLE `colaborador`
  ADD PRIMARY KEY (`CodigoColaborador`),
  ADD KEY `CodigoCargo` (`CodigoCargo`);

ALTER TABLE `os`
  ADD PRIMARY KEY (`OS`),
  ADD UNIQUE KEY `NumeroOS` (`NumeroOS`),
  ADD KEY `CodigoColaborador` (`CodigoColaborador`),
  ADD KEY `CodigoCliente` (`CodigoCliente`);

ALTER TABLE `andamentoos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `cargo`
  MODIFY `CodigoCargo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

ALTER TABLE `cliente`
  MODIFY `CodigoCliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

ALTER TABLE `colaborador`
  MODIFY `CodigoColaborador` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `os`
  MODIFY `OS` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `andamentoos`
  ADD CONSTRAINT `andamentoos_ibfk_1` FOREIGN KEY (`OS`) REFERENCES `os` (`OS`);

ALTER TABLE `colaborador`
  ADD CONSTRAINT `colaborador_ibfk_1` FOREIGN KEY (`CodigoCargo`) REFERENCES `cargo` (`CodigoCargo`);

ALTER TABLE `os`
  ADD CONSTRAINT `os_ibfk_1` FOREIGN KEY (`CodigoColaborador`) REFERENCES `colaborador` (`CodigoColaborador`),
  ADD CONSTRAINT `os_ibfk_2` FOREIGN KEY (`CodigoCliente`) REFERENCES `cliente` (`CodigoCliente`);
COMMIT;
