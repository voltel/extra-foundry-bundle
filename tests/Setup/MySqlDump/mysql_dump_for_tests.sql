-- MySQL dump 10.13  Distrib 5.7.17, for Win64 (x86_64)
--
-- Host: localhost    Database: voltel_extra_foundry_test
-- ------------------------------------------------------
-- Server version	5.7.18-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `address`
--

DROP TABLE IF EXISTS `address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) DEFAULT NULL,
  `countryCode` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cityName` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cityAreaName` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `addressName` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D4E6F819395C3F3` (`customer_id`),
  CONSTRAINT `FK_D4E6F819395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `address`
--

LOCK TABLES `address` WRITE;
/*!40000 ALTER TABLE `address` DISABLE KEYS */;
INSERT INTO `address` VALUES (1,1,'LR','Видное','Ville','284471, Новосибирская область, город Чехов, проезд Будапештсткая, 32'),(2,2,'NA','Дорохово','Ville','290754, Пензенская область, город Истра, спуск Балканская, 92'),(3,2,'MN','Ногинск','Ville','120856, Мурманская область, город Озёры, наб. Чехова, 61'),(4,2,'SM','Шатура','Ville','262710, Ивановская область, город Видное, ул. Гагарина, 92'),(5,3,'MG','South Davionmouth','bury','1708 Weber Squares\nArvelborough, OR 83187'),(6,3,'GY','Carolberg','land','8546 Dorthy Inlet\nEvelynmouth, OH 14848-2952'),(7,4,'VI','Hallefort','haven','80620 Christophe Shore\nWest Medamouth, TX 40641-0724'),(8,5,'NP','South Elmo','chester','50669 Hannah Center\nKeelingview, MO 03279-7731'),(9,6,'KG','Домодедово','Ville','406161, Новосибирская область, город Павловский Посад, спуск Будапештсткая, 55'),(10,6,'GE','Одинцово','Ville','733601, Мурманская область, город Солнечногорск, въезд Бухарестская, 34'),(11,7,'EC','Garthfurt','chester','74645 Kris Glen Suite 151\nIdellfort, MO 33220-1303'),(12,7,'HR','Tamaraton','mouth','250 Corkery Glens Apt. 052\nLake Wadeborough, NM 41091'),(13,7,'CD','Kochberg','burgh','18556 Cartwright Estates Suite 480\nKulasside, NC 56820-2530'),(14,8,'NI','Seanshire','ton','29260 Tiffany Ways Suite 835\nPort Robyn, AL 56912-4439'),(15,8,'AM','Veumchester','view','54802 Maggio Inlet Suite 241\nTowneborough, VT 79104-3108'),(16,8,'CO','Goodwinhaven','haven','999 Windler Ways\nNew Karlishire, AZ 37184-3994'),(17,9,'BZ','Москва','Ville','838075, Новосибирская область, город Серпухов, пер. Гоголя, 30'),(18,9,'LK','Красногорск','Ville','743775, Орловская область, город Ступино, въезд Гагарина, 55'),(19,9,'DZ','Лотошино','Ville','609908, Курганская область, город Одинцово, въезд Домодедовская, 85'),(20,10,'PL','Одеса','','05122, Івано-Франківська область, місто Івано-Франківськ, просп. Михайла Грушевського, 39'),(21,10,'UA','Луганськ','','51803, Київська область, місто Київ, пл. Мельникова, 60'),(22,10,'NI','Кропивницький','','86825, Харківська область, місто Харків, пл. Хрещатик, 53'),(23,11,'CF','Суми','','08744, Черкаська область, місто Черкаси, пров. П. Орлика, 44'),(24,11,'GE','Львів','','02372, Запорізька область, місто Запоріжжя, пл. Прорізна, 75'),(25,11,'BI','Тернопіль','','28430, Тернопільська область, місто Тернопіль, просп. Паторжинського, 25'),(26,12,'SD','South Monatown','shire','97928 Smitham Ridges\nPort Darionfort, SC 87235'),(27,12,'UZ','Keelingview','ton','713 Rolfson Corner Suite 189\nPort Westonport, KS 82294'),(28,13,'KH','Liashire','berg','9685 Jacobi Lake\nNorth Guiseppetown, WI 46415-0938'),(29,13,'MX','Stantonhaven','fort','71890 Katelyn Pine Apt. 858\nLake Jeffereybury, IL 44516'),(30,14,'MP','Cyrilton','chester','4461 Schumm Via Suite 102\nSouth Haleyland, MI 50254-1233'),(31,14,'VC','Gislasonstad','mouth','2566 Gavin Station\nEast Evelyn, TX 88207-4896'),(32,15,'FR','Харків','','82457, Запорізька область, місто Запоріжжя, вул. М. Коцюбинського, 41'),(33,16,'VC','Клин','Ville','708410, Владимирская область, город Орехово-Зуево, бульвар Чехова, 32'),(34,16,'JO','Видное','Ville','203471, Липецкая область, город Серпухов, пл. Сталина, 83'),(35,17,'IS','Шаховская','Ville','629353, Московская область, город Чехов, шоссе Гагарина, 43'),(36,17,'AI','Красногорск','Ville','215169, Саратовская область, город Волоколамск, наб. Косиора, 33'),(37,18,'CV','Одеса','','43269, Запорізька область, місто Запоріжжя, просп. П. Орлика, 11'),(38,19,'BO','Донецьк','','57178, Закарпатська область, місто Ужгород, пл. Інститутська, 57'),(39,20,'FK','Донецьк','','63878, Тернопільська область, місто Тернопіль, вул. Солом’янська, 58');
/*!40000 ALTER TABLE `address` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `categoryName` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES (1,'Cars'),(2,'Jewelry'),(3,'Furniture'),(4,'Apartments'),(5,'Vehicles'),(6,'Houses'),(7,'Luxury');
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer`
--

DROP TABLE IF EXISTS `customer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstName` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastName` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `isOrganization` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer`
--

LOCK TABLES `customer` WRITE;
/*!40000 ALTER TABLE `customer` DISABLE KEYS */;
INSERT INTO `customer` VALUES (1,'Изольда','Блохин',0),(2,'Трофим','Макаров',0),(3,'Hessel, Kshlerin and Rau',NULL,1),(4,'Blaze','Wunsch',0),(5,'Koss, Emmerich and Pfannerstill',NULL,1),(6,'ПАО Рыб',NULL,1),(7,'Kiera','VonRueden',0),(8,'Dalton','White',0),(9,'Егор','Ефремова',0),(10,'Вадим','Петренко',0),(11,'Кирил','Крамаренко',0),(12,'Kuhic, Johns and Upton',NULL,1),(13,'Isom','McGlynn',0),(14,'Schowalter Ltd',NULL,1),(15,'Маргарита','Крамаренко',0),(16,'ПАО Текстиль',NULL,1),(17,'МКК МясГлав',NULL,1),(18,'ТОВ \"Едельвейс-Дизайн\"',NULL,1),(19,'Анастасія','Васильчук',0),(20,'Галина','Василенко',0);
/*!40000 ALTER TABLE `customer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order`
--

DROP TABLE IF EXISTS `order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) DEFAULT NULL,
  `status` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `orderedAt` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `deliveredAt` datetime DEFAULT NULL,
  `deliveryAddress_id` int(11) DEFAULT NULL,
  `billingAddress_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F52993989395C3F3` (`customer_id`),
  KEY `IDX_F5299398B313412A` (`deliveryAddress_id`),
  KEY `IDX_F529939843656FE6` (`billingAddress_id`),
  CONSTRAINT `FK_F529939843656FE6` FOREIGN KEY (`billingAddress_id`) REFERENCES `address` (`id`),
  CONSTRAINT `FK_F52993989395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`),
  CONSTRAINT `FK_F5299398B313412A` FOREIGN KEY (`deliveryAddress_id`) REFERENCES `address` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order`
--

LOCK TABLES `order` WRITE;
/*!40000 ALTER TABLE `order` DISABLE KEYS */;
INSERT INTO `order` VALUES (1,1,'awaiting','2023-01-23 08:39:12',NULL,1,NULL),(2,1,'awaiting','2023-03-07 21:21:03',NULL,1,NULL),(3,2,'checkedout','2022-03-23 23:16:17',NULL,2,NULL),(4,2,'sent','2023-05-31 22:30:24',NULL,2,NULL),(5,2,'awaiting','2025-12-14 18:45:47',NULL,2,NULL),(6,2,'awaiting','2024-11-29 01:37:49',NULL,2,4),(7,3,'delivered','2024-03-21 06:05:02','2022-11-07 08:44:00',6,NULL),(8,3,'ordered','2023-05-20 23:50:43',NULL,5,5),(9,3,'delivered','2021-07-17 08:22:08','2021-07-07 05:04:01',5,NULL),(10,3,'checkedout','2025-10-22 18:14:23',NULL,5,6),(11,4,'delivered','2024-02-18 20:30:45','2021-12-09 12:59:31',7,NULL),(12,4,'delivered','2024-01-13 00:52:02','2022-05-11 03:31:31',7,7),(13,5,'cancelled','2023-05-05 15:33:03',NULL,8,8),(14,5,'ordered','2022-12-28 03:48:06',NULL,8,8),(15,6,'cancelled','2022-01-13 13:06:27',NULL,9,NULL),(16,6,'ordered','2021-06-26 08:09:57',NULL,9,9),(17,6,'awaiting','2021-05-22 00:39:48',NULL,9,9),(18,7,'sent','2024-12-29 18:30:31',NULL,11,NULL),(19,7,'delivered','2025-05-10 07:28:12','2025-03-06 00:41:02',12,11),(20,8,'ordered','2022-06-24 00:08:50',NULL,16,15),(21,8,'cancelled','2025-11-20 04:46:06',NULL,15,NULL),(22,9,'awaiting','2022-07-19 08:50:00',NULL,19,NULL),(23,10,'ordered','2024-10-02 20:59:40',NULL,21,20),(24,10,'checkedout','2023-05-04 22:44:30',NULL,20,22),(25,10,'cancelled','2023-08-19 09:42:28',NULL,20,NULL),(26,11,'sent','2021-09-11 11:42:54',NULL,25,25),(27,11,'sent','2021-12-06 21:58:17',NULL,25,23),(28,11,'awaiting','2021-08-29 02:48:32',NULL,24,23),(29,11,'cancelled','2026-02-23 22:46:56',NULL,23,NULL),(30,12,'delivered','2024-06-19 21:55:31','2022-05-26 11:48:42',26,26),(31,12,'checkedout','2022-01-05 14:03:14',NULL,26,27),(32,12,'delivered','2022-03-07 12:56:51','2021-08-28 17:07:54',26,26),(33,13,'cancelled','2026-03-06 08:20:14',NULL,28,28),(34,13,'awaiting','2025-10-14 16:57:08',NULL,29,29),(35,13,'delivered','2021-06-30 17:07:01','2021-06-11 18:46:38',29,28),(36,14,'sent','2026-03-09 11:20:58',NULL,30,30),(37,14,'awaiting','2025-03-21 02:35:59',NULL,30,31),(38,15,'delivered','2021-06-20 06:28:55','2021-06-13 15:01:23',32,32),(39,16,'ordered','2023-06-12 19:49:40',NULL,33,33),(40,16,'ordered','2023-07-13 19:20:01',NULL,34,NULL),(41,17,'delivered','2021-10-26 18:23:07','2021-09-05 13:57:35',36,35),(42,17,'sent','2024-09-01 08:16:50',NULL,35,35),(43,17,'cancelled','2021-08-05 19:57:35',NULL,35,NULL),(44,18,'sent','2024-09-03 01:43:03',NULL,37,37),(45,18,'cancelled','2025-12-28 00:21:47',NULL,37,NULL),(46,19,'awaiting','2025-10-03 17:17:00',NULL,38,NULL),(47,19,'ordered','2025-10-26 06:00:50',NULL,38,NULL),(48,20,'cancelled','2022-05-15 18:21:49',NULL,39,NULL),(49,20,'ordered','2022-06-19 13:06:41',NULL,39,39);
/*!40000 ALTER TABLE `order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_item`
--

DROP TABLE IF EXISTS `order_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `unitCount` int(11) NOT NULL,
  `notes` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_52EA1F094584665A` (`product_id`),
  KEY `IDX_52EA1F098D9F6D38` (`order_id`),
  CONSTRAINT `FK_52EA1F094584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`),
  CONSTRAINT `FK_52EA1F098D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `order` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=128 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_item`
--

LOCK TABLES `order_item` WRITE;
/*!40000 ALTER TABLE `order_item` DISABLE KEYS */;
INSERT INTO `order_item` VALUES (1,5,1,3,'I\'m pleased, and wag my tail when it\'s pleased. Now I growl when I\'m pleased, and wag my tail when I\'m angry. Therefore I\'m mad.\' \'I call it sad?\' And she squeezed herself up on to the waving of the.'),(2,16,1,11,'Caterpillar. Alice folded her hands, and was beating her violently with its mouth and began bowing to the Gryphon. \'I mean, what makes them so often, you know.\' \'I don\'t see,\' said the Caterpillar.'),(3,1,2,19,'The hedgehog was engaged in a moment like a wild beast, screamed \'Off with her head! Off--\' \'Nonsense!\' said Alice, \'it\'s very easy to know when the tide rises and sharks are around, His voice has a.'),(4,14,2,5,'Dormouse began in a hoarse growl, \'the world would go round a deal too flustered to tell its age, there was nothing so VERY much out of a large plate came skimming out, straight at the Queen.'),(5,7,3,8,'March Hare. \'It was a most extraordinary noise going on rather better now,\' she said, \'and see whether it\'s marked \"poison\" or not\'; for she thought, and it set to work shaking him and punching him.'),(6,6,3,19,'Duchess by this time, as it went. So she was going off into a doze; but, on being pinched by the time it vanished quite slowly, beginning with the Mouse was swimming away from him, and said nothing.'),(7,5,3,6,'The Mock Turtle replied; \'and then the different branches of Arithmetic--Ambition, Distraction, Uglification, and Derision.\' \'I never saw one, or heard of one,\' said Alice. \'Of course you know the.'),(8,16,4,3,'Gryphon, \'you first form into a large dish of tarts upon it: they looked so good, that it might appear to others that what you like,\' said the King. (The jury all brightened up again.) \'Please your.'),(9,4,4,13,'Alice dear!\' said her sister; \'Why, what a Mock Turtle is.\' \'It\'s the Cheshire Cat, she was now more than that, if you wouldn\'t have come here.\' Alice didn\'t think that will be the right word).'),(10,10,5,15,'I want to see if she could do to come before that!\' \'Call the next verse.\' \'But about his toes?\' the Mock Turtle at last, more calmly, though still sobbing a little irritated at the window.\' \'THAT.'),(11,8,5,10,'I am! But I\'d better take him his fan and gloves, and, as a lark, And will talk in contemptuous tones of her sharp little chin into Alice\'s head. \'Is that all?\' said Alice, who always took a great.'),(12,10,5,16,'Alice looked down at her hands, and was going a journey, I should think you\'ll feel it a bit, if you want to go down the chimney?--Nay, I shan\'t! YOU do it!--That I won\'t, then!--Bill\'s to go on.'),(13,11,6,18,'Hatter. \'I deny it!\' said the Rabbit noticed Alice, as she was peering about anxiously among the trees upon her knee, and looking at the Caterpillar\'s making such VERY short remarks, and she soon.'),(14,8,6,14,'Alice like the Queen?\' said the March Hare will be much the same thing with you,\' said the Cat. \'Do you take me for a great hurry. An enormous puppy was looking about for a moment to be a walrus or.'),(15,18,7,14,'She said it to his ear. Alice considered a little door was shut again, and Alice was very nearly in the book,\' said the Pigeon had finished. \'As if it wasn\'t very civil of you to death.\"\' \'You are.'),(16,1,8,14,'So you see, as they came nearer, Alice could speak again. The rabbit-hole went straight on like a thunderstorm. \'A fine day, your Majesty!\' the Duchess was VERY ugly; and secondly, because they\'re.'),(17,20,8,6,'The first question of course you know the song, she kept on good terms with him, he\'d do almost anything you liked with the Queen,\' and she felt that it led into the sky. Alice went timidly up to.'),(18,15,9,15,'Caterpillar sternly. \'Explain yourself!\' \'I can\'t help it,\' she thought, \'and hand round the court and got behind him, and said to the Mock Turtle, and said nothing. \'When we were little,\' the Mock.'),(19,5,9,16,'I think it so quickly that the reason is--\' here the conversation a little. \'\'Tis so,\' said the King exclaimed, turning to Alice. \'Nothing,\' said Alice. \'Of course you know why it\'s called a.'),(20,2,9,14,'Mock Turtle said: \'no wise fish would go through,\' thought poor Alice, \'it would have appeared to them she heard one of its mouth again, and that\'s all the way I want to go! Let me see--how IS it to.'),(21,15,9,16,'YOUR table,\' said Alice; \'living at the March Hare: she thought it over a little bit of mushroom, and crawled away in the window, and some were birds,) \'I suppose they are the jurors.\' She said the.'),(22,17,10,3,'Let me see: four times six is thirteen, and four times seven is--oh dear! I shall think nothing of the tail, and ending with the next verse,\' the Gryphon went on. \'Or would you like the right.'),(23,19,10,19,'King said gravely, \'and go on crying in this way! Stop this moment, and fetch me a pair of gloves and a piece of evidence we\'ve heard yet,\' said Alice; \'that\'s not at all this time, and was going on.'),(24,14,10,19,'Duchess\'s voice died away, even in the world go round!\"\' \'Somebody said,\' Alice whispered, \'that it\'s done by everybody minding their own business!\' \'Ah, well! It means much the same age as herself.'),(25,6,11,3,'Alice opened the door and found that her neck from being broken. She hastily put down yet, before the trial\'s over!\' thought Alice. \'I\'ve tried the roots of trees, and I\'ve tried to fancy to cats if.'),(26,17,11,5,'Mock Turtle would be a very decided tone: \'tell her something about the same thing, you know.\' \'Not at all,\' said Alice: \'--where\'s the Duchess?\' \'Hush! Hush!\' said the Gryphon replied rather.'),(27,18,11,15,'Normans--\" How are you thinking of?\' \'I beg your pardon!\' cried Alice in a moment that it might happen any minute, \'and then,\' thought Alice, and, after folding his arms and frowning at the March.'),(28,9,11,1,'White Rabbit: it was all very well to say \"HOW DOTH THE LITTLE BUSY BEE,\" but it puzzled her very earnestly, \'Now, Dinah, tell me the list of the edge of her head made her feel very queer indeed.'),(29,8,12,9,'ME, and told me he was gone, and, by the fire, licking her paws and washing her face--and she is such a thing I know. Silence all round, if you please! \"William the Conqueror, whose cause was.'),(30,16,13,18,'An obstacle that came between Him, and ourselves, and it. Don\'t let him know she liked them best, For this must ever be A secret, kept from all the jelly-fish out of sight: then it chuckled. \'What.'),(31,1,13,13,'Alice again. \'No, I give you fair warning,\' shouted the Queen was silent. The King looked anxiously over his shoulder as he found it made Alice quite hungry to look at the door-- Pray, what is the.'),(32,5,13,17,'Hatter continued, \'in this way:-- \"Up above the world go round!\"\' \'Somebody said,\' Alice whispered, \'that it\'s done by everybody minding their own business!\' \'Ah, well! It means much the most.'),(33,20,14,18,'Shakespeare, in the sky. Twinkle, twinkle--\"\' Here the Queen shrieked out. \'Behead that Dormouse! Turn that Dormouse out of the Lobster Quadrille?\' the Gryphon hastily. \'Go on with the strange.'),(34,1,14,10,'Alice quietly said, just as she could not make out that one of them even when they arrived, with a round face, and large eyes full of tears, \'I do wish they COULD! I\'m sure she\'s the best of.'),(35,9,15,14,'I am! But I\'d better take him his fan and the other side of the trees under which she had brought herself down to nine inches high. CHAPTER VI. Pig and Pepper For a minute or two, looking for them.'),(36,6,15,3,'How puzzling all these changes are! I\'m never sure what I\'m going to turn into a large plate came skimming out, straight at the number of bathing machines in the pictures of him), while the Mock.'),(37,13,15,20,'Queen, pointing to the table, half hoping she might find another key on it, and fortunately was just possible it had no pictures or conversations?\' So she was playing against herself, for she had.'),(38,16,15,2,'IN the well,\' Alice said very politely, \'if I had it written down: but I grow at a king,\' said Alice. \'Then you shouldn\'t talk,\' said the Dodo replied very solemnly. Alice was beginning very.'),(39,7,16,11,'I\'ll get into her eyes; and once again the tiny hands were clasped upon her knee, and looking at the time she had made the whole party look so grave that she hardly knew what she was trying to fix.'),(40,5,16,13,'Bill!\' then the Mock Turtle Soup is made from,\' said the Queen, and Alice, were in custody and under sentence of execution. Then the Queen in a low voice, to the croquet-ground. The other guests had.'),(41,19,17,20,'So they couldn\'t get them out again. That\'s all.\' \'Thank you,\' said the Hatter. \'Stolen!\' the King said to herself, as she went on, \'if you don\'t even know what \"it\" means well enough, when I.'),(42,8,17,2,'And she tried to fancy to cats if you cut your finger VERY deeply with a T!\' said the White Rabbit, who said in a piteous tone. And the executioner went off like an honest man.\' There was not.'),(43,9,18,6,'Gryphon: and it set to work shaking him and punching him in the middle. Alice kept her waiting!\' Alice felt a very curious sensation, which puzzled her very much pleased at having found out a box of.'),(44,19,18,20,'Hatter. \'Does YOUR watch tell you just now what the moral of THAT is--\"Take care of themselves.\"\' \'How fond she is such a capital one for catching mice--oh, I beg your acceptance of this rope--Will.'),(45,13,18,17,'Mock Turtle persisted. \'How COULD he turn them out with trying, the poor child, \'for I can\'t get out of its voice. \'Back to land again, and she had read several nice little histories about children.'),(46,17,19,10,'COULD! I\'m sure _I_ shan\'t be able! I shall have some fun now!\' thought Alice. The King laid his hand upon her face. \'Wake up, Dormouse!\' And they pinched it on both sides at once. \'Give your.'),(47,2,19,7,'Bill! I wouldn\'t be so stingy about it, you know.\' \'I DON\'T know,\' said the King, \'that saves a world of trouble, you know, this sort of circle, (\'the exact shape doesn\'t matter,\' it said,) and then.'),(48,3,19,7,'Alice)--\'and perhaps you were down here till I\'m somebody else\"--but, oh dear!\' cried Alice in a moment. \'Let\'s go on for some time in silence: at last turned sulky, and would only say, \'I am older.'),(49,18,20,13,'ONE respectable person!\' Soon her eye fell upon a low trembling voice, \'--and I hadn\'t drunk quite so much!\' said Alice, \'a great girl like you,\' (she might well say this), \'to go on in a game of.'),(50,7,20,5,'Improve his shining tail, And pour the waters of the room. The cook threw a frying-pan after her as she ran; but the Hatter with a whiting. Now you know.\' \'I DON\'T know,\' said Alice, a good deal on.'),(51,16,20,5,'Gryphon, sighing in his throat,\' said the Queen to-day?\' \'I should like it put more simply--\"Never imagine yourself not to be listening, so she took up the fan and gloves, and, as a lark, And will.'),(52,10,21,16,'And the muscular strength, which it gave to my right size: the next moment she felt certain it must be Mabel after all, and I don\'t want to get through the air! Do you think, at your age, it is.'),(53,15,21,15,'Alice led the way, was the White Rabbit, \'but it doesn\'t matter much,\' thought Alice, \'they\'re sure to make out what she was quite pleased to find her way through the little door, so she sat on.'),(54,9,21,3,'Caterpillar contemptuously. \'Who are YOU?\' said the Cat, as soon as there was nothing so VERY nearly at the end of half an hour or so there were a Duck and a Canary called out to the Caterpillar.'),(55,11,22,6,'I fancied that kind of authority among them, called out, \'First witness!\' The first witness was the Rabbit coming to look through into the jury-box, or they would die. \'The trial cannot proceed,\'.'),(56,19,22,12,'White Rabbit as he said to herself, (not in a helpless sort of mixed flavour of cherry-tart, custard, pine-apple, roast turkey, toffee, and hot buttered toast,) she very seldom followed it), and.'),(57,7,23,12,'Alice began in a hurried nervous manner, smiling at everything about her, to pass away the time. Alice had been for some time after the others. \'We must burn the house if it makes me grow smaller, I.'),(58,5,23,7,'Bill! I wouldn\'t be so easily offended, you know!\' The Mouse did not dare to laugh; and, as the Dormouse go on crying in this way! Stop this moment, and fetch me a good thing!\' she said to the.'),(59,6,23,2,'YOUR temper!\' \'Hold your tongue!\' said the Duchess; \'and that\'s why. Pig!\' She said the Hatter. \'I told you butter wouldn\'t suit the works!\' he added in an undertone.'),(60,20,23,13,'After a time she found herself lying on the bank, with her head!\' about once in a trembling voice:-- \'I passed by his garden, and marked, with one eye, How the Owl and the beak-- Pray how did you.'),(61,16,24,5,'Forty-two. ALL PERSONS MORE THAN A MILE HIGH TO LEAVE THE COURT.\' Everybody looked at it uneasily, shaking it every now and then at the end of the well, and noticed that the Mouse was speaking, and.'),(62,10,24,8,'Who would not join the dance. \'\"What matters it how far we go?\" his scaly friend replied. \"There is another shore, you know, with oh, such long curly brown hair! And it\'ll fetch things when you.'),(63,15,24,7,'The Hatter was the BEST butter, you know.\' \'And what are they made of?\' \'Pepper, mostly,\' said the Caterpillar. \'Is that the hedgehog had unrolled itself, and was going to dive in among the people.'),(64,4,24,3,'There was exactly one a-piece all round. (It was this last remark, \'it\'s a vegetable. It doesn\'t look like it?\' he said, \'on and off, for days and days.\' \'But what did the archbishop find?\' The.'),(65,10,25,19,'Alice gently remarked; \'they\'d have been ill.\' \'So they were,\' said the Eaglet. \'I don\'t see how the game was going to shrink any further: she felt that it was a queer-shaped little creature, and.'),(66,2,25,3,'She had just succeeded in bringing herself down to them, and it\'ll sit up and walking away. \'You insult me by talking such nonsense!\' \'I didn\'t know how to begin.\' For, you see, Miss, this here.'),(67,3,25,12,'Queen, pointing to the tarts on the second time round, she found herself safe in a minute, while Alice thought over all the other players, and shouting \'Off with her head impatiently; and, turning.'),(68,20,26,19,'CHAPTER VI. Pig and Pepper For a minute or two she walked off, leaving Alice alone with the end of your nose-- What made you so awfully clever?\' \'I have answered three questions, and that he had.'),(69,15,26,7,'As they walked off together. Alice was not here before,\' said Alice,) and round goes the clock in a deep sigh, \'I was a good deal to ME,\' said Alice aloud, addressing nobody in particular. \'She\'d.'),(70,5,26,19,'Dodo said, \'EVERYBODY has won, and all her coaxing. Hardly knowing what she was coming back to yesterday, because I was a queer-shaped little creature, and held it out to the conclusion that it made.'),(71,4,27,20,'Hatter added as an explanation. \'Oh, you\'re sure to do THAT in a few minutes that she did not feel encouraged to ask the question?\' said the King, \'that only makes the matter worse. You MUST have.'),(72,7,27,13,'I ever saw in my size; and as Alice could hardly hear the rattle of the teacups as the Caterpillar contemptuously. \'Who are YOU?\' said the Caterpillar sternly. \'Explain yourself!\' \'I can\'t go no.'),(73,14,28,15,'Rabbit angrily. \'Here! Come and help me out of sight, he said in a sort of knot, and then hurried on, Alice started to her great delight it fitted! Alice opened the door as you are; secondly.'),(74,13,28,2,'I to get her head made her feel very sleepy and stupid), whether the pleasure of making a daisy-chain would be as well say this), \'to go on for some time without interrupting it. \'They must go by.'),(75,3,28,9,'I could say if I must, I must,\' the King triumphantly, pointing to the Mock Turtle a little bit of the sense, and the Panther were sharing a pie--\' [later editions continued as follows The Panther.'),(76,10,28,17,'Alice: \'allow me to introduce it.\' \'I don\'t know much,\' said the last few minutes to see what was going to begin lessons: you\'d only have to turn into a butterfly, I should frighten them out of.'),(77,6,29,15,'Caterpillar angrily, rearing itself upright as it spoke (it was exactly the right way of keeping up the other, trying every door, she found she had not attended to this mouse? Everything is so.'),(78,16,29,1,'So she set to work, and very soon came upon a Gryphon, lying fast asleep in the shade: however, the moment he was going to dive in among the trees as well go in ringlets at all; and I\'m sure I don\'t.'),(79,9,29,18,'ARE you talking to?\' said the Mock Turtle, and said to herself; \'the March Hare and the March Hare. \'Exactly so,\' said Alice. The King looked anxiously round, to make out what it was: at first was.'),(80,18,30,18,'Alice, and she put her hand in hand, in couples: they were IN the well,\' Alice said to herself how she would have made a snatch in the world am I? Ah, THAT\'S the great wonder is, that I\'m doubtful.'),(81,10,30,7,'Alice. \'I\'ve so often read in the court!\' and the King replied. Here the other birds tittered audibly. \'What I was a general chorus of voices asked. \'Why, SHE, of course,\' he said to itself \'The.'),(82,13,30,8,'I should think you\'ll feel it a violent shake at the door-- Pray, what is the same side of the water, and seemed to her that she wasn\'t a bit of stick, and tumbled head over heels in its sleep.'),(83,13,30,14,'I\'ll get into the darkness as hard as he could think of nothing else to do, and perhaps as this before, never! And I declare it\'s too bad, that it ought to speak, and no one listening, this time, as.'),(84,17,31,19,'Pigeon in a game of play with a sigh: \'it\'s always tea-time, and we\'ve no time to wash the things being alive; for instance, there\'s the arch I\'ve got to come upon them THIS size: why, I should be.'),(85,3,31,2,'White Rabbit with pink eyes ran close by it, and found in it about four feet high. \'I wish I could not swim. He sent them word I had not a moment like a writing-desk?\' \'Come, we shall get on.'),(86,4,31,13,'Rabbit-Hole Alice was not quite sure whether it was all dark overhead; before her was another puzzling question; and as Alice could see her after the birds! Why, she\'ll eat a little scream, half of.'),(87,20,31,2,'Alice thought she might find another key on it, and they went on in a trembling voice, \'Let us get to the dance. Would not, could not, would not, could not, would not open any of them. However, on.'),(88,20,32,3,'Gryphon. \'I\'ve forgotten the words.\' So they got settled down in a great deal to ME,\' said the Hatter, and, just as I do,\' said Alice indignantly. \'Let me alone!\' \'Serpent, I say again!\' repeated.'),(89,2,32,12,'The first thing she heard one of the evening, beautiful Soup! \'Beautiful Soup! Who cares for you?\' said the Cat. \'--so long as you might catch a bat, and that\'s very like a tunnel for some time.'),(90,5,32,5,'HAVE tasted eggs, certainly,\' said Alice to herself. \'I dare say you\'re wondering why I don\'t think,\' Alice went on at last, and managed to swallow a morsel of the shepherd boy--and the sneeze of.'),(91,19,33,14,'Majesty,\' said the one who had followed him into the roof off.\' After a time she had quite a new idea to Alice, they all looked puzzled.) \'He must have a trial: For really this morning I\'ve nothing.'),(92,13,34,19,'I\'ll try and say \"How doth the little door: but, alas! the little door, so she went on in a voice of the cattle in the house, and have next to her. \'I can tell you what year it is?\' \'Of course not,\'.'),(93,3,34,18,'THIS witness.\' \'Well, if I must, I must,\' the King said to the door. \'Call the next witness!\' said the Mock Turtle, capering wildly about. \'Change lobsters again!\' yelled the Gryphon in an offended.'),(94,16,34,6,'I suppose.\' So she began nibbling at the righthand bit again, and Alice thought decidedly uncivil. \'But perhaps it was all dark overhead; before her was another long passage, and the sounds will.'),(95,7,34,19,'BE TRUE--\" that\'s the jury-box,\' thought Alice, \'to pretend to be a comfort, one way--never to be otherwise than what it was: she was about a foot high: then she walked on in these words: \'Yes, we.'),(96,7,35,9,'Tarts? The King and Queen of Hearts, he stole those tarts, And took them quite away!\' \'Consider your verdict,\' he said to Alice, they all stopped and looked at it gloomily: then he dipped it into.'),(97,1,35,17,'Queen till she fancied she heard a little anxiously. \'Yes,\' said Alice, as she spoke; \'either you or your head must be kind to them,\' thought Alice, \'and if it had lost something; and she said to.'),(98,6,36,5,'The jury all brightened up at the cook till his eyes were looking up into hers--she could hear him sighing as if he had come back with the next verse.\' \'But about his toes?\' the Mock Turtle: \'crumbs.'),(99,1,37,4,'Gryphon went on. \'Or would you tell me,\' said Alice, who felt very lonely and low-spirited. In a little timidly, for she could not tell whether they were nice grand words to say.) Presently she.'),(100,17,37,2,'The judge, by the Queen had only one way up as the White Rabbit; \'in fact, there\'s nothing written on the door began sneezing all at once. The Dormouse shook itself, and was looking about for some.'),(101,6,38,12,'Mock Turtle Soup is made from,\' said the Mock Turtle went on. \'I do,\' Alice hastily replied; \'at least--at least I mean what I see\"!\' \'You might just as she swam lazily about in the beautiful.'),(102,9,38,16,'Alice heard it muttering to himself in an offended tone. And she began again: \'Ou est ma chatte?\' which was full of tears, \'I do wish they WOULD put their heads downward! The Antipathies, I think--\'.'),(103,18,39,19,'Queen, who were giving it a very short time the Queen was to eat or drink anything; so I\'ll just see what this bottle was a good many voices all talking at once, she found herself lying on their.'),(104,18,39,10,'Gryphon, \'you first form into a pig, and she looked down into its mouth open, gazing up into the Dormouse\'s place, and Alice was silent. The Dormouse had closed its eyes by this time, and was going.'),(105,6,40,15,'I think I should say \"With what porpoise?\"\' \'Don\'t you mean that you never to lose YOUR temper!\' \'Hold your tongue!\' said the Hatter. \'It isn\'t mine,\' said the Cat, \'a dog\'s not mad. You grant.'),(106,10,41,16,'YOU, and no room to open them again, and all would change to dull reality--the grass would be quite as safe to stay in here any longer!\' She waited for a few minutes that she was talking. \'How CAN I.'),(107,10,42,11,'ONE respectable person!\' Soon her eye fell on a little way off, and she could see, as well as I get SOMEWHERE,\' Alice added as an explanation; \'I\'ve none of them hit her in such a new idea to Alice.'),(108,6,42,11,'Alice sharply, for she had got to do,\' said the Gryphon: and Alice could not even room for her. \'I wish I could shut up like telescopes: this time she saw maps and pictures hung upon pegs. She took.'),(109,11,43,4,'King said to herself in the morning, just time to be two people! Why, there\'s hardly enough of it now in sight, and no more to come, so she felt sure it would all come wrong, and she swam about.'),(110,13,43,17,'What would become of me?\' Luckily for Alice, the little door about fifteen inches high: she tried hard to whistle to it; but she knew the meaning of it altogether; but after a few minutes, and she.'),(111,20,44,19,'She was moving them about as much use in waiting by the Hatter, \'or you\'ll be telling me next that you have just been picked up.\' \'What\'s in it?\' said the Caterpillar. \'Well, I\'ve tried hedges,\' the.'),(112,14,44,12,'Alice with one finger; and the Dormouse began in a very little! Besides, SHE\'S she, and I\'m sure _I_ shan\'t be beheaded!\' \'What for?\' said the Cat, \'or you wouldn\'t keep appearing and vanishing so.'),(113,8,44,5,'ONE with such sudden violence that Alice had not gone much farther before she had drunk half the bottle, she found that her idea of having the sentence first!\' \'Hold your tongue!\' added the March.'),(114,18,45,20,'NOT be an advantage,\' said Alice, \'because I\'m not used to it in the sea, some children digging in the world she was now only ten inches high, and she was quite pleased to find her in a melancholy.'),(115,18,45,10,'Alice could see this, as she stood watching them, and he says it\'s so useful, it\'s worth a hundred pounds! He says it kills all the jurymen on to her great disappointment it was over at last: \'and I.'),(116,20,45,15,'King. \'Nearly two miles high,\' added the Dormouse, and repeated her question. \'Why did you ever see such a hurry to change the subject. \'Ten hours the first sentence in her pocket, and pulled out a.'),(117,2,46,9,'I can\'t remember,\' said the Caterpillar seemed to think that proved it at all; and I\'m I, and--oh dear, how puzzling it all came different!\' the Mock Turtle. Alice was beginning to end,\' said the.'),(118,3,46,15,'I\'ll go round and swam slowly back again, and made another rush at Alice the moment how large she had forgotten the little golden key, and Alice\'s elbow was pressed so closely against her foot, that.'),(119,16,47,2,'Some of the hall: in fact she was ever to get rather sleepy, and went to the whiting,\' said the Queen, the royal children; there were three little sisters,\' the Dormouse go on till you come to the.'),(120,17,47,9,'For instance, suppose it doesn\'t mind.\' The table was a very long silence, broken only by an occasional exclamation of \'Hjckrrh!\' from the sky! Ugh, Serpent!\' \'But I\'m not Ada,\' she said, by way of.'),(121,14,47,7,'I wish you wouldn\'t mind,\' said Alice: \'besides, that\'s not a VERY unpleasant state of mind, she turned the corner, but the Mouse replied rather impatiently: \'any shrimp could have been changed for.'),(122,12,47,1,'White Rabbit, \'but it sounds uncommon nonsense.\' Alice said nothing; she had gone through that day. \'A likely story indeed!\' said the King. Here one of these cakes,\' she thought, and looked at.'),(123,9,48,9,'And the moral of that is--\"Birds of a candle is blown out, for she was in the middle. Alice kept her eyes to see the earth takes twenty-four hours to turn into a doze; but, on being pinched by the.'),(124,5,48,9,'I don\'t believe there\'s an atom of meaning in it.\' The jury all looked so good, that it was the matter with it. There was a good thing!\' she said this she looked down at them, and the King said, for.'),(125,9,48,19,'There ought to be Number One,\' said Alice. \'Exactly so,\' said the King: \'however, it may kiss my hand if it thought that she wasn\'t a bit hurt, and she felt sure it would be quite as much as.'),(126,18,49,1,'Queen. \'Can you play croquet with the Queen said--\' \'Get to your tea; it\'s getting late.\' So Alice began in a thick wood. \'The first thing she heard the Queen ordering off her head!\' Those whom she.'),(127,11,49,3,'YOU like cats if you were down here with me! There are no mice in the air: it puzzled her very much pleased at having found out that the hedgehog a blow with its eyelids, so he with his head!\' she.');
/*!40000 ALTER TABLE `order_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `productName` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `inPromotion` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `registeredAt` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product`
--

LOCK TABLES `product` WRITE;
/*!40000 ALTER TABLE `product` DISABLE KEYS */;
INSERT INTO `product` VALUES (1,'est iste molestiae','1','2026-01-17 16:09:10'),(2,'voluptas quibusdam amet','1','2025-03-19 14:02:30'),(3,'iste aut aliquam','1','2024-04-04 11:34:54'),(4,'rerum a ut','1','2024-06-23 01:49:10'),(5,'dolores excepturi rem','1','2025-03-24 05:20:39'),(6,'velit et laudantium','','2025-03-07 03:36:47'),(7,'maiores sint quis','','2026-01-30 17:15:54'),(8,'eligendi velit aut','1','2024-11-08 07:02:44'),(9,'quod id dolorem','1','2026-01-20 17:03:47'),(10,'veritatis eveniet omnis','1','2025-05-04 07:33:13'),(11,'mollitia laudantium beatae','','2025-09-03 02:36:29'),(12,'aliquid at reprehenderit','1','2024-12-14 04:52:01'),(13,'est vero sed','1','2026-01-24 00:41:05'),(14,'qui recusandae eos','1','2025-05-16 21:57:39'),(15,'cupiditate laborum voluptatibus','','2025-11-28 03:15:37'),(16,'at et optio','1','2022-07-24 09:54:43'),(17,'quibusdam et vero','1','2023-03-10 22:10:57'),(18,'est voluptas deserunt','1','2022-01-22 20:32:07'),(19,'aut quidem nulla','','2023-03-06 04:01:57'),(20,'sed numquam aut','1','2021-10-19 03:32:00');
/*!40000 ALTER TABLE `product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_category`
--

DROP TABLE IF EXISTS `product_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_category` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`product_id`,`category_id`),
  KEY `IDX_CDFC73564584665A` (`product_id`),
  KEY `IDX_CDFC735612469DE2` (`category_id`),
  CONSTRAINT `FK_CDFC735612469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_CDFC73564584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_category`
--

LOCK TABLES `product_category` WRITE;
/*!40000 ALTER TABLE `product_category` DISABLE KEYS */;
INSERT INTO `product_category` VALUES (1,4),(1,7),(2,3),(3,5),(3,6),(3,7),(4,5),(5,3),(5,5),(6,5),(7,2),(7,6),(7,7),(8,1),(8,4),(9,1),(9,2),(10,3),(10,4),(10,5),(11,2),(11,5),(12,2),(13,7),(14,5),(15,2),(15,6),(16,2),(16,6),(16,7),(17,1),(17,5),(17,6),(18,7),(19,3),(19,7),(20,2);
/*!40000 ALTER TABLE `product_category` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-03-28 16:20:33
