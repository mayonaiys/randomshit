CREATE TABLE utilisateur (
    id INTEGER,
    login VARCHAR(64) NOT NULL,
    password VARCHAR(64) NOT NULL,
    mail VARCHAR(64) NOT NULL,
    status INTEGER,
    PRIMARY KEY(id)
);

CREATE TABLE memes (
    id INTEGER,
    userid INTEGER,
    link VARCHAR(64) NOT NULL,
    status INTEGER,
    PRIMARY KEY (id)
);