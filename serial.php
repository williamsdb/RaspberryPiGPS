<?php
// location for log files. Include the trailing slash
$logs = "/var/www/html/logs/";
$debug = FALSE;
$first_time = TRUE;
$set_time = TRUE;

if ($debug) file_put_contents($logs.date('Ymd').'-serial-log.txt','Startup'.PHP_EOL,FILE_APPEND);

// Set the serial port parameters
$c = stream_context_create(array('dio' =>
	array('data_rate' => 9600,
		  'data_bits' => 8,
		  'stop_bits' => 1,
		  'parity' => 0,
		  'flow_control' => 0,
		  'is_blocking' => 0,
		  'canonical' => 1)));

// open the port and check that it has worked
if ( !$t = fopen('dio.serial:///dev/ttyACM0','r+b',false, $c) ) // Open for read
    if ($debug) file_put_contents($logs.date('Ymd').'-serial-log.txt','Failed to open ttyACM0'.PHP_EOL,FILE_APPEND);

// cycle round forever looking for the correct GPS record
while(true) {
    // get a record from the GPS
    $line=fgets($t,1024);
    // we are only interested in GPMRC records
    if (!empty($line)){
        if ( strpos($line,"GPRMC")){
            // break up the string
            // 1 - time
            // 2 - validity
            // 3 - latitude
            // 4 - latitude hemisphere
            // 5 - longitude
            // 6 - longitude hemisphere
            // 7 - speed
            // 8 - true course
            // 9 - date
            // 10 - variation
            // 11 - variation_e_w
            // 12 - checksum
            $resp = explode(",", $line);
            if ($first_time){
                // set the pi time
                $dt = substr($resp[9],4,2).'-'.substr($resp[9],2,2).'-'.substr($resp[9],0,2).' '.
                      substr($resp[1],0,2).':'.substr($resp[1],2,2).':'.substr($resp[1],4,2);
                if ($set_time) exec('date -s "'.$dt.'"');
                $first_time = FALSE;
            }else{
                // we are only interested in valid GPS records
                if ($resp[2] == "A") {
                    $lat_decimal = degrees_to_decimal($resp[3], $resp[4]);
                    $lon_decimal = degrees_to_decimal($resp[5], $resp[6]);
                    file_put_contents($logs.date('Ymd').'-simple-log.txt', $resp[9].",".$resp[1].",".$lat_decimal.",".$lon_decimal.PHP_EOL,FILE_APPEND);
                }
            }
            if ($debug) file_put_contents($logs.date('Ymd').'-serial-log.txt',$line.PHP_EOL,FILE_APPEND);
        }else{
            if ($debug) file_put_contents($logs.date('Ymd').'-serial-log.txt',$line.PHP_EOL,FILE_APPEND);
        }
    }
}


// function to convert degress (HHMM.SS) to decimal
function degrees_to_decimal($data, $hemisphere){
    $ddmmss = $data/100;
    $degrees = (int)$ddmmss;
    $minutes = (($ddmmss - $degrees) * 100 ) / 60.0;
    $decimal = $degrees+$minutes;

    if ($hemisphere == "N" or $hemisphere == "E"){
        return $decimal;
    }else{
        return -$decimal;
    }
}

?>
