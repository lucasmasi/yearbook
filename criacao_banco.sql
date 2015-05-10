
--
-- Table structure for table `cidades`
--

CREATE TABLE IF NOT EXISTS `cidades` (
  `idCidade` int(11) NOT NULL AUTO_INCREMENT,
  `idEstado` int(11) NOT NULL,
  `nomeCidade` varchar(70) NOT NULL,
  PRIMARY KEY (`idCidade`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `cidades`
--


-- --------------------------------------------------------

--
-- Table structure for table `estados`
--

CREATE TABLE IF NOT EXISTS `estados` (
  `idEstado` int(11) NOT NULL AUTO_INCREMENT,
  `sigaEstado` char(2) NOT NULL,
  `nomeEstado` varchar(50) NOT NULL,
  PRIMARY KEY (`idEstado`),
  UNIQUE KEY `sigaEstado` (`sigaEstado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `estados`
--


-- --------------------------------------------------------

--
-- Table structure for table `participantes`
--

CREATE TABLE IF NOT EXISTS `participantes` (
  `login` varchar(20) NOT NULL,
  `senha` varchar(50) NOT NULL,
  `nomeCompleto` varchar(50) NOT NULL,
  `arquivoFoto` varchar(50) NOT NULL,
  `cidade` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `descricao` varchar(5000) NOT NULL,
  PRIMARY KEY (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
