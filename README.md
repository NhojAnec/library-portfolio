# Online Library Management System

A PHP and MySQL website where students reserve books and librarians handle loans and returns.

**Portfolio page:** https://nhojanec.github.io/library-portfolio/

The page explains how the system works: the student and admin sides, one loan from start to finish with screenshots, the six database tables, the key code, and the bugs I left in before closing the project.

## What's in this repo

| Path | What it is |
| --- | --- |
| `index.html`, `shots/` | The portfolio page and its screenshots |
| `library/` | The full PHP website, as I left it |
| `library/library.sql` | The database, rebuilt from the queries in the code, with test data |

The PHP code is kept as it was when I finished the project. The known bugs are listed on the portfolio page, not fixed.

## Run it yourself

GitHub Pages can't run PHP, so the website itself needs XAMPP or a similar local server.

1. Install [XAMPP](https://www.apachefriends.org/) and start **Apache** and **MySQL**.
2. Copy the `library` folder into `C:\xampp\htdocs\`.
3. Import `library/library.sql` in phpMyAdmin (`http://localhost/phpmyadmin` > Import).
4. Open `http://localhost/library/` for students, or `http://localhost/library/admin/` for the admin panel.

The test logins are listed at the top of `library.sql`. They are made-up accounts for local testing only.

## Built with

PHP 8 (PDO), MySQL / MariaDB, jQuery, Bootstrap 3, DataTables.

## Credits

The original layout and starting code come from the Online Library Management System template by [ITSOURCECODE](https://itsourcecode.com/), which is credited in the site's footer.

## Author's note

Oh hey
Just wanted to add a note that I made this website as a part of Web Development class' final project sometime during 2024.
It was a group project consists of 3-4 people? I really can't remember that much but most of the works are done by me + 1-2 other member(s) who stayed all night or two working alongside me bc it's the deadline week.
Maybe I really don't remember bc of that lol.
Anyways, during that time most of our AI use were free chatGPT just for CSS cuz literally no one in our group knows CSS lmao.
The reason Claude is in the repo credit is bc I just managed to find this folder of this...thing so I whip them to put everything inside GitHub Docs and repo so yeah kinda neat huh?
Welp, love ya' all, if any of you even read or see this.
