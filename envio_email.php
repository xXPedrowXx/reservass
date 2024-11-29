<?php

include 'email_convite.php';

selectemail($conn);

while ($row = $resultado->fetch_assoc()) {
    sendReminderEmails( $conn);
}

header("Location: index.php"); 

function sendReminderEmails( $conn) {
    $todayArray = getdate();
    $todayArray['hours'] -= 4; // Adjust hours

    $now = sprintf(
        '%04d-%02d-%02d %02d:00:00',
        $todayArray['year'],
        $todayArray['mon'],
        $todayArray['mday'],
        $todayArray['hours']
    );
    
    // Calculate one hour later
    $oneHourLater = date('Y-m-d H:00:00', strtotime('+1 hour', strtotime($now)));
    
    // Calculate one day later
    $oneDayLater = date('Y-m-d 00:00:00', strtotime('+1 day', strtotime($now)));

        // Check for meetings starting now
        $resultNow = getMeetingsStartingNow($now, $conn);
        while ($rowNow = $resultNow->fetch_assoc()) {
            $reserva_id = $rowNow['id'];
            sendEmail( $reserva_id ,'agora',$conn);
    
            // Update aviso_1hr to true
            updateNowNotice($reserva_id, $conn);
        }





    $result1 = getMeetingsInOneHour($now, $oneHourLater, $conn);
    while ($row1 = $result1->fetch_assoc()) {
        $reserva_id = $row1['id'];
        sendEmail( $reserva_id ,'daqui 1 hora',$conn);

        // Update aviso_1hr to true
        updateOneHourNotice($reserva_id, $conn);
    }



    $dayAfterTomorrow = date('Y-m-d 23:59:59', strtotime('+2 days', strtotime($now)));
    
    // Check for meetings starting in 24 hours
    $result24hrs = getMeetingsIn24Hours($oneDayLater, $dayAfterTomorrow, $conn);
    while ($row24hrs = $result24hrs->fetch_assoc()) {
        $reserva_id = $row24hrs['id'];
        sendEmail( $reserva_id ,'amanha',$conn);

        // Update aviso_24hrs to true
        update24HoursNotice($reserva_id, $conn);
    }
}


?>