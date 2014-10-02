<?php
    include("zklib/zklib.php");
    include("etime.php");
    $unsent_records = array();
    $zk = new ZKLib(MACHINE_IP, MACHINE_PORT);

    send_data_in_file($unsent_records);

    $ret = $zk->connect();
    sleep(1);
    if ( $ret ): 
        $zk->disableDevice();
        $attendance = $zk->getAttendance();

        while(list($idx, $attendancedata) = each($attendance)):
            if ( $attendancedata[2] == 14 )
                $status = 'Check Out';
            else
                $status = 'Check In';
            $data = array('etag' => $attendancedata[0], 'entry_time' => date( "Y-m-d H:i:s", strtotime( $attendancedata[3] ) ), 'company_key' => API_KEY, 'entry_type'=>0);
            send_http_request($data,$unsent_records);
        endwhile;
        write_to_file($unsent_records);

        $zk->clearAttendance();
        sleep(1);
        $zk->enableDevice();
        sleep(1);
        $zk->disconnect();
    endif
?>