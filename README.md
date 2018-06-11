# Nimda Advanced TeamSpeak 3 Bot
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/86370bd136ce46ba9ead64b272ba11a3)](https://www.codacy.com/app/drwhat/NimdaTS3?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=JABirchall/NimdaTS3&amp;utm_campaign=Badge_Grade)
[![Code Climate](https://codeclimate.com/github/JABirchall/NimdaTS3/badges/gpa.svg)](https://codeclimate.com/github/JABirchall/NimdaTS3/)
[![Test Coverage](https://codeclimate.com/github/JABirchall/NimdaTS3/badges/coverage.svg)](https://codeclimate.com/github/JABirchall/NimdaTS3/coverage)
[![Issue Count](https://codeclimate.com/github/JABirchall/NimdaTS3/badges/issue_count.svg)](https://codeclimate.com/github/JABirchall/NimdaTS3)

A Modular Designed TeamSpeak 3 bot for Server owners and admins

## Getting Started

To install this bot all you need to do is download or clone the repository to your server.

First run the command to setup the autoloader

```
php composer.phar dump-autoload -o
```

Edit the Teamspeak and database configs in side the config folder then launch the bot with
```
php start.php
```

## Prerequisities

PHP version 5.6+, php_PDO extentions, mbstring and Teamspeak 3 Server

## Features

* Asynchronous execution
* Plugin Auto-loading and Auto-installing
* Eloquent database support for Progresql, Mysql, MSSQL, SQLite
* Global Ban system built in
* Permissions
* Timers

## Coding style

We use FIG PSR-2 coding style standard, please read [PSR-2 coding style guide](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) for specific details.

## Deployment

This bot must be run in CLI: php start.php

## Built With

* PHP 7.0 - Totally
* Love - Maybe
* PHPStorm - ergaerga

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct, and the process for submitting pull requests to us.

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/JABirchall/NimdaTS3/tags). 

## Authors

* **JABirchall** - *Main Bot Class, plugin system, Timers*
* **Najsr** - *Permissions*

See also the list of [contributors](https://github.com/JABirchall/NimdaTS3/graphs/contributors) who participated in this project.

## License

This project is licensed under GNU AGPLv3 License - see the [LICENSE](LICENSE) file for details

## Acknowledgments

* noother
* MAJID
* [Najsr](https://github.com/Najsr)
