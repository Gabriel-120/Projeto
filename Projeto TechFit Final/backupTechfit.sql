
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

/*!40000 DROP DATABASE IF EXISTS `techfit`*/;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `techfit` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `techfit`;
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `agendamento` WRITE;
/*!40000 ALTER TABLE `agendamento` DISABLE KEYS */;
INSERT INTO `agendamento` VALUES (3,'2025-12-04 14:55:06','cancelado',4,3),(4,'2025-12-04 15:05:05','agendado',4,3),(5,'2025-12-04 15:57:09','agendado',4,7);
/*!40000 ALTER TABLE `agendamento` ENABLE KEYS */;
UNLOCK TABLES;
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `alunos` WRITE;
/*!40000 ALTER TABLE `alunos` DISABLE KEYS */;
INSERT INTO `alunos` VALUES (1,'Feminino','Rua das Flores, 10','(11) 98888-1111','ANA2025',1),(3,'Feminino','231231232131','(12) 34567-8976',NULL,6),(4,'Masculino','1231232112','(21) 34523-1413',NULL,7),(5,'Masculino','231231232131','(12) 34567-8976',NULL,8),(6,'Feminino','231231232131','(12) 34567-8976',NULL,9),(7,'Feminino','231231232131','(12) 34567-8976',NULL,10);
/*!40000 ALTER TABLE `alunos` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `aulas` WRITE;
/*!40000 ALTER TABLE `aulas` DISABLE KEYS */;
INSERT INTO `aulas` VALUES (4,'asdasdasd','2029-01-31 12:00:00',213,'',2,1,2);
/*!40000 ALTER TABLE `aulas` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `aulas_aluno` WRITE;
/*!40000 ALTER TABLE `aulas_aluno` DISABLE KEYS */;
INSERT INTO `aulas_aluno` VALUES (4,3),(4,7);
/*!40000 ALTER TABLE `aulas_aluno` ENABLE KEYS */;
UNLOCK TABLES;
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `avaliacoes` WRITE;
/*!40000 ALTER TABLE `avaliacoes` DISABLE KEYS */;
INSERT INTO `avaliacoes` VALUES (3,'AAAAAAAAAA',1.00,3,2),(5,'aasdasd',5.00,6,2),(6,'asdadsa',5.00,4,2);
/*!40000 ALTER TABLE `avaliacoes` ENABLE KEYS */;
UNLOCK TABLES;
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `avisos` WRITE;
/*!40000 ALTER TABLE `avisos` DISABLE KEYS */;
INSERT INTO `avisos` VALUES (7,'aaaaaaaaaaaaaaaaaaa','Comunicado','aaaaaaaaaa','2026-01-03','2025-12-03','/images/upload/avisos/aviso_69306b2fee27c7.52568148.jpg',NULL),(8,'asdasda','Evento','asdad','2026-01-04','2025-12-04','/images/upload/avisos/aviso_6931ce1f688102.12471071.jpg',2),(9,'ASDA','Comunicado','SADAD','2026-01-04','2025-12-04',NULL,2);
/*!40000 ALTER TABLE `avisos` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `checkin` WRITE;
/*!40000 ALTER TABLE `checkin` DISABLE KEYS */;
INSERT INTO `checkin` VALUES (1,'2025-11-12 09:00:00',1,1);
/*!40000 ALTER TABLE `checkin` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `estoque` WRITE;
/*!40000 ALTER TABLE `estoque` DISABLE KEYS */;
INSERT INTO `estoque` VALUES (1,150,'Whey Protein 1kg'),(2,300,'Barra de Cereal'),(3,50,'Camiseta Techfit M');
/*!40000 ALTER TABLE `estoque` ENABLE KEYS */;
UNLOCK TABLES;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `filiais` WRITE;
/*!40000 ALTER TABLE `filiais` DISABLE KEYS */;
INSERT INTO `filiais` VALUES (1,'Techfit Centro','Rua Principal, 123, Centro','(11) 98765-4321'),(2,'Techfit Bairro Norte','Avenida Norte, 456, Bairro Norte','(11) 91234-5678'),(3,'ASDA','ASDADASDA','13131231231');
/*!40000 ALTER TABLE `filiais` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `funcionarios` WRITE;
/*!40000 ALTER TABLE `funcionarios` DISABLE KEYS */;
INSERT INTO `funcionarios` VALUES (2,6000.00,44,'Gerente',4);
/*!40000 ALTER TABLE `funcionarios` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `mensagens` WRITE;
/*!40000 ALTER TABLE `mensagens` DISABLE KEYS */;
INSERT INTO `mensagens` VALUES (1,'Seu novo treino','Oi Ana, já lancei seu novo treino (Treino A).','2025-11-12 11:00:00',NULL,0,1,2),(2,'Re: Seu novo treino','Obrigada, instrutor! Vou começar hoje mesmo.','2025-11-12 11:05:00',NULL,0,2,1);
/*!40000 ALTER TABLE `mensagens` ENABLE KEYS */;
UNLOCK TABLES;
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `modalidades` WRITE;
/*!40000 ALTER TABLE `modalidades` DISABLE KEYS */;
INSERT INTO `modalidades` VALUES (1,'Musculação','Treinamento de força com pesos livres e máquinas.'),(2,'Yoga','Prática de posturas, respiração e meditação para equilíbrio.'),(4,'AA','AA');
/*!40000 ALTER TABLE `modalidades` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `pagamentos` WRITE;
/*!40000 ALTER TABLE `pagamentos` DISABLE KEYS */;
INSERT INTO `pagamentos` VALUES (1,'Aprovado','2025-11-05 10:00:00',1199.00,'Cartão de Crédito',1),(3,'confirmado','2025-12-04 17:59:41',99.90,'Plano Básico Mensal',3),(4,'confirmado','2025-12-04 18:01:13',1199.00,'Plano Premium Anual',3);
/*!40000 ALTER TABLE `pagamentos` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `planos` WRITE;
/*!40000 ALTER TABLE `planos` DISABLE KEYS */;
INSERT INTO `planos` VALUES (1,'Plano Básico Mensal','Acesso a todas as áreas de musculação.',99.90,30),(2,'Plano Premium Anual','Acesso total, incluindo todas as aulas.',1199.00,365);
/*!40000 ALTER TABLE `planos` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `planos_aluno` WRITE;
/*!40000 ALTER TABLE `planos_aluno` DISABLE KEYS */;
INSERT INTO `planos_aluno` VALUES (1,2,'2025-11-05','2026-11-05','ativo'),(3,1,'2025-12-04','2026-01-03','ativo'),(3,2,'2025-12-04','2026-12-04','ativo');
/*!40000 ALTER TABLE `planos_aluno` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `suporte` WRITE;
/*!40000 ALTER TABLE `suporte` DISABLE KEYS */;
INSERT INTO `suporte` VALUES ('TKT-2025-001','Aberto','Equipamento','Leg press da filial Centro está fazendo barulho.',1),('TKT-2025-7BA6E786','Resolvido','Pagamento','asdasda',7),('TKT-2025-B4D42E10','Em Andamento','Agendamento','dafssfafa',3);
/*!40000 ALTER TABLE `suporte` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `treinos` WRITE;
/*!40000 ALTER TABLE `treinos` DISABLE KEYS */;
INSERT INTO `treinos` VALUES (1,'Treino A - Foco em Pernas','Agachamento, Leg Press, Extensora.','2025-11-12 00:00:00',1,NULL);
/*!40000 ALTER TABLE `treinos` ENABLE KEYS */;
UNLOCK TABLES;
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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Ana Silva','ana.silva@email.com','123.456.789-00',NULL,'aluno','202cb962ac59075b964b07152d234b70','/images/upload/pfp/avatar.png'),(2,'Alex Souza','teste@techfit.com','111.222.333-44',NULL,'funcionario','80177534a0c99a7e3645b52f2027a48b','/images/upload/pfp/avatar.png'),(4,'Daniel Moreira','daniel.moreira@techfit.com','555.666.777-88',NULL,'funcionario','80177534a0c99a7e3645b52f2027a48b','/images/upload/pfp/avatar.png'),(6,'Cleitin','aa@teste.com','123.456.789-02','1989-07-12','aluno','ca561fc8482b7c3b4de22dd3b0422923','/images/upload/pfp/avatar_6.jpeg'),(7,'asdsadsad','baa@teste.com','123.213.213-12','1990-10-12','aluno','10a9c136d796bab18d3e144092a4f20a','/images/upload/pfp/avatar.png'),(8,'Cleber','teste@teste.com','123.456.432-12','1990-05-10','funcionario','06afa6c8b54d3cc80d269379d8b6a078','/images/upload/pfp/avatar.png'),(9,'asdada','123321@teste.com','123.132.132-13','1990-05-10','aluno','04ab167311203a5dafc6d88f95260723','/images/upload/pfp/avatar.png'),(10,'asdada','blabla@teste.com','123.452.134-23','2006-05-10','aluno','007eef79897cf95d8fd17f352a3a4799','/images/upload/pfp/avatar.png');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

