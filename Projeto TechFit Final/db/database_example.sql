-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: localhost    Database: techfit
-- ------------------------------------------------------
-- Server version	8.0.43

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `techfit`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `techfit` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `techfit`;

--
-- Table structure for table `agendamento`
--

DROP TABLE IF EXISTS `agendamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `agendamento` (
  `id_agendamento` int NOT NULL AUTO_INCREMENT,
  `data_agendamento` datetime NOT NULL,
  `status` enum('agendado','cancelado','espera','presente','ausente') DEFAULT NULL,
  `id_aula` int NOT NULL,
  `id_aluno` int NOT NULL,
  PRIMARY KEY (`id_agendamento`),
  KEY `idx_agendamento_aluno` (`id_aluno`),
  KEY `idx_agendamento_aula` (`id_aula`),
  KEY `idx_agendamento_status` (`status`),
  KEY `idx_agendamento_data` (`data_agendamento`),
  CONSTRAINT `agendamento_ibfk_1` FOREIGN KEY (`id_aula`) REFERENCES `aulas` (`id_aula`) ON DELETE CASCADE,
  CONSTRAINT `agendamento_ibfk_2` FOREIGN KEY (`id_aluno`) REFERENCES `alunos` (`id_aluno`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `alunos`
--

DROP TABLE IF EXISTS `alunos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alunos` (
  `id_aluno` int NOT NULL AUTO_INCREMENT,
  `genero` varchar(20) NOT NULL,
  `endereco` text NOT NULL,
  `telefone` varchar(19) NOT NULL,
  `codigo_acesso` varchar(100) DEFAULT NULL,
  `id_usuario` int NOT NULL,
  PRIMARY KEY (`id_aluno`),
  UNIQUE KEY `codigo_acesso` (`codigo_acesso`),
  KEY `idx_alunos_usuario` (`id_usuario`),
  KEY `idx_alunos_telefone` (`telefone`),
  KEY `idx_alunos_codigo_acesso` (`codigo_acesso`),
  CONSTRAINT `alunos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aulas`
--

DROP TABLE IF EXISTS `aulas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aulas` (
  `id_aula` int NOT NULL AUTO_INCREMENT,
  `nome_aula` varchar(255) NOT NULL,
  `dia_aula` datetime NOT NULL,
  `quantidade_pessoas` int NOT NULL,
  `descricao` text NOT NULL,
  `id_funcionario` int NOT NULL,
  `id_modalidade` int NOT NULL,
  `id_filial` int NOT NULL,
  PRIMARY KEY (`id_aula`),
  KEY `idx_aulas_funcionario` (`id_funcionario`),
  KEY `idx_aulas_modalidade` (`id_modalidade`),
  KEY `idx_aulas_filial` (`id_filial`),
  KEY `idx_aulas_data` (`dia_aula`),
  CONSTRAINT `aulas_ibfk_1` FOREIGN KEY (`id_funcionario`) REFERENCES `funcionarios` (`id_funcionario`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `aulas_ibfk_2` FOREIGN KEY (`id_modalidade`) REFERENCES `modalidades` (`id_modalidade`) ON DELETE RESTRICT,
  CONSTRAINT `aulas_ibfk_3` FOREIGN KEY (`id_filial`) REFERENCES `filiais` (`id_filial`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aulas_aluno`
--

DROP TABLE IF EXISTS `aulas_aluno`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aulas_aluno` (
  `id_aula` int NOT NULL,
  `id_aluno` int NOT NULL,
  PRIMARY KEY (`id_aula`,`id_aluno`),
  KEY `idx_aulas_aluno_aluno` (`id_aluno`),
  CONSTRAINT `aulas_aluno_ibfk_1` FOREIGN KEY (`id_aula`) REFERENCES `aulas` (`id_aula`) ON DELETE CASCADE,
  CONSTRAINT `aulas_aluno_ibfk_2` FOREIGN KEY (`id_aluno`) REFERENCES `alunos` (`id_aluno`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `avaliacoes`
--

DROP TABLE IF EXISTS `avaliacoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `avaliacoes` (
  `id_avaliacao` int NOT NULL AUTO_INCREMENT,
  `comentarios` varchar(255) NOT NULL,
  `nota` decimal(4,2) NOT NULL,
  `id_aluno` int NOT NULL,
  `id_funcionario` int DEFAULT NULL,
  PRIMARY KEY (`id_avaliacao`),
  KEY `idx_avaliacoes_aluno` (`id_aluno`),
  KEY `idx_avaliacoes_funcionario` (`id_funcionario`),
  CONSTRAINT `avaliacoes_ibfk_1` FOREIGN KEY (`id_aluno`) REFERENCES `alunos` (`id_aluno`) ON DELETE CASCADE,
  CONSTRAINT `avaliacoes_ibfk_2` FOREIGN KEY (`id_funcionario`) REFERENCES `funcionarios` (`id_funcionario`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `avisos`
--

DROP TABLE IF EXISTS `avisos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `avisos` (
  `id_alerta` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(100) NOT NULL,
  `tipo` enum('Comunicado','Promocao','Evento','Manutencao','MudancaHorario','Novidade','DicaSaude','AvisoSeguranca') NOT NULL,
  `conteudo` text NOT NULL,
  `expira` date NOT NULL,
  `data_criacao` date NOT NULL,
  `anexo_path` varchar(255) DEFAULT NULL,
  `id_funcionario` int DEFAULT NULL,
  PRIMARY KEY (`id_alerta`),
  KEY `idx_avisos_funcionario` (`id_funcionario`),
  KEY `idx_avisos_tipo` (`tipo`),
  KEY `idx_avisos_expira` (`expira`),
  CONSTRAINT `avisos_ibfk_1` FOREIGN KEY (`id_funcionario`) REFERENCES `funcionarios` (`id_funcionario`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `checkin`
--

DROP TABLE IF EXISTS `checkin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `checkin` (
  `id_checkin` int NOT NULL AUTO_INCREMENT,
  `data_checkin` datetime NOT NULL,
  `id_filial` int NOT NULL,
  `id_aluno` int NOT NULL,
  PRIMARY KEY (`id_checkin`),
  KEY `idx_checkin_filial` (`id_filial`),
  KEY `idx_checkin_aluno` (`id_aluno`),
  KEY `idx_checkin_data` (`data_checkin`),
  CONSTRAINT `checkin_ibfk_1` FOREIGN KEY (`id_filial`) REFERENCES `filiais` (`id_filial`) ON DELETE RESTRICT,
  CONSTRAINT `checkin_ibfk_2` FOREIGN KEY (`id_aluno`) REFERENCES `alunos` (`id_aluno`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `estoque`
--

DROP TABLE IF EXISTS `estoque`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estoque` (
  `id_estoque` int NOT NULL AUTO_INCREMENT,
  `quantidade` int NOT NULL,
  `tipo_produto` varchar(100) NOT NULL,
  PRIMARY KEY (`id_estoque`),
  KEY `idx_estoque_tipo` (`tipo_produto`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `filiais`
--

DROP TABLE IF EXISTS `filiais`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `filiais` (
  `id_filial` int NOT NULL AUTO_INCREMENT,
  `nome_filial` varchar(100) NOT NULL,
  `endereco` text NOT NULL,
  `telefone` varchar(16) NOT NULL,
  PRIMARY KEY (`id_filial`),
  UNIQUE KEY `nome_filial` (`nome_filial`),
  UNIQUE KEY `idx_filiais_nome` (`nome_filial`),
  KEY `idx_filiais_telefone` (`telefone`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `funcionarios`
--

DROP TABLE IF EXISTS `funcionarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `funcionarios` (
  `id_funcionario` int NOT NULL AUTO_INCREMENT,
  `salario` decimal(8,2) NOT NULL,
  `carga_horaria` int NOT NULL,
  `cargo` varchar(50) NOT NULL,
  `id_usuario` int NOT NULL,
  PRIMARY KEY (`id_funcionario`),
  KEY `idx_funcionarios_usuario` (`id_usuario`),
  KEY `idx_funcionarios_cargo` (`cargo`),
  CONSTRAINT `funcionarios_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mensagens`
--

DROP TABLE IF EXISTS `mensagens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mensagens` (
  `id_mensagem` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(100) NOT NULL,
  `corpo` text NOT NULL,
  `data_envio` datetime NOT NULL,
  `data_exclusao` date DEFAULT NULL,
  `apagado` tinyint(1) DEFAULT '0',
  `id_destinatario` int NOT NULL,
  `id_remetente` int NOT NULL,
  PRIMARY KEY (`id_mensagem`),
  KEY `idx_mensagens_destinatario` (`id_destinatario`),
  KEY `idx_mensagens_remetente` (`id_remetente`),
  KEY `idx_mensagens_envio` (`data_envio`),
  CONSTRAINT `mensagens_ibfk_1` FOREIGN KEY (`id_destinatario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  CONSTRAINT `mensagens_ibfk_2` FOREIGN KEY (`id_remetente`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `modalidades`
--

DROP TABLE IF EXISTS `modalidades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modalidades` (
  `id_modalidade` int NOT NULL AUTO_INCREMENT,
  `nome_modalidade` varchar(100) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  PRIMARY KEY (`id_modalidade`),
  UNIQUE KEY `nome_modalidade` (`nome_modalidade`),
  UNIQUE KEY `idx_modalidades_nome` (`nome_modalidade`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pagamentos`
--

DROP TABLE IF EXISTS `pagamentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pagamentos` (
  `id_pagamento` int NOT NULL AUTO_INCREMENT,
  `status` varchar(12) NOT NULL,
  `data_pagamento` datetime NOT NULL,
  `valor` decimal(9,2) NOT NULL,
  `metodo_pagamento` varchar(100) NOT NULL,
  `id_aluno` int NOT NULL,
  PRIMARY KEY (`id_pagamento`),
  KEY `idx_pagamentos_aluno` (`id_aluno`),
  KEY `idx_pagamentos_status` (`status`),
  KEY `idx_pagamentos_data` (`data_pagamento`),
  CONSTRAINT `pagamentos_ibfk_1` FOREIGN KEY (`id_aluno`) REFERENCES `alunos` (`id_aluno`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `planos`
--

DROP TABLE IF EXISTS `planos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `planos` (
  `id_plano` int NOT NULL AUTO_INCREMENT,
  `nome_plano` varchar(100) NOT NULL,
  `descricao_plano` varchar(255) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `duracao` int NOT NULL,
  PRIMARY KEY (`id_plano`),
  UNIQUE KEY `nome_plano` (`nome_plano`),
  UNIQUE KEY `idx_planos_nome` (`nome_plano`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `planos_aluno`
--

DROP TABLE IF EXISTS `planos_aluno`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `planos_aluno` (
  `id_aluno` int NOT NULL,
  `id_plano` int NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL,
  `status` enum('ativo','suspenso','cancelado') NOT NULL DEFAULT 'ativo',
  PRIMARY KEY (`id_aluno`,`id_plano`),
  KEY `idx_planos_aluno_aluno` (`id_aluno`),
  KEY `idx_planos_aluno_plano` (`id_plano`),
  KEY `idx_planos_aluno_status` (`status`),
  CONSTRAINT `planos_aluno_ibfk_1` FOREIGN KEY (`id_aluno`) REFERENCES `alunos` (`id_aluno`) ON DELETE CASCADE,
  CONSTRAINT `planos_aluno_ibfk_2` FOREIGN KEY (`id_plano`) REFERENCES `planos` (`id_plano`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `suporte`
--

DROP TABLE IF EXISTS `suporte`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suporte` (
  `ticket` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL,
  `categoria_suporte` varchar(50) NOT NULL,
  `descricao_suporte` varchar(255) NOT NULL,
  `id_aluno` int NOT NULL,
  PRIMARY KEY (`ticket`),
  UNIQUE KEY `idx_suporte_ticket` (`ticket`),
  KEY `idx_suporte_aluno` (`id_aluno`),
  KEY `idx_suporte_status` (`status`),
  CONSTRAINT `suporte_ibfk_1` FOREIGN KEY (`id_aluno`) REFERENCES `alunos` (`id_aluno`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `treinos`
--

DROP TABLE IF EXISTS `treinos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `treinos` (
  `id_treino` int NOT NULL AUTO_INCREMENT,
  `nome_treino` varchar(50) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `dia_treino` datetime NOT NULL,
  `id_aluno` int NOT NULL,
  `id_funcionario` int DEFAULT NULL,
  PRIMARY KEY (`id_treino`),
  KEY `idx_treinos_aluno` (`id_aluno`),
  KEY `idx_treinos_funcionario` (`id_funcionario`),
  KEY `idx_treinos_data` (`dia_treino`),
  CONSTRAINT `treinos_ibfk_1` FOREIGN KEY (`id_aluno`) REFERENCES `alunos` (`id_aluno`) ON DELETE CASCADE,
  CONSTRAINT `treinos_ibfk_2` FOREIGN KEY (`id_funcionario`) REFERENCES `funcionarios` (`id_funcionario`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `cpf` varchar(16) NOT NULL,
  `data_nascimento` date DEFAULT NULL,
  `tipo` enum('aluno','funcionario') NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `avatar` varchar(255) NOT NULL DEFAULT '/images/upload/pfp/avatar.png',
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `cpf` (`cpf`),
  UNIQUE KEY `idx_usuarios_email` (`email`),
  UNIQUE KEY `idx_usuarios_cpf` (`cpf`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping events for database 'techfit'
--

--
-- Dumping routines for database 'techfit'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-04 15:16:15
