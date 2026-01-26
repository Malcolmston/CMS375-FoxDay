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

## Events

```json5
[
  {
    "title": "Write code",
    "date": "2026-3-15",
    "description": "Write so much code that your hand fall off, but it will be alright, because you will have your full stack coding project 5% done."
  },
  {
    "title": "Climb super tall walls",
    "date": "2026-4-22",
    "description": "Climb 7 new routes; witch you have not yet done. similarly to the coding day, this fox day will result in your arms and leggs no longer working. The only downside is that you will have 0% of that full stack project done."
  },
  {
    "title": "Go to the beach",
    "date": "2026-5-8",
    "description": "Go to the beach and enjoy the sun, sand, and surf. This fox day will result in your skin being sunburned, and you will have 0% of that full stack project done. But at least you will have all of your limbs in tact."
  },
  {
    "title": "Go to amusement park",
    "date": "2026-3-28",
    "description": "Go to the amusement park and enjoy the rides, food, and fun. This fox day will result in your stomach being full, and while it dose not seem like it will affect you, you will have the bast sleep of your live. Now while this seems good, the downsides are that you will not be able to use your legs for 2 weeks, you will proboly miss yout next day 8am class, and even worse, you wont have even started that project you have been trying so hard to start all year."
  },
  {
    "title": "Go on a hike",
    "date": "2026-4-11",
    "description": "So you go on this hike, manage to either fall 6 or 7 times, or twist your ankle. This will result in your fox day ending in complete dispair (and pain). Now while you wount realy be able to use your legs for 2 weeks, this could be seen as a positive, sise you will have ample time to complete your full stack project. Now if you sometime manage to get through the hike unscathed, you will also have plenty of time the rest of the day to complete your project.  "
  },
  {
    "title": "Drive somewre",
    "date": "2026-5-17",
    "description": "This sound completly non-descipt, but you are only hear for vibes. This fox day actuly has no posible downsides; and despite you driving arround for like 5hrs you 'definitly' remember to fill up with gas. (lol sike you deffinitle are pushing that car) Now once you arive back on campus you will also have time to write some code, but like usual you will sit in char, look at your computer one and then go back to doom scolling for the next 2.5 hrs."
  }
]
```
