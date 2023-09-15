create database belajar_laravel_database;

use belajar_laravel_database;

create table categories
(
    id          varchar(100) not null primary key,
    name        varchar(100) not null,
    description text,
    created_at  timestamp
) engine innodb;

desc categories;

create table counters
(
    id      varchar(100) not null primary key,
    counter int          not null default 0
) engine innodb;

insert into counters(id, counter)
values ('sample', 0);

select *
From counters;

create table products
(
    id          varchar(100) not null primary key,
    name        varchar(100) not null,
    description text         null,
    price       int          not null,
    category_id varchar(100) not null,
    created_at  timestamp    not null default current_timestamp,
    constraint fk_category_id foreign key (category_id) references categories (id)
) engine innodb;

select * from products;

drop table products;

drop table categories;

drop table counters;
drop table migrations;

show tables;

select * from migrations;

select * from categories;

CREATE TABLE
    `categories` (
        `id` varchar(100) NOT NULL,
        `name` varchar(100) NOT NULL,
        `description` text DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci

CREATE TABLE
`counters` (
    `id` varchar(100) NOT NULL,
    `counter` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci

CREATE TABLE
    `products` (
        `id` varchar(100) NOT NULL,
        `name` varchar(100) NOT NULL,
        `description` text DEFAULT NULL,
        `price` int(11) NOT NULL,
        `category_id` varchar(100) NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        KEY `fk_category_id` (`category_id`),
        CONSTRAINT `fk_category_id` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci

DROP TABLE products;
DROP TABLE counters;
DROP TABLE categories;