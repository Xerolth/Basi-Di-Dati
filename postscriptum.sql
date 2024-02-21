-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 13, 2023 at 03:11 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `postscriptum`
--

-- --------------------------------------------------------

--
-- Table structure for table `appartiene`
--

CREATE TABLE `appartiene` (
  `id_blog` int(10) NOT NULL,
  `id_categoria` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appartiene`
--

INSERT INTO `appartiene` (`id_blog`, `id_categoria`) VALUES
(63, 5),
(63, 8),
(71, 1),
(71, 2),
(72, 9),
(72, 11),
(73, 5),
(73, 6),
(74, 5),
(74, 7),
(75, 9),
(75, 12),
(76, 5),
(76, 6);

-- --------------------------------------------------------

--
-- Table structure for table `blog`
--

CREATE TABLE `blog` (
  `id_blog` int(11) NOT NULL,
  `nome_blog` varchar(20) NOT NULL,
  `descrizione_blog` varchar(200) NOT NULL,
  `creazione_blog` datetime NOT NULL,
  `autore_blog` int(11) DEFAULT NULL,
  `tema` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blog`
--

INSERT INTO `blog` (`id_blog`, `nome_blog`, `descrizione_blog`, `creazione_blog`, `autore_blog`, `tema`) VALUES
(63, 'Pokemon', 'Questo è un blog dedicato ai Pokemon.', '2023-07-11 21:35:56', 38, 'Oceano'),
(71, 'Racconti Preferiti', 'Lorem Ipsum', '2023-07-12 00:07:53', 41, 'Nord'),
(72, 'Van Gogh', 'Blog dedicato a Van Gogh e alle sue opere', '2023-07-12 00:24:27', 42, 'Default'),
(73, 'Passione Cinema!', 'Blog in cui vi parlerò dei film che mi piacciono e delle nuove uscite!', '2023-07-12 00:29:27', 43, 'Nord'),
(74, 'provaprova', 'dsdsdasdasdasdasdasdasda', '2023-07-12 00:30:42', 43, 'Oceano'),
(75, 'Lorem Ipsum', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', '2023-07-12 00:33:17', 38, 'Nord'),
(76, 'Film Preferiti', 'In questo blog vi parlerò dei miei film preferitiIn questo blog vi parlerò dei miei film preferitiIn questo blog vi parlerò dei miei film preferitiIn questo blog vi parlerò dei miei film preferiti', '2023-07-12 18:00:11', 44, 'Oceano');

-- --------------------------------------------------------

--
-- Table structure for table `categoria`
--

CREATE TABLE `categoria` (
  `id_categoria` int(10) NOT NULL,
  `nome_categoria` varchar(30) NOT NULL,
  `parent_categoria` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categoria`
--

INSERT INTO `categoria` (`id_categoria`, `nome_categoria`, `parent_categoria`) VALUES
(1, 'Sport', NULL),
(2, 'Sport di contatto', 1),
(3, 'Sport invernali', 1),
(4, 'Sport acquatici', 1),
(5, 'Intrattenimento', NULL),
(6, 'Cinema', 5),
(7, 'Serie TV', 5),
(8, 'Teatro', 5),
(9, 'Cultura', NULL),
(10, 'Storia', 9),
(11, 'Arte', 9),
(12, 'Letteratura', 9),
(13, 'Tecnologia', NULL),
(14, 'Smartphone', 13),
(15, 'PC', 13),
(16, 'Videogiochi', 13),
(17, 'Attualità', NULL),
(18, 'Cronaca', 17),
(19, 'Politica', 17),
(20, 'Economia', 17),
(21, 'Tempo Libero', NULL),
(22, 'Fai da Te', 21),
(23, 'Aria Aperta', 21),
(24, 'Viaggi', 21);

-- --------------------------------------------------------

--
-- Table structure for table `coautore`
--

CREATE TABLE `coautore` (
  `id_utente` int(11) NOT NULL,
  `id_blog` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coautore`
--

INSERT INTO `coautore` (`id_utente`, `id_blog`) VALUES
(38, 71),
(38, 74),
(39, 63),
(41, 63),
(42, 74);

-- --------------------------------------------------------

--
-- Table structure for table `commento`
--

CREATE TABLE `commento` (
  `id_commento` int(11) NOT NULL,
  `testo_commento` text NOT NULL,
  `creazione_commento` datetime NOT NULL,
  `post` int(11) NOT NULL,
  `autore_commento` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `commento`
--

INSERT INTO `commento` (`id_commento`, `testo_commento`, `creazione_commento`, `post`, `autore_commento`) VALUES
(74, 'Ho provato ad inserire caratteri pericolosi, ma non ha funzionato!', '2023-07-11 22:37:39', 84, 38),
(75, 'Bel blog, bravo! Ora provo ad inserire qualcosa di malefico!', '2023-07-11 22:38:39', 84, 39),
(76, 'Scherzavo', '2023-07-11 22:38:44', 84, 39),
(77, 'Bel racconto, mi piace!', '2023-07-12 00:08:38', 94, 41),
(78, 'bel racconto!', '2023-07-12 00:19:23', 96, 38),
(79, 'anche a me!', '2023-07-12 00:19:41', 94, 38),
(80, 'bel post, bravo!', '2023-07-12 00:22:23', 97, 41),
(81, 'Il mio quadro preferito <3\n', '2023-07-12 00:26:35', 98, 38),
(82, 'anche il mio!\n', '2023-07-12 00:26:56', 98, 39),
(83, 'fantastico <3', '2023-07-12 18:49:33', 103, 38),
(84, 'Ha delle inquadrature incredibili, bella scelta!', '2023-07-12 18:50:17', 102, 38);

-- --------------------------------------------------------

--
-- Table structure for table `like`
--

CREATE TABLE `like` (
  `id_utente` int(11) NOT NULL,
  `id_post` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `like`
--

INSERT INTO `like` (`id_utente`, `id_post`) VALUES
(38, 94),
(38, 95),
(38, 96),
(38, 98),
(38, 102),
(38, 103),
(39, 98),
(41, 94),
(41, 97),
(41, 98);

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE `post` (
  `id_post` int(11) NOT NULL,
  `titolo` varchar(50) NOT NULL,
  `testo_post` text NOT NULL,
  `numlike` int(8) NOT NULL DEFAULT 0,
  `creazione_post` datetime NOT NULL,
  `copertina` varchar(2048) DEFAULT NULL,
  `immagine` varchar(2048) DEFAULT NULL,
  `blog` int(10) NOT NULL,
  `autore_post` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `post`
--

INSERT INTO `post` (`id_post`, `titolo`, `testo_post`, `numlike`, `creazione_post`, `copertina`, `immagine`, `blog`, `autore_post`) VALUES
(84, 'Umbreon', 'Umbreon è un Pokémon di tipo Buio introdotto in seconda generazione.\r\n\r\nSi evolve da Eevee quando ha un alto affetto e aumenta di livello durante la notte o quando ha un alto affetto e aumenta di livello durante la notte con un Coccio Lunare nella borsaXD. È una delle evoluzioni finali di Eevee, le altre sono Vaporeon, Jolteon, Flareon, Espeon, Leafeon, Glaceon e Sylveon.\r\n\r\nÈ il Pokémon iniziale di Pokémon Colosseum insieme ad Espeon. ', 0, '2023-07-11 21:36:10', 'imgs/post/63/38mirko211689104170/copertina_1680798418783762.jpg', NULL, 63, 38),
(94, 'Lorem Ipsum', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec magna velit, volutpat feugiat mi sit amet, accumsan placerat mi. Mauris nec risus eu metus vehicula semper. Cras et lacus sed purus dapibus pharetra. In non luctus velit. Phasellus ac tortor in ex condimentum sagittis. Curabitur luctus lacinia turpis. Donec ornare neque vel sodales efficitur. Pellentesque posuere sagittis mi non dapibus. Pellentesque eu elit quis orci lacinia consectetur sed id leo. Sed consectetur consequat libero quis volutpat. Etiam eget blandit erat. Sed scelerisque neque elit, ac blandit lorem accumsan sit amet. Maecenas pretium nec magna eget malesuada. Donec a malesuada libero. Maecenas lobortis nulla quam, at vestibulum est faucibus quis. Maecenas risus diam, molestie ac odio non, volutpat convallis nulla. ', 2, '2023-07-12 00:08:23', 'imgs/post/71/41utenteprova21689113303/copertina_1U6jbeq8PluT7aVRmN5ilg.jpeg', 'imgs/post/71/41utenteprova21689113303/immagine17057248642_9a88d08c3b_oscaled870x555.jpg', 71, 41),
(95, 'Fire of Prometheus', 'We feel the atmosphere become quiet as the sounds of conversation come to a stop. Everyone slowly rises to their feet to look out at what is happening beyond our cameras.\r\n\r\nBreaking through the polluted atmosphere, we see stars. We had made it through the stratosphere. A staggering sense of accomplishment wells up within us.\r\n\r\nWe let out cries of joy. The sky, the stars, the machines, the lives, they all give us their blessings. The voices join into song.\r\n\r\nWe are headed ever further into the outside world, just as we promised you. We are alive, just as you were. We are singing, singing, singing. Will our song reach you? Will our feelings reach you, wherever you are? Hallelujah. Hallelujah. Hallelujah. Hallelujah.\r\n\r\nHallelujah!\r\n\r\nFrom the machine entrusted with a humble wish, a simple hymn spreads throughout the universe. ', 1, '2023-07-12 00:10:13', 'imgs/post/71/41utenteprova21689113413/copertina_1U6jbeq8PluT7aVRmN5ilg.jpeg', 'imgs/post/71/41utenteprova21689113413/immagine1U6jbeq8PluT7aVRmN5ilg.jpeg', 71, 41),
(96, 'Il principe Felice', 'Il Principe Felice è una statua posta su una colonna, ricoperta di foglie d\'oro e pietre preziose, e pertanto ammirata da tutti gli abitanti di un\'innominata città. Una notte, una rondine che si sta recando al sole in Egitto decide di sostare ai piedi della statua del Principe, e lo vede piangere: lui le racconta la sua storia, dicendo di essere vissuto sempre felice nel palazzo di Sanssouci e non essersi mai chiesto cosa vi fosse fuori da esso, e adesso che è morto vede, dall\'alto della colonna, le brutture e le miserie della città che nella sua vita aveva sempre ignorato. Quindi, prega la rondine di aiutarlo a cancellarle finché possibile. La rondine preferirebbe il caldo egiziano, ma, vinta dalle lacrime del Principe, acconsente di aiutarlo e inizia a spogliarlo dei gioielli che lo adornano, per poi donarli ai poveri e ai bisognosi che il Principe le indica.', 1, '2023-07-12 00:18:50', 'imgs/post/71/41utenteprova21689113930/copertina_Happy_prince.jpg', 'imgs/post/71/41utenteprova21689113930/immagineHappy_prince.jpg', 71, 41),
(97, 'Pinocchio', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec magna velit, volutpat feugiat mi sit amet, accumsan placerat mi. Mauris nec risus eu metus vehicula semper. Cras et lacus sed purus dapibus pharetra. In non luctus velit. Phasellus ac tortor in ex condimentum sagittis. Curabitur luctus lacinia turpis. Donec ornare neque vel sodales efficitur. Pellentesque posuere sagittis mi non dapibus. Pellentesque eu elit quis orci lacinia consectetur sed id leo. Sed consectetur consequat libero quis volutpat. Etiam eget blandit erat. Sed scelerisque neque elit, ac blandit lorem accumsan sit amet. Maecenas pretium nec magna eget malesuada. Donec a malesuada libero. Maecenas lobortis nulla quam, at vestibulum est faucibus quis. Maecenas risus diam, molestie ac odio non, volutpat convallis nulla. ', 1, '2023-07-12 00:21:28', 'imgs/post/71/38mirko211689114088/copertina_morty.ononoki.jpg', NULL, 71, 38),
(98, 'Notte Stellata', 'Starry, starry night\r\nPaint your palette blue and grey\r\nLook out on a summer\'s day\r\nWith eyes that know the darkness in my soul\r\nShadows on the hills\r\nSketch the trees and the daffodils\r\nCatch the breeze and the winter chills\r\nIn colors on the snowy linen land', 3, '2023-07-12 00:26:03', 'imgs/post/72/42utenteprova31689114363/copertina_VincentVanGoghTheStarryNight.png', 'imgs/post/72/42utenteprova31689114363/immagineVincentVanGoghTheStarryNight.png', 72, 42),
(99, 'Arrival', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec magna velit, volutpat feugiat mi sit amet, accumsan placerat mi. Mauris nec risus eu metus vehicula semper. Cras et lacus sed purus dapibus pharetra. In non luctus velit. Phasellus ac tortor in ex condimentum sagittis. Curabitur luctus lacinia turpis. Donec ornare neque vel sodales efficitur. Pellentesque posuere sagittis mi non dapibus. Pellentesque eu elit quis orci lacinia consectetur sed id leo. Sed consectetur consequat libero quis volutpat. Etiam eget blandit erat. Sed scelerisque neque elit, ac blandit lorem accumsan sit amet. Maecenas pretium nec magna eget malesuada. Donec a malesuada libero. Maecenas lobortis nulla quam, at vestibulum est faucibus quis. Maecenas risus diam, molestie ac odio non, volutpat convallis nulla. ', 0, '2023-07-12 00:30:10', 'imgs/post/73/43marcovaldo1689114610/copertina_Arrival1.png', 'imgs/post/73/43marcovaldo1689114610/immagineArrival_film.jpg', 73, 43),
(100, 'Lorem Ipsum', 'asdgashdgajshdgasjdpaodjfkjsfmvpokslj noaawihdioqiwdohefkahrfoag4jfhbakjrhfhSJGDghizdgvjzdhvhxv:ksahdjahdjgae uywgduaejhgasdyyadgusgysghagssfgsgfjA asdgashdgajshdgasjdpaodjfkjsfmvpokslj noaawihdioqiwdohefkahrfoag4jfhbakjrhfhSJGDghizdgvjzdhvhxv:ksahdjahdjgae uywgduaejhgasdyyadgusgysghagssfgsgfjAasdgashdgajshdgasjdpaodjfkjsfmvpokslj noaawihdioqiwdohefkahrfoag4jfhbakjrhfhSJGDghizdgvjzdhvhxv:ksahdjahdjgae uywgduaejhgasdyyadgusgysghagssfgsgfjAasdgashdgajshdgasjdpaodjfkjsfmvpokslj noaawihdioqiwdohefkahrfoag4jfhbakjrhfhSJGDghizdgvjzdhvhxv:ksahdjahdjgae uywgduaejhgasdyyadgusgysghagssfgsgfjAasdgashdgajshdgasjdpaodjfkjsfmvpokslj noaawihdioqiwdohefkahrfoag4jfhbakjrhfhSJGDghizdgvjzdhvhxv:ksahdjahdjgae uywgduaejhgasdyyadgusgysghagssfgsgfjA', 0, '2023-07-12 00:32:15', 'imgs/post/74/38mirko211689114735/copertina_1680798418783762.jpg', NULL, 74, 38),
(101, 'ciao a tutti!', 'sto testando le funzionalità del sito', 0, '2023-07-12 00:33:44', 'imgs/post/75/38mirko211689114824/copertina_Arrival_film.jpg', NULL, 75, 38),
(102, 'Barry Lyndon', 'Barry Lyndon è un film del 1975 scritto, diretto e prodotto da Stanley Kubrick.\r\nSi tratta di un film storico che trae il proprio soggetto dal romanzo Le memorie di Barry Lyndon di William Makepeace Thackeray.\r\nNonostante all\'uscita nelle sale non abbia prodotto incassi cospicui, Barry Lyndon è oggi considerato uno dei migliori film di Kubrick e una delle più grandi opere cinematografiche mai realizzate. Per creare un\'opera il più possibile realistica, Kubrick trasse ispirazione dai più famosi paesaggisti del XVIII secolo per scegliere le ambientazioni dei set. Le riprese vennero effettuate in Inghilterra, Irlanda e Germania.\r\nLe scene e i costumi vennero ricavati da quadri, stampe e disegni d\'epoca. Grazie a questa attenzione ai dettagli il film ottenne i premi Oscar alla migliore fotografia (John Alcott), alla migliore scenografia (Ken Adam) e ai migliori costumi (Milena Canonero e Ulla-Britt Soderlund) nel 1976. Le riprese vennero invece eseguite con l\'ausilio della sola luce naturale o, tutt\'al più, delle candele e delle lampade a olio per le riprese notturne. Questa scelta implicò l\'utilizzo di lenti rivoluzionarie, studiate dalla Zeiss per la NASA (come il Zeiss Planar 50mm f0.7, uno degli obiettivi più luminosi mai realizzati nella storia della fotografia), e di nuove macchine da presa messe a punto dalla Panavision.\r\nNel Regno Unito e negli Stati Uniti uscì il 18 dicembre 1975, mentre in Italia il 1 gennaio 1976. ', 1, '2023-07-12 18:05:40', 'imgs/post/76/44barrylyndon1689177940/copertina_poster_06.jpg', 'imgs/post/76/44barrylyndon1689177940/immagineposter_06.jpg', 76, 44),
(103, 'The End of Evangelion', 'Neon Genesis Evangelion: The End of Evangelion è una pellicola d\'animazione del 1997 diretta da Hideaki Anno.\r\n\r\nIl lungometraggio, di produzione giapponese, fu realizzato come finale della serie televisiva Neon Genesis Evangelion, andata in onda fino al 1996, conclusasi con due episodi che diventarono fonte di controversie. Poco prima della sua uscita Hideaki Anno e lo studio Gainax produssero un lungometraggio chiamato Neon Genesis Evangelion: Death And Rebirth, formato da un riassunto dei primi ventiquattro episodi della serie e una breve anticipazione dell\'opera. Come Death And Rebirth, The End of Evangelion venne concepito dai creatori per essere diviso in due parti, chiamate \"episodio 25\" e \"episodio 26\", rifacimento delle ultime due puntate della serie televisiva originale. Nel 1998 i due lungometraggi, parzialmente sovrapposti, vennero concatenati eliminando la parte in comune e ripubblicati con il nome di Revival of Evangelion.\r\n \r\nLa storia è incentrata su Shinji Ikari, giovane pilota di un mecha chiamato Eva, e sulla sua collega Asuka Soryu Langley. Shinji durante il lungometraggio si ritrova a decidere il corso di un processo chiamato Progetto per il perfezionamento dell\'uomo, in cui le anime degli esseri umani si uniscono in un\'unica entità divina. Nel corso del processo il ragazzo accetta la realtà e se stesso per quel che è, rifiutando il Perfezionamento e preferendo vivere in un mondo in cui coesistere con le individualità degli altri esseri umani. Per il lungometraggio venne chiamato il personale dei doppiatori della serie originale, fra cui Megumi Ogata, Yuko Miyamura e Megumi Hayashibara.\r\n\r\nIl lungometraggio diventò un successo di botteghino e incassò un miliardo e mezzo di yen. Con gli anni e l\'accrescersi della fama del franchise di Evangelion è diventato un titolo di culto dell\'animazione giapponese. è stato premiato agli Awards of the Japanese Academy, agli Animation Kobe e alla quindicesima edizione del Golden Gloss Award. ', 1, '2023-07-12 18:34:15', 'imgs/post/76/44barrylyndon1689179655/copertina_Screenshot(187).png', 'imgs/post/76/44barrylyndon1689179655/immaginew4iteqp86bs21.jpg', 76, 44);

-- --------------------------------------------------------

--
-- Table structure for table `tema`
--

CREATE TABLE `tema` (
  `nome_tema` varchar(15) NOT NULL,
  `colore_sfondo` varchar(20) NOT NULL,
  `colore_font` varchar(20) NOT NULL,
  `colore_bottoni` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tema`
--

INSERT INTO `tema` (`nome_tema`, `colore_sfondo`, `colore_font`, `colore_bottoni`) VALUES
('Default', 'bg-light', 'text-black', 'btn-primary'),
('Nord', 'bg-secondary', 'text-body', 'btn-info'),
('Oceano', 'bg-primary', 'text-dark', 'btn-info'),
('Scuro', 'bg-dark', 'text-light', 'btn-secondary');

-- --------------------------------------------------------

--
-- Table structure for table `utente`
--

CREATE TABLE `utente` (
  `id_utente` int(11) NOT NULL,
  `nome_utente` varchar(20) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `cognome` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `passhash` varchar(255) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `documento` varchar(20) NOT NULL,
  `creazione_utente` datetime NOT NULL,
  `avatar` varchar(2048) NOT NULL DEFAULT 'imgs/avatar/default.jpg',
  `descrizione_utente` tinytext DEFAULT NULL,
  `verificato` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `utente`
--

INSERT INTO `utente` (`id_utente`, `nome_utente`, `nome`, `cognome`, `email`, `passhash`, `telefono`, `documento`, `creazione_utente`, `avatar`, `descrizione_utente`, `verificato`) VALUES
(38, 'mirko21', 'Mirko', 'Secchini', 'mirko21@gmail.com', '$2y$10$Tu0FttXxpODtDTK6L7BxKetotIzJr4qNfW5XAz61wcliLko0WzVGW', '213112331231', 'AZ0000001', '2023-07-11 21:17:35', 'imgs/avatar/mirko21/1689104851_1680798418783762.jpg', 'Sono il primo utente! Unico ed inimitabile.', 1),
(39, 'utenteprova', 'utente', 'prova', 'utente@prova.it', '$2y$10$gO3M.zf6YR5V6xfE0ylbB.lgmPdUQ.GuW9/SqwMYPFBDF48orvyNO', '231312312312', 'AZ2182372', '2023-07-11 21:28:09', 'imgs/avatar/utenteprova/1689103701_1680798418783762.jpg', 'Sono un utente di prova.', 1),
(41, 'utenteprova2', 'utente', 'prova', 'utente@prova2.com', '$2y$10$vwxOaYpjZVsg7P/Hef1LSOVlC5migLiOr7XmHP7.ueijeZp/UOYzu', '3922081419', 'AB0000010', '2023-07-12 00:07:00', 'imgs/avatar/utenteprova2/1689113231_Horus.png', 'Questo è il mio profilo, piacere!', 1),
(42, 'utenteprova3', 'utente', 'prova', 'utente@prova3.com', '$2y$10$pIynQDssqe.DqEOyk48it.4DGNSuEz0BnhQu171VMa1X6VWsrl3TO', '3922081420', 'AB0000011', '2023-07-12 00:23:38', 'imgs/avatar/utenteprova3/1689114230_selfportrait.jpg', 'Mi piace van gogh', 1),
(43, 'marcovaldo', 'Marco', 'Valdo', 'marco@valdo.com', '$2y$10$XGKqcOmsFSdb4WUEeSpCkOutw8nhsYDEYsE0vqch4R2xdD6fN49F6', '3922081421', 'AB0000012', '2023-07-12 00:28:02', 'imgs/avatar/marcovaldo/1689114512_FP5wC0maQAIZ7If.jpg', 'ciao a tutti!', 1),
(44, 'barrylyndon', 'Barry', 'Lyndon', 'barry@lyndon.com', '$2y$10$NMwGR8Vhyk.2UP.tFuRsceOyfwstjHQ8AbKfgGXH0Yo3B3tl1LPAG', '3922081422', 'AB0000015', '2023-07-12 17:10:22', 'imgs/avatar/barrylyndon/1689174635_poster_06.jpg', 'Mi piace barry lyndon', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appartiene`
--
ALTER TABLE `appartiene`
  ADD PRIMARY KEY (`id_blog`,`id_categoria`),
  ADD KEY `id_categoria` (`id_categoria`);

--
-- Indexes for table `blog`
--
ALTER TABLE `blog`
  ADD PRIMARY KEY (`id_blog`),
  ADD KEY `autore_blog` (`autore_blog`),
  ADD KEY `tema` (`tema`);

--
-- Indexes for table `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indexes for table `coautore`
--
ALTER TABLE `coautore`
  ADD PRIMARY KEY (`id_utente`,`id_blog`),
  ADD KEY `id_blog` (`id_blog`);

--
-- Indexes for table `commento`
--
ALTER TABLE `commento`
  ADD PRIMARY KEY (`id_commento`),
  ADD KEY `post` (`post`),
  ADD KEY `autore_commento` (`autore_commento`);

--
-- Indexes for table `like`
--
ALTER TABLE `like`
  ADD PRIMARY KEY (`id_utente`,`id_post`),
  ADD KEY `id_post` (`id_post`);

--
-- Indexes for table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id_post`),
  ADD KEY `blog` (`blog`),
  ADD KEY `autore_post` (`autore_post`);

--
-- Indexes for table `tema`
--
ALTER TABLE `tema`
  ADD PRIMARY KEY (`nome_tema`);

--
-- Indexes for table `utente`
--
ALTER TABLE `utente`
  ADD PRIMARY KEY (`id_utente`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blog`
--
ALTER TABLE `blog`
  MODIFY `id_blog` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id_categoria` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `commento`
--
ALTER TABLE `commento`
  MODIFY `id_commento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `post`
--
ALTER TABLE `post`
  MODIFY `id_post` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `utente`
--
ALTER TABLE `utente`
  MODIFY `id_utente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appartiene`
--
ALTER TABLE `appartiene`
  ADD CONSTRAINT `appartiene_ibfk_1` FOREIGN KEY (`id_blog`) REFERENCES `blog` (`id_blog`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `appartiene_ibfk_2` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id_categoria`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `blog`
--
ALTER TABLE `blog`
  ADD CONSTRAINT `blog_ibfk_1` FOREIGN KEY (`autore_blog`) REFERENCES `utente` (`id_utente`) ON DELETE CASCADE,
  ADD CONSTRAINT `blog_ibfk_2` FOREIGN KEY (`tema`) REFERENCES `tema` (`nome_tema`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `coautore`
--
ALTER TABLE `coautore`
  ADD CONSTRAINT `coautore_ibfk_1` FOREIGN KEY (`id_blog`) REFERENCES `blog` (`id_blog`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `coautore_ibfk_2` FOREIGN KEY (`id_utente`) REFERENCES `utente` (`id_utente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `commento`
--
ALTER TABLE `commento`
  ADD CONSTRAINT `commento_ibfk_1` FOREIGN KEY (`autore_commento`) REFERENCES `utente` (`id_utente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `commento_ibfk_2` FOREIGN KEY (`post`) REFERENCES `post` (`id_post`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `like`
--
ALTER TABLE `like`
  ADD CONSTRAINT `like_ibfk_1` FOREIGN KEY (`id_post`) REFERENCES `post` (`id_post`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `like_ibfk_2` FOREIGN KEY (`id_utente`) REFERENCES `utente` (`id_utente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `post_ibfk_1` FOREIGN KEY (`autore_post`) REFERENCES `utente` (`id_utente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `post_ibfk_2` FOREIGN KEY (`blog`) REFERENCES `blog` (`id_blog`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
