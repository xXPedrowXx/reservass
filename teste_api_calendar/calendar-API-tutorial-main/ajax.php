<?php
session_start();
header('Content-type: application/json');


include './inc/query.php'; 

require_once('google-calendar-api.php');

try {
    // Get event details
    $event = $_POST['event_details'];
    $capi = new GoogleCalendarApi();

    
    // Get user calendar timezone
    if(!isset($_SESSION['user_timezone']))
        $_SESSION['user_timezone'] = $capi->GetUserCalendarTimezone($_SESSION['access_token']);
    
    // Ensure the event is for one day only and at a fixed time
    if ($event['all_day'] || !empty($event['recurrence'])) {
        throw new Exception('Eventos devem ser de um dia e em horário fixo.');
    }

    // Create event on primary calendar
    $event_id = $capi->CreateCalendarEvent('primary', $event['title'], false, null, null, $event['event_time'], $_SESSION['user_timezone'], $_SESSION['access_token'], $event['guests']);


   

    echo json_encode([ 'success' => true, 'event_id' => $event_id ]);


   
}
catch(Exception $e) {
    header('Bad Request', true, 400);
    echo json_encode(array( 'success' => false, 'message' => '$e->getMessage()' ) );
}
?>