# CMS375-FoxDay

## Creation process
> I started by creating the required PHP and HTML files. 
> I then built out the simple form and input validation pages.
> I next created the Connect.ph file to connect to the database. 
> I createded Event and User classes to handle the data in the database in a more object oriented way.
> Once I had the building blocks in place I implomented the logic to tha page.
> Once I had the logic I went on to add tailwind and fontaweome librarys for styling and icons.
> I used the public use templates for the creation of the componets.
> I then addeded a config file to store the color styling for the pages main gradient. 
> I also wanted to change out the font to something more modern and clean, and used Google Fonts for that. 
> I then updtedeated the user facing php files to have warning and erroers hidden for the user; witch enhanced the usability of the site.


## Tables

```
events
 - id
 - title
 - date
 - description
```

```mysql
CREATE TABLE IF NOT EXISTS events (
  id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(30) NOT NULL UNIQUE,
  date DATE NOT NULL,
  description TEXT NOT NULL
);
```


```
users
 - id
 - name
 - year
 - email
 - createdAt
 - updatedAt
 - deletedAt
 ```

```mysql
CREATE TABLE IF NOT EXISTS users (
 id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(30) NOT NULL,
 year enum('1','2','3','4') NOT NULL default '1',
 email VARCHAR(50) NOT NULL UNIQUE,
 createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
 updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 deletedAt DATETIME DEFAULT NULL
);
```

```
user_requests
 - id
 - user_id
 - event_id
 ```

```mysql
CREATE TABLE IF NOT EXISTS user_requests (
 id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id INT(6) UNSIGNED NOT NULL,
 event_id INT(6) UNSIGNED NOT NULL,

 FOREIGN KEY (user_id) REFERENCES users(id),
 FOREIGN KEY (event_id) REFERENCES events(id)
);
```

> there are many events and users thus the user of a many:many relationship via the user_requests table.
> the use of enum for the year column allows for a more robust validation of the year input.


## AI use

> for this projeect I wrote all of the raw php, sql, and basic html, as well as most of the styling
> I used AI to to validate my html styling for linting isues, and to generate better color palettes for the site.
> I used AI to generate all of my commets, so they could be easily re-read by other developers.


## Docker

> the project is retrivable via docker.

```shell
docker pull ghcr.io/malcolmston/foxday:latest
```

> to run docker file use the following command.
```shell
docker run -p 8080:80 ghcr.io/malcolmston/foxday:latest 
```

> to spin up the full stack appliction run
```shell
docker-compose up -d
```

> and then go to localhost:8000 to view the site and localhost:8080 to view the admin panel.
