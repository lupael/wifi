# WIFIS

A hotspot system written in PHP for Mikrotik / RouterOS devices with subscription options and Paypal integration.

The system was made in 2019 as an experiment for a local WISP network. It is very lightweight and can be installed on a Raspberry PI. The system does not have any administration build in (for now), so you have to manage it via RouterOS and SQL. It works on desktop and mobile and is multi-lingual (English, Italian, Slovenian and Croatian).


# Requirements

  - RouterOS (Mikrotik) with Hotspot package
  - PHP
  - pear2/Net_RouterOS
  - MySQL
  - Redis
  - Composer

# Installation

```sh
$ git clone https://github.com/andrejtrcek/wifis.git
$ cd wifis
$ composer install
$ mysql -u username -p database_name < wifis.sql
$ nano dao/config.php
```

# RouterOS / Mikrotik configuration

### Hotspot package

Install Hotspot package from https://mikrotik.com/download


### Replace Hotspot files

Upload and replace auto generated files in the hotspot/ folder on RouterOS.


### Configuration

```sh
/interface bridge add name=Hotspot
/ip hotspot profile add dns-name=wifis.info hotspot-address=10.10.2.1 login-by=mac,http-chap,https,http-pap,mac-cookie name=wifis
/ip pool add name=wifis ranges=10.10.2.1-10.10.2.255
/ip dhcp-server add address-pool=wifis disabled=no interface=Hotspot lease-time=1h name=wifis
/ip hotspot add address-pool=wifis addresses-per-mac=3 disabled=no interface=Hotspot name=wifis profile=wifis
/ip hotspot user profile
add address-pool=wifis !idle-timeout !keepalive-timeout mac-cookie-timeout=1d name=InternetDay rate-limit=2048k/2048k shared-users=unlimited transparent-proxy=yes
add address-pool=wifis !idle-timeout !keepalive-timeout mac-cookie-timeout=1d name=InternetWeekend rate-limit=2048k/2048k shared-users=unlimited transparent-proxy=yes
add address-pool=wifis !idle-timeout !keepalive-timeout mac-cookie-timeout=1d name=internetWeek rate-limit=2048k/2048k shared-users=unlimited transparent-proxy=yes
/ip address add address=10.10.2.1/24 comment="WIFIS Network" interface=Hotspot network=10.10.2.1
/ip dhcp-server network add address=10.10.2.0/24 comment="WIFIS Network" gateway=10.10.2.1
/ip dns static add address=10.10.2.1 name=wifis.info type=A
/ip dns set allow-remote-requests=yes servers=1.1.1.1
/ip hotspot walled-garden
add dst-host=wifis.info
add dst-host=*.paypal.com
add dst-host=*.paypalobjects.com
add dst-host=*.paypal-metrics.com
add dst-host=*.akamaiedge.net
add dst-host=paypal.112.2O7.net
```

