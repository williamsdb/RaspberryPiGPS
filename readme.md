# RaspberryPiGPS

A demonstration of the connecting to a GPS unit from a RaspberryPi using PHP.
This is a straight re-write of [mrichardson23's gps_experimentation] (https://github.com/mrichardson23/gps_experimentation) Python equivalent.

Features:

* ability to write out valid records from GPS unit to a log file

Read more about how to install the required code and test [on my blog](http://www.spokenlikeageek.com/2016/02/09/raspberry-pi-gps…ting-code-to-php/).


## Installation

To get up and running:

* Copy the files to your server
* create a folder somewhere to write the log files to
* change the permissions so that the web server has access, for example on centos:

    sudo chown apache:apache /link/to/folder

* open index.php and set the $logs to the correct locations.

You are good to go.

## Usage

Use at our own risk!

## License

Do what you want with it. It is provided “as is” with no warranties whatsoever.
