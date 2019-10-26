create table if not exists books_information
(
    isbn             char(10)     not null,
    title            varchar(255) not null,
    publication_date date         not null,
    edition          varchar(255) not null,
    authors          varchar(255) not null,
    cover_image      varchar(255) null,
    description      text         null,
    primary key (isbn)
)
    charset = latin1;

create table if not exists books_content
(
    id       int auto_increment
        primary key,
    isbn     char(10)     not null,
    chapter  tinyint      not null,
    title    varchar(255) not null,
    content  longtext     not null,
    video_id varchar(255) null,
    constraint idx_chapter_isbn
        unique (isbn, chapter),
    constraint books_content_ibfk_1
        foreign key (isbn) references books_information (isbn)
            on update cascade on delete cascade
)
    charset = latin1;

create table if not exists users
(
    id                        int auto_increment
        primary key,
    name                      varchar(50)                   not null,
    email                     varchar(255)                  not null,
    password_hash             varchar(255)                  not null,
    password_reset_hash       varchar(64)                   null,
    password_reset_expires_at datetime                      null,
    activation_hash           varchar(64)                   null,
    is_active                 tinyint(1)   default 0        not null,
    type                      varchar(255) default 'reader' not null,
    bio                       varchar(255)                  null,
    profile_photo             varchar(255)                  null,
    constraint activation_hash
        unique (activation_hash),
    constraint email
        unique (email),
    constraint password_reset_hash
        unique (password_reset_hash)
);

create table if not exists chapter_history
(
    id         int auto_increment
        primary key,
    isbn       char(10)                           not null,
    chapter_no smallint                           not null,
    user_id    int                                not null,
    created_at datetime default CURRENT_TIMESTAMP not null,
    constraint chapter_history_ibfk_1
        foreign key (user_id) references users (id)
            on update cascade on delete cascade
);

create index user_id
    on chapter_history (user_id);

create table if not exists messages
(
    id         int auto_increment
        primary key,
    from_user  int                                not null,
    to_user    int                                not null,
    title      varchar(255)                       not null,
    body       text                               not null,
    created_at datetime default CURRENT_TIMESTAMP not null,
    constraint messages_ibfk_1
        foreign key (from_user) references users (id)
            on update cascade on delete cascade,
    constraint messages_ibfk_2
        foreign key (to_user) references users (id)
            on update cascade on delete cascade
)
    charset = latin1;

create index from_user
    on messages (from_user);

create index to_user
    on messages (to_user);

create table if not exists messages_replies
(
    id         int auto_increment,
    message_id int                                not null,
    user_id    int                                not null,
    created_at datetime default CURRENT_TIMESTAMP not null,
    textbody   varchar(255)                       not null,
    constraint id
        unique (id),
    constraint messages_replies_ibfk_1
        foreign key (message_id) references messages (id)
            on update cascade on delete cascade,
    constraint messages_replies_ibfk_2
        foreign key (user_id) references users (id)
            on update cascade on delete cascade
);

create index message_id
    on messages_replies (message_id);

create index user_id
    on messages_replies (user_id);

alter table messages_replies
    add primary key (id);

create table if not exists posts
(
    id         int auto_increment
        primary key,
    isbn       char(10)                           not null,
    title      varchar(255)                       not null,
    body       text                               not null,
    user_id    int                                not null,
    created_at datetime default CURRENT_TIMESTAMP not null,
    constraint posts_ibfk_2
        foreign key (isbn) references books_information (isbn)
            on update cascade on delete cascade,
    constraint posts_ibfk_3
        foreign key (user_id) references users (id)
            on update cascade on delete cascade
)
    charset = latin1;

create index isbn
    on posts (isbn);

create index user_id
    on posts (user_id);

create table if not exists posts_points
(
    user_id int        not null,
    post_id int        not null,
    point   tinyint(1) not null,
    constraint posts_points_ibfk_1
        foreign key (user_id) references users (id)
            on update cascade on delete cascade,
    constraint posts_points_ibfk_2
        foreign key (post_id) references posts (id)
            on update cascade on delete cascade
)
    charset = latin1;

create index post_id
    on posts_points (post_id);

create index user_id
    on posts_points (user_id);

create table if not exists posts_reports
(
    id         int auto_increment
        primary key,
    post_id    int                                not null,
    user_id    int                                not null,
    text       varchar(255)                       not null,
    created_at datetime default CURRENT_TIMESTAMP not null,
    constraint posts_reports_ibfk_1
        foreign key (post_id) references posts (id)
            on update cascade on delete cascade,
    constraint posts_reports_ibfk_2
        foreign key (user_id) references users (id)
            on update cascade on delete cascade
)
    charset = latin1;

create index post_id
    on posts_reports (post_id);

create index user_id
    on posts_reports (user_id);

create table if not exists remembered_logins
(
    token_hash varchar(64) not null,
    user_id    int         not null,
    expires_at datetime    not null,
    primary key (token_hash),
    constraint remembered_logins_ibfk_1
        foreign key (user_id) references users (id)
            on update cascade on delete cascade
);

create index user_id
    on remembered_logins (user_id);

create table if not exists replies
(
    id         int auto_increment
        primary key,
    text       text                               not null,
    post_id    int                                not null,
    user_id    int                                null,
    created_at datetime default CURRENT_TIMESTAMP null,
    constraint replies_ibfk_2
        foreign key (user_id) references users (id)
            on update cascade on delete cascade,
    constraint replies_ibfk_3
        foreign key (post_id) references posts (id)
            on update cascade on delete cascade
)
    charset = latin1;

create index post_id
    on replies (post_id);

create index user_id
    on replies (user_id);

create table if not exists replies_points
(
    user_id  int        not null,
    reply_id int        not null,
    point    tinyint(1) not null,
    constraint replies_points_ibfk_1
        foreign key (user_id) references users (id)
            on update cascade on delete cascade,
    constraint replies_points_ibfk_2
        foreign key (reply_id) references replies (id)
            on update cascade on delete cascade
)
    charset = latin1;

create index reply_id
    on replies_points (reply_id);

create index user_id
    on replies_points (user_id);

create table if not exists replies_reports
(
    id         int auto_increment
        primary key,
    reply_id   int                                not null,
    user_id    int                                not null,
    text       text                               not null,
    created_at datetime default CURRENT_TIMESTAMP not null,
    constraint replies_reports_ibfk_1
        foreign key (reply_id) references replies (id)
            on update cascade on delete cascade,
    constraint replies_reports_ibfk_2
        foreign key (user_id) references users (id)
            on update cascade on delete cascade
)
    charset = latin1;

create index reply_id
    on replies_reports (reply_id);

create index user_id
    on replies_reports (user_id);


