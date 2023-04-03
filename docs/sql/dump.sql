
create table if not exists users
(
    id           int auto_increment  primary key,
    name         varchar(90)   null,
    username     varchar(90)   null,
    phone        varchar(20)   null,
    email        varchar(90)   null,
    picture      varchar(90)   null,
    bio          longtext      null,
    password     varchar(100)  null,
    created_at   datetime      null,
    updated_at   datetime      null,
    origin       int           null,
    last_access  datetime      null,
    active       int default 0 null,
    facebook     varchar(255)  null,
    google       varchar(255)  null,
    birth_year   int           null,
    accept_terms tinyint       null
);

INSERT INTO `users` (`id`, `name`, `username`, `email`, `password`, `created_at`, `updated_at`, `active`) VALUES
(1,	'App',	'app-access',	'app@site.com',	'$2y$10$u37VCsZFkfOSZQROMsCR8.Fx9w5yGRyi922izEJKHjnoHkdUT0Req',	'2023-03-01 22:28:49',	'2023-03-01 22:28:49',	1);

create table if not exists billings
(
    id            int auto_increment primary key,
    name          varchar(90)    null,
    government_id varchar(11)    null,
    email         varchar(90)    null,
    amount        decimal(10, 2) null,
    due_date      date           null,
    debt_id       int            null,
    status        int            null,
    created_at    datetime       null,
    updated_at    datetime       null
);

create table if not exists payments
(
    id          int auto_increment primary key,
    debt_id     int            null,
    paid_at     datetime       not null,
    paid_amount decimal(10, 2) null,
    paid_by     varchar(90)    null,
    billing_id  int default 0  null,
    status      int default 0  null,
    created_at  datetime       null,
    updated_at  datetime       null
);


