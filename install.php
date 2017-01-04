<?php
ini_set('max_execution_time', 3000);
require 'dbconn.php';

$sql = "CREATE TABLE IF NOT EXISTS users (
		user_id INT(8) NOT NULL AUTO_INCREMENT,
		user_uuid TEXT NOT NULL,
		user_username TEXT NOT NULL,
		user_password TEXT NOT NULL,
		user_email TEXT NOT NULL,
		user_group VARCHAR(255) NOT NULL DEFAULT 'Unlinked',
		user_class VARCHAR(255) NOT NULL DEFAULT 'Player',
		user_posts INT(8) NOT NULL,
		user_join_date VARCHAR(255) NOT NULL,
		user_remember_hash VARCHAR(255) NOT NULL,
		user_password_recover_hash VARCHAR(255) NOT NULL,
		PRIMARY KEY (user_id)
		) AUTO_INCREMENT=1";
$forumConn->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS groups (
		group_id INT(8) NOT NULL AUTO_INCREMENT,
		group_name TEXT NOT NULL,
		group_perm_level TEXT NOT NULL,
		group_icon TEXT NOT NULL,
		group_color TEXT NOT NULL,
		PRIMARY KEY (group_id)
		) AUTO_INCREMENT=1";
$forumConn->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS classes (
		class_id INT(8) NOT NULL AUTO_INCREMENT,
		class_name TEXT NOT NULL,
		class_color TEXT NOT NULL,
		PRIMARY KEY (class_id)
		) AUTO_INCREMENT=1";
$forumConn->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS categories (
		cat_id INT(8) NOT NULL AUTO_INCREMENT,
		cat_hash VARCHAR(255) NOT NULL,
		cat_name VARCHAR(255) NOT NULL,
		cat_description VARCHAR(255) NOT NULL,
		UNIQUE INDEX cat_name_unique (cat_name),
		PRIMARY KEY (cat_id)
		) AUTO_INCREMENT=1";
$forumConn->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS posts (
		post_id INT(8) NOT NULL AUTO_INCREMENT,
		post_hash VARCHAR(255) NOT NULL,
		post_content TEXT NOT NULL,
		post_date VARCHAR(255) NOT NULL,
		post_topic VARCHAR(255) NOT NULL,
		post_by VARCHAR(255) NOT NULL,
		PRIMARY KEY (post_id)
		) AUTO_INCREMENT=1";
$forumConn->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS threads (
		thread_id INT(8) NOT NULL AUTO_INCREMENT,
		thread_hash VARCHAR(255) NOT NULL,
		thread_name TEXT NOT NULL,
		thread_content TEXT NOT NULL,
		thread_date VARCHAR(255) NOT NULL,
		thread_topic VARCHAR(255) NOT NULL,
		thread_uuid VARCHAR(255) NOT NULL,
		thread_by VARCHAR(255) NOT NULL,
		thread_replies VARCHAR(255) NOT NULL DEFAULT '0',
		latest_post_by VARCHAR(255) NOT NULL,
		latest_post_date VARCHAR(255) NOT NULL,
		likes VARCHAR(255) NOT NULL DEFAULT '0',
		dislikes VARCHAR(255) NOT NULL DEFAULT '0',
		PRIMARY KEY (thread_id)
		) AUTO_INCREMENT=1";
$forumConn->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS replies (
		reply_id INT(8) NOT NULL AUTO_INCREMENT,
		reply_hash VARCHAR(255) NOT NULL,
		reply_content TEXT NOT NULL,
		reply_date VARCHAR(255) NOT NULL,
		reply_topic VARCHAR(255) NOT NULL,
		reply_by VARCHAR(255) NOT NULL,
		likes VARCHAR(255) NOT NULL DEFAULT '0',
		dislikes VARCHAR(255) NOT NULL DEFAULT '0',
		reply_uuid VARCHAR(255) NOT NULL,
		PRIMARY KEY (reply_id)
		) AUTO_INCREMENT=1";
$forumConn->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS notifications (
		notification_id INT(8) NOT NULL AUTO_INCREMENT,
		notification_for VARCHAR(255) NOT NULL,
		notification_from VARCHAR(255) NOT NULL,
		notification_content TEXT NOT NULL,
		notification_on TEXT NOT NULL,
		notification_read INT(8) NOT NULL,
		notification_type VARCHAR(255) NOT NULL,
		notification_date VARCHAR(255) NOT NULL,
		PRIMARY KEY (notification_id)
		) AUTO_INCREMENT=1";
$forumConn->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS dmessages (
		dmessage_id INT(8) NOT NULL AUTO_INCREMENT,
		dmessage_for VARCHAR(255) NOT NULL,
		dmessage_from VARCHAR(255) NOT NULL,
		dmessage_content TEXT NOT NULL,
		dmessage_on TEXT NOT NULL,
		dmessage_read INT(8) NOT NULL,
		dmessage_date TIMESTAMP NOT NULL,
		PRIMARY KEY (dmessage_id)
		) AUTO_INCREMENT=1";
$forumConn->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS ratings (
		rating_id INT(8) NOT NULL AUTO_INCREMENT,
		rating_for_uuid VARCHAR(255) NOT NULL,
		rating_for_type VARCHAR(255) NOT NULL,
		rating_from VARCHAR(255) NOT NULL,
		rating_value TEXT NOT NULL,
		PRIMARY KEY (rating_id)
		) AUTO_INCREMENT=1";
$forumConn->exec($sql);

$query = "INSERT INTO `groups` (`group_name`, `group_perm_level`, `group_icon`, `group_color`) VALUES (:group_name, :group_perm_level, :group_icon, :group_color)";
$prepare = $forumConn->prepare($query);
$prepare->bindValue(':group_name', "Unlinked");
$prepare->bindValue(':group_perm_level', "0");
$prepare->bindValue(':group_icon', "fa fa-times");
$prepare->bindValue(':group_color', "#000");

try {
    $add = $prepare->execute();
}
catch(PDOException $e) {
    die($e->getMessage());
}


$query = "INSERT INTO `groups` (`group_name`, `group_perm_level`, `group_icon`, `group_color`) VALUES (:group_name, :group_perm_level, :group_icon, :group_color)";
$prepare = $forumConn->prepare($query);
$prepare->bindValue(':group_name', "Default");
$prepare->bindValue(':group_perm_level', "1");
$prepare->bindValue(':group_icon', "fa fa-user");
$prepare->bindValue(':group_color', "#aaa");

try {
    $add = $prepare->execute();
}
catch(PDOException $e) {
    die($e->getMessage());
}


$query = "INSERT INTO `groups` (`group_name`, `group_perm_level`, `group_icon`, `group_color`) VALUES (:group_name, :group_perm_level, :group_icon, :group_color)";
$prepare = $forumConn->prepare($query);
$prepare->bindValue(':group_name', "Admin");
$prepare->bindValue(':group_perm_level', "2");
$prepare->bindValue(':group_icon', "fa fa-gavel");
$prepare->bindValue(':group_color', "#f55");

try {
    $add = $prepare->execute();
}
catch(PDOException $e) {
    die($e->getMessage());
}


$query = "INSERT INTO `groups` (`group_name`, `group_perm_level`, `group_icon`, `group_color`) VALUES (:group_name, :group_perm_level, :group_icon, :group_color)";
$prepare = $forumConn->prepare($query);
$prepare->bindValue(':group_name', "Banned");
$prepare->bindValue(':group_perm_level', "0");
$prepare->bindValue(':group_icon', "fa fa-user-times");
$prepare->bindValue(':group_color', "#a00");

try {
    $add = $prepare->execute();
}
catch(PDOException $e) {
    die($e->getMessage());
}


$query = "INSERT INTO `classes` (`class_name`, `class_color`) VALUES (:class_name, :class_color)";
$prepare = $forumConn->prepare($query);
$prepare->bindValue(':class_name', "Builder");
$prepare->bindValue(':class_color', "#a0a");

try {
    $add = $prepare->execute();
}
catch(PDOException $e) {
    die($e->getMessage());
}


$query = "INSERT INTO `classes` (`class_name`, `class_color`) VALUES (:class_name, :class_color)";
$prepare = $forumConn->prepare($query);
$prepare->bindValue(':class_name', "Redstoner");
$prepare->bindValue(':class_color', "#f55");

try {
    $add = $prepare->execute();
}
catch(PDOException $e) {
    die($e->getMessage());
}


$query = "INSERT INTO `classes` (`class_name`, `class_color`) VALUES (:class_name, :class_color)";
$prepare = $forumConn->prepare($query);
$prepare->bindValue(':class_name', "Player");
$prepare->bindValue(':class_color', "#aaa");

try {
    $add = $prepare->execute();
}
catch(PDOException $e) {
    die($e->getMessage());
}


$query = "INSERT INTO `classes` (`class_name`, `class_color`) VALUES (:class_name, :class_color)";
$prepare = $forumConn->prepare($query);
$prepare->bindValue(':class_name', "Owner");
$prepare->bindValue(':class_color', "#55f");

try {
    $add = $prepare->execute();
}
catch(PDOException $e) {
    die($e->getMessage());
}


$sql = "CREATE TABLE IF NOT EXISTS items (
		item_id INT(10) NOT NULL AUTO_INCREMENT,
		item_seller VARCHAR(255) NOT NULL,
		item_title VARCHAR(255) NOT NULL,
		item_type VARCHAR(255) NOT NULL,
		item_meta VARCHAR(255) NOT NULL,
		item_count INT(8) NOT NULL,
		item_cost INT(10) NOT NULL,
		item_nbt_ingame TEXT NOT NULL,
		item_search_query TEXT NOT NULL,
		PRIMARY KEY (item_id)
		) AUTO_INCREMENT=1";
$marketConn->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS donors (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		name VARCHAR(255),
		product VARCHAR(255),
		productAmount VARCHAR(255),
		productPrice DECIMAL(19,2) NOT NULL,
		purchase_date TIMESTAMP
		)";
$marketConn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS donorsTotal (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		name VARCHAR(255),
		total DECIMAL(19,2)
		)";
$marketConn->query($sql);

echo "Databases installed!";