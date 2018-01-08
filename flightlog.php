<?php

// Flightlog Core - part of Flightlog https://flightlog.openspotter.org
//
// Saves dump1090 mutability output (https://github.com/mutability/dump1090) in a MySQL Database
//
// Copyright (c) 2016 - 2018 Christian Borchmann-Backhaus <christian@borchi.de>
//
// This file is free software: you may copy, redistribute and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation, either version 2 of the License, or (at your
// option) any later version.
//
// This file is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
// General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program. If not, see <http://www.gnu.org/licenses/>.

error_reporting(E_ALL);

// Define MySQL connection

define('mysql_host', 'p:localhost');
define('mysql_user', 'user');
define('mysql_password', 'password');
define('mysql_database', 'flightlog_core');

// a unique id for the dump1090 mutability source

$source = 1;

// Get the data from dump1090 mutability JSON output located at
// /run/dump1090-mutability/aircraft.json

function getData()
{
    if (file_exists('/run/dump1090-mutability/aircraft.json')) {
        $file = '/run/dump1090-mutability/aircraft.json';
        
        return json_decode(file_get_contents($file), true)['aircraft'];
    } else {
        echo "/run/dump1090-mutability/aircraft.json not found, is dump1090-mutability running?" . PHP_EOL;
        echo "sudo /etc/init.d/dump1090-mutability status" . PHP_EOL;
    }
 }

 // Prase and saves flight data and position data
 
function processData($data, $source)
{
    echo "---- Source: $source ------------------------------------" . PHP_EOL;
    
    foreach ($data as $row) {
        
        // hex: the 24-bit ICAO identifier of the aircraft, as 6 hex digits.
        // The identifier may start with '~', this means that the address is a non-ICAO address (e.g. from TIS-B).
        if (isset($row['hex'])) {
            $hex = strtoupper(trim($row['hex']));
        } else
            $hex = NULL;
        // squawk: the 4-digit squawk (octal representation)
        if (isset($row['squawk'])) {
            $squawk = intval($row['squawk']);
        } else
            $squawk = NULL;
        // flight: the flight name / callsign
        if (isset($row['flight'])) {
            $flight = strtoupper(trim($row['flight']));
        } else
            $flight = '?';
        
        // lat, lon: the aircraft position in decimal degrees
        if (isset($row['lat'])) {
            $latitude = floatval($row['lat']);
        } else
            $latitude = NULL;
        // lat, lon: the aircraft position in decimal degrees
        if (isset($row['lon'])) {
            $longitude = floatval($row['lon']);
        } else
            $longitude = NULL;
        // altitude: the aircraft altitude in feet, or "ground" if it is reporting it is on the ground
        if (isset($row['altitude'])) {
            if ($row['altitude'] == 'ground') {
                $altitude = 0;
            } else
                $altitude = intval($row['altitude']);
        } else
            $altitude = NULL;
        // vert_rate: vertical rate in feet/minute
        if (isset($row['vert_rate'])) {
            $vert_rate = intval($row['vert_rate']);
        } else
            $vert_rate = NULL;
        // track: true track over ground in degrees (0-359)
        if (isset($row['track'])) {
            $track = intval($row['track']);
        } else
            $track = NULL;
        // speed: reported speed in kt. This is usually speed over ground, but might be IAS - you can't tell the difference here, sorry!
        if (isset($row['speed'])) {
            $speed = intval($row['speed']);
        } else
            $speed = NULL;
        // messages: total number of Mode S messages received from this aircraft
        if (isset($row['messages'])) {
            $messages = intval($row['messages']);
        } else
            $messages = NULL;
        // seen: how long ago (in seconds before "now") a message was last received from this aircraft
        if (isset($row['seen'])) {
            $seen = floatval($row['seen']);
        } else
            $seen = NULL;
        
        echo "ICAO: $hex" . PHP_EOL;
        echo "Squawk: $squawk" . PHP_EOL;
        echo "Flight: $flight" . PHP_EOL;
        echo "Latitude: $latitude" . PHP_EOL;
        echo "Longitude: $longitude" . PHP_EOL;
        echo "Altitude: $altitude" . PHP_EOL;
        echo "Vertical Rate: $vert_rate" . PHP_EOL;
        echo "Track: $track" . PHP_EOL;
        echo "Speed: $speed" . PHP_EOL;
        echo "Messages: $messages" . PHP_EOL;
        echo "Seen: $seen" . PHP_EOL;
        echo "" . PHP_EOL;
        
        $date = date("Y-m-d");
        
        // Check if ICAO24 Code is a valid Hexadecimal
        if (ctype_xdigit($hex)) {
            
            // Save only flights with at least an altitude and a valid ICAO Code
            if ($altitude != NULL && $hex != '000000') {
                
                saveFlights($hex, $squawk, $flight, $latitude, $longitude, $altitude, $vert_rate, $track, $speed, $seen, $source);
            }
            
            // Save only good positions 
            if ($altitude != NULL && $hex != '000000' && $squawk != NULL && $flight != '?' && $latitude != NULL && $longitude != NULL && $vert_rate != NULL && $track != NULL && $speed != NULL) {
                savePositions($hex, $squawk, $flight, $latitude, $longitude, $altitude, $vert_rate, $track, $speed, $messages, $seen, $date, $source);
            }
        }
    }
}

// Saves the position data

function savePositions($hex, $squawk, $flight, $latitude, $longitude, $altitude, $vert_rate, $track, $speed, $messages, $seen, $date, $source)
{
    $db = @new MySQLi(mysql_host, mysql_user, mysql_password, mysql_database);
    mysqli_set_charset($db, "utf8");
    
    if (mysqli_connect_errno() == 0) {
        
        $query = "INSERT INTO
						positions (hex, squawk, flight, altitude, track, messages, seen, speed, vert_rate, latitude, longitude, date, source )
					VALUES (?,?,?,?,?,?,?,?,?,?,?,?,? )";
        $sql = $db->prepare($query);
        
        $sql->bind_param('sisiiiiddddsi', $hex, $squawk, $flight, $altitude, $track, $messages, $seen, $speed, $vert_rate, $latitude, $longitude, $date, $source);
        
        $sql->execute();
        
        if ($sql->affected_rows == 1) {
            // printf ("Saved ID %d.\n", $sql->insert_id);
        } else {
            // echo 'Error...';
        }
    } else {
        echo 'Database error: ' . mysqli_connect_errno() . ' : ' . mysqli_connect_error();
    }
    
    $sql->close();
    $db->close();
}

// Saves the flights data

function saveFlights($hex, $squawk, $flight, $latitude, $longitude, $altitude, $vert_rate, $track, $speed, $seen, $source)
{
    $db = @new MySQLi(mysql_host, mysql_user, mysql_password, mysql_database);
    mysqli_set_charset($db, "utf8");
    
    if (mysqli_connect_errno() == 0) {
        
        $query = "INSERT INTO
						flights (source, hex, first_squawk, last_squawk, callsign, first_altitude, last_altitude, first_track, last_track, first_speed, last_speed, first_vert_rate, last_vert_rate, first_latitude, first_longitude, last_latitude, last_longitude, first_timestamp, last_timestamp, date )
					VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?, now(), now(), date(now()))
            
				ON DUPLICATE KEY UPDATE
										last_timestamp = now(),
										last_squawk = VALUES(last_squawk),
										last_altitude = VALUES(last_altitude),
										last_track = VALUES(last_track),
										last_speed = VALUES(last_speed),
										last_vert_rate = VALUES(last_vert_rate),
										last_latitude = VALUES(last_latitude),
										last_longitude = VALUES(last_longitude)";
        $sql = $db->prepare($query);
        
        $sql->bind_param('isiisiiiiiidddddd', $source, $hex, $squawk, $squawk, $flight, $altitude, $altitude, $track, $track, $speed, $speed, $vert_rate, $vert_rate, $latitude, $longitude, $latitude, $longitude);
        
        $sql->execute();
        
        if ($sql->affected_rows == 1) {
            // printf ("Saved ID %d.\n", $sql->insert_id);
        } else {
            // echo 'Error...';
        }
    } else {
        echo 'Database error: ' . mysqli_connect_errno() . ' : ' . mysqli_connect_error();
    }
    
    $sql->close();
    $db->close();
}

// do the magic


if ($source != NULL && intval($source > 0)) {
    while (1 != 2) {
        
        $data = getData();
        processData($data, $source);
        unset($data);
        sleep(5);
    }
} else {
    echo "" . PHP_EOL;
    echo "Invalid settings for source $source !!!" . PHP_EOL;
    echo "" . PHP_EOL;
    echo "Do not forget to set your reciver location in /etc/default/dump1090-mutability and restart with" . PHP_EOL;
    echo "sudo /etc/init.d/dump1090-mutability restart" . PHP_EOL;
    echo "" . PHP_EOL;
}
