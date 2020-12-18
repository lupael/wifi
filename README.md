# WIFIS

A hotspot system written in PHP for Mikrotik / RouterOS devices with subscription options and Paypal integration.

The system was made in 2019 as an experiment for a local WISP network. It is very lightweight and can be installed on a Raspberry PI. The system does not have any administration build in (for now), so you have to manage it via RouterOS and SQL. It works on desktop and mobile and is multi-lingual (English, Italian, Slovenian and Croatian).


# Requirements

  - RouterOS (Mikrotik)
  - PHP
  - MySQL
  - Redis
  - Composer

### Installation

```sh
$ https://github.com/andrejtrcek/wifis.git
$ cd wifis
$ composer install
$ mysql -u username -p database_name < wifis.sql
$ nano dao/config.php
```

### Author

Andrej Trcek
Web: http://www.andrejtrcek.com
E-mail: me@andrejtrcek.com
