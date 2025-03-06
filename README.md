![Logo](https://github.com/eti-lan/LANPage/blob/master/assets/lan_page.png?raw=true) 
# LANPage
A simple website template for your LAN Party that integrates with LAN Launcher.

It was developed in a very simple way so that anyone with a little knowledge can customize it. A MySQL database was deliberately omitted. Everything can be configured via files. The requirements for the web server are minimal. Each feature can be switched on or off via the configuration file. Special attention is paid to the statistics function, furthermore game servers or downloads can be linked in XML format - see the sample XML files.

It should be possible to personalize the page for each event. In addition to the customizable logo, further features should be integrated. But lately this project is only an example implementation. Help to improve it or fork it for your own project.

## Features:
* Entry point for your LAN party event
* Can be set up quickly and requires no maintenance
* Designed to always work, even if it is only used once a year
* Offer downloads for the participants
* Show a server list (offline/online)
* Various statistic features via the LAN Launcher

## Things required:
* Webserver and PHP7 or newer
* PHP SQLite, mbstring and XML extension enabled

## Installation:
- Use our automated installer:
  <code>wget -q https://www.eti-lan.xyz/lanpage.sh && sh lanpage.sh</code> (recommended) or clone the git repository
- Copy or rename config.sample.php to config.php and change values
- chown the files to match your webserver/php configured user
- chmod 0755 db/*.db
- See the *.sample.xml files and modify them to match your needs
- See https://www.eti-lan.xyz/#customize for more information about integration and the launcher.ini and launcher.css files

## Screenshots:

![1](https://raw.githubusercontent.com/eti-lan/LANPage/master/_screenshots/1.png) 

![2](https://raw.githubusercontent.com/eti-lan/LANPage/master/_screenshots/2.png) 

![3](https://raw.githubusercontent.com/eti-lan/LANPage/master/_screenshots/3.png) 