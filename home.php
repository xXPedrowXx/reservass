<?php
session_start();

if(!isset($_SESSION['access_token'])) {
    header('Location: google-login.php');
    exit();    
}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.1.9/jquery.datetimepicker.min.css" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.1.9/jquery.datetimepicker.min.js"></script>
<style type="text/css">
#form-container {
    width: 400px;
    margin: 100px auto;
}

input[type="text"], select {
    border: 1px solid rgba(0, 0, 0, 0.15);
    font-family: inherit;
    font-size: inherit;
    padding: 8px;
    border-radius: 0px;
    outline: none;
    display: block;
    margin: 0 0 20px 0;
    width: 100%;
    box-sizing: border-box;
}

.input-error {
    border: 1px solid red !important;
}

#create-event {
    background: none;
    width: 100%;
    display: block;
    margin: 0 auto;
    border: 2px solid #2980b9;
    padding: 8px;
    background: none;
    color: #2980b9;
    cursor: pointer;
}
</style>
</head>

<body>

<div id="form-container">
    <input type="text" id="event-title" placeholder="Título do Evento" autocomplete="off" />
    <input type="text" id="event-start-time" placeholder="Hora de Início do Evento" autocomplete="off" />
    <input type="text" id="event-end-time" placeholder="Hora de Término do Evento" autocomplete="off" />
    <input type="text" id="event-guests" placeholder="Emails dos Convidados (separados por vírgula)" autocomplete="off" />
    
    <button id="create-event" data-operation="create">Criar Evento</button>
</div>

<script>
function AdjustMinTime(ct) {
    var dtob = new Date(),
        current_date = dtob.getDate(),
        current_month = dtob.getMonth() + 1,
        current_year = dtob.getFullYear();
            
    var full_date = current_year + '-' +
                    ( current_month < 10 ? '0' + current_month : current_month ) + '-' + 
                    ( current_date < 10 ? '0' + current_date : current_date );

    if(ct.dateFormat('Y-m-d') == full_date)
        this.setOptions({ minTime: 0 });
    else 
        this.setOptions({ minTime: false });
}

$("#event-start-time, #event-end-time").datetimepicker({ format: 'Y-m-d H:i', minDate: 0, minTime: 0, step: 5, onShow: AdjustMinTime, onSelectDate: AdjustMinTime });

$("#create-event").on('click', function(e) {


    if($("#event-end-time").datetimepicker('getValue') < $("#event-start-time").datetimepicker('getValue')) {
        var temp = $("#event-end-time").val();
        $("#event-end-time").val($("#event-start-time").val());
        $("#event-start-time").val(temp);
    }

    parameters = {     
        title: $("#event-title").val(), 
        event_time: {
            start_time: $("#event-start-time").val().replace(' ', 'T') + ':00',
            end_time: $("#event-end-time").val().replace(' ', 'T') + ':00'
        },
        all_day: 0,
        operation: 'create',
        guests: $("#event-guests").val().split(',')
    };

    $("#create-event").attr('disabled', 'disabled');
    $.ajax({
        type: 'POST',
        url: 'teste_api_calendar/calendar-API-tutorial-main/ajax.php',
        data: { event_details: parameters },
        dataType: 'json',
        success: function(response) {
            $("#create-event").removeAttr('disabled');
            alert('Evento criado com ID: ' + response.event_id);
        },
        error: function(response) {
            $("#create-event").removeAttr('disabled');
            alert(response.responseJSON.message);
        }
    });
});
</script>

</body>
</html>