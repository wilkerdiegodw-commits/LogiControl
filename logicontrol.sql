-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 20/11/2026 às 03:44
-- Versão do servidor: 10.11.16-MariaDB
-- Versão do PHP: 8.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `logicontrol`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `enderecos`
--

CREATE TABLE `enderecos` (
  `id` int(11) NOT NULL,
  `tipo` enum('Origem','Destino') NOT NULL,
  `rua` varchar(255) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `bairro` varchar(150) NOT NULL,
  `cidade` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `enderecos`
--

INSERT INTO `enderecos` (`id`, `tipo`, `rua`, `numero`, `bairro`, `cidade`) VALUES
(1, 'Origem', 'diego', '12', 'wilker', 'cascavel'),
(2, 'Destino', 'pavao', '15', 'diego', 'goiania');

-- --------------------------------------------------------

--
-- Estrutura para tabela `fretes`
--

CREATE TABLE `fretes` (
  `id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `endereco_origem_id` int(11) NOT NULL,
  `endereco_destino_id` int(11) NOT NULL,
  `transportador_id` int(11) NOT NULL,
  `data_frete` date NOT NULL,
  `criado_em` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `fretes`
--

INSERT INTO `fretes` (`id`, `produto_id`, `endereco_origem_id`, `endereco_destino_id`, `transportador_id`, `data_frete`, `criado_em`) VALUES
(1, 3, 1, 2, 1, '2026-11-28', '2026-11-20 01:10:51');

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `descricao` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `descricao`) VALUES
(3, 'caixa', 'papelao'),
(4, 'caixa', 'lo'),
(5, 'caixa', 'lo'),
(6, 'old', 'mo');

-- --------------------------------------------------------

--
-- Estrutura para tabela `transportadores`
--

CREATE TABLE `transportadores` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `documento` varchar(30) DEFAULT NULL,
  `telefone` varchar(30) NOT NULL,
  `placa_veiculo` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `transportadores`
--

INSERT INTO `transportadores` (`id`, `nome`, `documento`, `telefone`, `placa_veiculo`) VALUES
(1, 'notebook', '10921063911', '42999736687', 'hdasbijjh'),
(2, 'notebook', '10921063911', '42999736687', 'hdasbijjh'),
(3, 'palhaco', '10921063911', '42999736687', 'hdasbijjh');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `enderecos`
--
ALTER TABLE `enderecos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `fretes`
--
ALTER TABLE `fretes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produto_id` (`produto_id`),
  ADD KEY `endereco_origem_id` (`endereco_origem_id`),
  ADD KEY `endereco_destino_id` (`endereco_destino_id`),
  ADD KEY `transportador_id` (`transportador_id`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `transportadores`
--
ALTER TABLE `transportadores`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `enderecos`
--
ALTER TABLE `enderecos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `fretes`
--
ALTER TABLE `fretes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `transportadores`
--
ALTER TABLE `transportadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `fretes`
--
ALTER TABLE `fretes`
  ADD CONSTRAINT `fretes_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`),
  ADD CONSTRAINT `fretes_ibfk_2` FOREIGN KEY (`endereco_origem_id`) REFERENCES `enderecos` (`id`),
  ADD CONSTRAINT `fretes_ibfk_3` FOREIGN KEY (`endereco_destino_id`) REFERENCES `enderecos` (`id`),
  ADD CONSTRAINT `fretes_ibfk_4` FOREIGN KEY (`transportador_id`) REFERENCES `transportadores` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
