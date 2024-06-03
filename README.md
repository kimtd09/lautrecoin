# L'autre Coin

A Symfony webapp inspired by the popular French website Leboncoin.fr. It is currently in development and more features will be added. Data are scrapped using home made scripts.

# Key Features

- Filters
- Search
- Pagination
- Registration + Login
- User CRUD
- Mailer Service
- Some Bootstrap for its convenience
- SCSS for custom styles
- MariaDB Persistence

# Data Scraping

Two tools are used to automate the scraping, Curl and PHP parser in a bash script. Both could have been automated in a Symfony service but I prefer doing it at system level with a cronjob. Source code won't be provided in this repository but I can give some hints.

The curl command looks like this  : *curl --http3 -H $headers1 -H $headers2 ... -H $headers13 -H $cookie --output - --compressed -H $cookie $url*

PHP Parser uses regular expressions to extract URLs with the following pattern : */href="(\/ad\/[\w_]+\/\d+)"/*

Initially data parsing was done with the DOMCrawler of Symfony with the help of the CSSSelector. A much more efficient way is using the embedded json in the HTML response. 
