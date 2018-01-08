# Flightlog Core 

## Saves dump1090-mutability output to MySQL

## What you need...

- a running ADS-B Reciver on a Linux PC or RaspberryPi using 
  dump1090-mutability (https://github.com/mutability/dump1090)
- PHP and MySQL installed and running

## What is a flight?

- In my definition the combination of one ICAO24 code with ohne callsign per day

## Start

- open a commandline:

````
php flightlog.php
