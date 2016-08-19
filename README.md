# Nimda Advanced TeamSpeak 3 Bot

A Modular Designed TeamSpeak 3 bot for Server owners and admins

## Getting Started

To install this bot all you need to do is download or clone the repository to your server.

Edit the Teamspeak and database configs in side the config folder then launch the bot with 
```
php start.php
```


## Prerequisities

PHP version 5.5+, Teamspeak 3 Server

## Features

* Asynchronous execution
* Plugin Autoloading and Auotinstalling
* Eloquent database support for Progresql, Mysql, MSSQL, SQLite

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

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/JABirchall/NimdaTS3/project/tags). 

## Authors

* **JABirchall** - *All the work*

See also the list of [contributors](https://github.com/JABirchall/NimdaTS3/graphs/contributors) who participated in this project.

## License

This project is licensed under GNU AGPLv3 License - see the [LICENSE](LICENSE) file for details

## Known issues

onWaitTimeout Event not firing, See: https://github.com/fkubis/teamspeak-php-framework/issues/8

## Acknowledgments

* noother
* MAJID
