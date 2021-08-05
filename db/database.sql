CREATE DATABASE IF NOT EXISTS shopbee
CHARACTER SET UTF8MB4
COLLATE utf8mb4_vietname_ci;
USE shopbee;


CREATE TABLE users (
	id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    hash_password VARCHAR(100) NOT NULL,
    fullname VARCHAR(100) NOT NULL DEFAULT '',
    email VARCHAR(150) NOT NULL UNIQUE,
    address VARCHAR(200) DEFAULT '',
    phone VARCHAR(13) DEFAULT '',
    role VARCHAR(10) DEFAULT 'user',
    avatar VARCHAR(200) DEFAULT 'https://i.ibb.co/BCX3q9q/57393124-364351517508038-8412224844044697600-n.png',
    created_at DATETIME NOT NULL DEFAULT now()
);

CREATE TABLE bills (
	id INT PRIMARY KEY AUTO_INCREMENT,
    created_at DATETIME NOT NULL DEFAULT now(),
    id_user INT NOT NULL,
    FOREIGN KEY (id_user) REFERENCES users(id)
);

CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(200),
    created_at DATETIME NOT NULL DEFAULT now()
);

CREATE TABLE products (
	id INT PRIMARY KEY AUTO_INCREMENT,
    price VARCHAR(50) NOT NULL,
    quantity INT,
    name VARCHAR(250),
    content VARCHAR(2500),
    images VARCHAR(2500),
    id_loai INT NOT NULL,
    FOREIGN KEY (id_loai) REFERENCES categories(id)
);

CREATE TABLE carts (
    id_user INT NOT NULL,
    id_product INT NOT NULL,
    quantity INT,
    FOREIGN KEY (id_user) REFERENCES users(id),
    FOREIGN KEY (id_product) REFERENCES products(id)
);

CREATE TABLE token (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(200) NOT NULL,
    token VARCHAR(10) NOT NULL UNIQUE,
    created_at DATETIME NOT NULL DEFAULT now()
);

CREATE TABLE favorites (
    id_user INT NOT NULL,
    id_product INT NOT NULL,
    is_like INT,
    FOREIGN KEY (id_user) REFERENCES users(id),
    FOREIGN KEY (id_product) REFERENCES products(id)
);