INSERT INTO `user` VALUES (1,'admin','d033e22ae348aeb5660fc2140aec35850c4da997','admin','juanma.aguero@gmail.com','admin'),(2,'juanma','06a7d69a0f4729ee1b5a8978fc04d75388c13448','user','jaguero@flowcode.com.ar','juanma');

INSERT INTO `item_menu` VALUES (1,'Home',1,0,0,'/home',0,''),(2,'Sample',1,0,0,'',1,''),(3,'blog',1,0,0,'/blog',4,''),(22,'sample page<br>',1,2,0,'/sample-page',3,''),(21,'sample child 2<br>',1,2,0,'/home',2,'');

INSERT INTO menu (id, `name`) VALUES
(1, 'main-menu');

INSERT INTO `permission` VALUES (1,'admin-login'),(2,'admin-create'),(3,'admin-edit'),(4,'admin-delete'),(33,'admin-blog-post-create'),(34,'admin-blog-post-edit'),(36,'admin-blog-post-delete'),(37,'admin-user-create'),(39,'admin-user-edit'),(42,'admin-user-delete'),(46,'admin-menu-create'),(47,'admin-menu-update'),(49,'admin-menu-delete'),(50,'admin-page-create'),(51,'admin-page-edit'),(52,'admin-page-delete');

INSERT INTO `role` VALUES (3,'admin'),(8,'blogger');

INSERT INTO `user_role` VALUES (1,2),(1,3),(4,8);

INSERT INTO `role_permission` VALUES (3,1),(3,2),(3,3),(3,4),(3,33),(3,34),(3,36),(3,37),(3,39),(3,42),(3,46),(3,47),(3,49),(3,50),(3,51),(3,52),(8,1),(8,33),(8,34),(8,36);

INSERT INTO `tag` VALUES (1,'post tag'),(4,'deportes'),(3,'test');


INSERT INTO `page` VALUES (1,'home','home','Home description','<p>Sample page content</p>\r\n',1,100),(2,'sample-page','sample page','Sample Page','<p>Another Sample page content</p>\r\n',1,100),(3,'blog','blog','Blog description','',1,100);