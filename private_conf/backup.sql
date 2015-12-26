LOCK TABLES `Category` WRITE;
/*!40000 ALTER TABLE `Category` DISABLE KEYS */;
INSERT INTO `Category` VALUES (1,'Picos, Rosquillas y Regañá','picos-rosquillas-y-regana','2015-04-30 16:40:51','2015-04-30 16:40:51',NULL,0),(2,'Patatas y Pan de Molde','patatas-y-pan-de-molde','2015-04-30 16:40:59','2015-04-30 16:40:59',NULL,0),(3,'Otros Productos','otros-productos','2015-04-30 16:41:06','2015-04-30 16:41:06',NULL,0),(4,'Pastelería','pasteleria','2015-04-30 16:41:13','2015-04-30 16:41:13',NULL,0),(5,'Otros Proveedores','otros-proveedores','2015-04-30 16:41:21','2015-04-30 16:41:21',NULL,0);
/*!40000 ALTER TABLE `Category` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Dumping data for table `Subcategory`
--

LOCK TABLES `Subcategory` WRITE;
/*!40000 ALTER TABLE `Subcategory` DISABLE KEYS */;
INSERT INTO `Subcategory` VALUES (1,1,'El Cartujano','el-cartujano','2015-04-30 16:41:39','2015-04-30 16:41:39',NULL,0),(2,1,'Integrales','integrales','2015-04-30 16:41:48','2015-04-30 16:41:48',NULL,0),(3,1,'Hostelería','hosteleria','2015-04-30 16:41:57','2015-04-30 16:41:57',NULL,0),(4,1,'Otros Proveedores','otros-proveedores','2015-05-02 11:10:54','2015-04-30 16:42:06',NULL,0),(5,2,'Patatas','patatas','2015-04-30 16:42:19','2015-04-30 16:42:19',NULL,0),(6,2,'Pan de Molde','pan-de-molde','2015-04-30 16:42:27','2015-04-30 16:42:27',NULL,0),(7,3,'Altramuces','altramuces','2015-04-30 16:42:42','2015-04-30 16:42:42',NULL,0),(8,3,'Pimientos Asados','pimientos-asados','2015-04-30 16:42:48','2015-04-30 16:42:48',NULL,0),(9,3,'Varios','varios','2015-04-30 16:42:54','2015-04-30 16:42:54',NULL,0),(10,4,'Dulces El Cartujano','dulces-el-cartujano','2015-04-30 16:43:07','2015-04-30 16:43:07',NULL,0),(11,4,'El Cartujano Pastelería a 1€','el-cartujano-pasteleria-a-1','2015-04-30 16:43:15','2015-04-30 16:43:15',NULL,0),(12,4,'Pastelería a Granel','pasteleria-a-granel','2015-04-30 16:43:24','2015-04-30 16:43:24',NULL,0),(13,5,'Dulces Otros Proveedores','dulces-otros-proveedores','2015-05-03 17:49:30','2015-04-30 16:43:32',NULL,0),(14,5,'Otros a 1€','otros-a-1','2015-04-30 16:43:41','2015-04-30 16:43:41',NULL,0),(15,5,'Otros sin azúcar','otros-sin-azucar','2015-04-30 16:43:52','2015-04-30 16:43:52',NULL,0),(16,5,'Otros a Granel','otros-a-granel','2015-04-30 16:44:00','2015-04-30 16:44:00',NULL,0),(17,5,'Bollería','bolleria','2015-04-30 16:44:10','2015-04-30 16:44:10',NULL,0);
/*!40000 ALTER TABLE `Subcategory` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;