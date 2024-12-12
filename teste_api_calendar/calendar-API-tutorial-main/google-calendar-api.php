<?php


class GoogleCalendarApi
{
	
	public function GetAccessToken($client_id, $redirect_uri, $client_secret, $code) {	
		$url = 'https://accounts.google.com/o/oauth2/token';			
		
		$curlPost = 'client_id=' . $client_id . '&redirect_uri=' . $redirect_uri . '&client_secret=' . $client_secret . '&code='. $code . '&grant_type=authorization_code';
		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $url);		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);		
		curl_setopt($ch, CURLOPT_POST, 1);		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);	
		$data = json_decode(curl_exec($ch), true);
		$http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);		
		if($http_code != 200) 
			throw new Exception('Error : Failed to receieve access token');
			
		return $data;
	}

	public function GetUserCalendarTimezone($access_token) {
		$url_settings = 'https://www.googleapis.com/calendar/v3/users/me/settings/timezone';
		
		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $url_settings);		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);	
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '. $access_token));	
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);	
		$data = json_decode(curl_exec($ch), true); //echo '<pre>';print_r($data);echo '</pre>';
		$http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);		
		if($http_code != 200) 
			throw new Exception('Error : Failed to get timezone');

		return $data['value'];
	}

	public function GetCalendarsList($access_token) {
		$url_parameters = array();

		$url_parameters['fields'] = 'items(id,summary,timeZone)';
		$url_parameters['minAccessRole'] = 'owner';

		$url_calendars = 'https://www.googleapis.com/calendar/v3/users/me/calendarList?'. http_build_query($url_parameters);
		
		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $url_calendars);		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);	
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '. $access_token));	
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);	
		$data = json_decode(curl_exec($ch), true); //echo '<pre>';print_r($data);echo '</pre>';
		$http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);		
		if($http_code != 200) 
			throw new Exception('Error : Failed to get calendars list');

		return $data['items'];
	}

	// need to add repeat argument here
	public function CreateCalendarEvent($calendar_id, $summary, $all_day, $recurrence, $recurrence_end, $event_time, $event_timezone, $access_token, $guests = []) {
		$url_events = 'https://www.googleapis.com/calendar/v3/calendars/' . $calendar_id . '/events';
	
		$curlPost = array('summary' => $summary); // event title
	
		// if event is an all day event or not 
		if($all_day == 1) {
			$curlPost['start'] = array('date' => $event_time['event_date']);
			$curlPost['end'] = array('date' => $event_time['event_date']);
		}
		else {
			$curlPost['start'] = array('dateTime' => $event_time['start_time'], 'timeZone' => $event_timezone);
			$curlPost['end'] = array('dateTime' => $event_time['end_time'], 'timeZone' => $event_timezone);
		}
	
		// if event repeats or not
		if ($recurrence == 1) {
			$curlPost['recurrence'] = array("RRULE:FREQ=WEEKLY;UNTIL=" . str_replace('-', '', $recurrence_end) . ";" );
		}
	
		// Add guests to the event
		if (!empty($guests)) {
			$curlPost['attendees'] = array_map(function($email) {
				return ['email' => trim($email)];
			}, $guests);
		}
	
		$ch = curl_init(); // Initializes a new session and return a cURL handle    
		curl_setopt($ch, CURLOPT_URL, $url_events);        
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return the transfer as a string of the return value of curl_exec() instead of outputting it directly.    
		curl_setopt($ch, CURLOPT_POST, 1); // http post    
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // stop cURL from verifying the peer's certificate
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '. $access_token, 'Content-Type: application/json'));    
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($curlPost));    
		$data = json_decode(curl_exec($ch), true);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);        
		if($http_code != 200) {
			throw new Exception('Error : Failed to create event');}
	
		
			$servername = "localhost";
			$username = "root";
			$password = "";
			$dbname = "controle_salas";
			
			$conn = new mysqli($servername, $username, $password, $dbname);
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			}
			function insertEvent($event_id, $summary, $event_time, $conn) {
				// Lógica da função usando $conn
				$sql = "INSERT INTO calendar_api (event_id, titulo, data_inicio, data_fim) VALUES (?, ?, ?, ?)";
				$stmt = $conn->prepare($sql);
				$stmt->bind_param("ssss", $event_id, $summary, $event_time['start_time'], $event_time['end_time']);
				$stmt->execute();
				$stmt->close();
			}
			
			insertEvent($data['id'], $summary, $event_time, $conn);
    
		return $data['id'];
	}

	
	
	
	public function UpdateCalendarEvent($event_id, $calendar_id, $summary, $all_day, $event_time, $event_timezone, $access_token, $attendees = array()) {
		$url_events = 'https://www.googleapis.com/calendar/v3/calendars/' . $calendar_id . '/events/' . $event_id;
	
		// Verifique se as variáveis de data e hora estão corretamente formatadas
		$start_time = $event_time['start_time']; // Ex: "2024-12-12T10:00:00"
		$end_time = $event_time['end_time']; // Ex: "2024-12-12T12:00:00"
		
		if ($all_day == 1) {
			// Se for um evento de dia inteiro, usamos apenas a data
			$curlPost['start'] = array('date' => $event_time['event_date']);
			$curlPost['end'] = array('date' => $event_time['event_date']);
		} else {
			// Para evento com horário específico, usamos dateTime e timeZone
			// Certifique-se de que as datas estão no formato correto (ISO 8601)
			$curlPost['start'] = array('dateTime' => $start_time, 'timeZone' => $event_timezone);
			$curlPost['end'] = array('dateTime' => $end_time, 'timeZone' => $event_timezone);
		}
	
		// Adicionar os convidados (se houver)
		if (!empty($attendees)) {
			$curlPost['attendees'] = array_map(function($email) {
				return ['email' => trim($email)];
			}, $attendees);
		}
	
		// Construir o corpo da requisição
		$curlPost['summary'] = $summary;
	
		// Inicializa a requisição cURL
		$ch = curl_init();        
		curl_setopt($ch, CURLOPT_URL, $url_events);        
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');        
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '. $access_token, 'Content-Type: application/json'));    
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($curlPost));    
	
		// Executa a requisição e captura a resposta
		$data = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	
		// Verifica se houve erro na requisição
		if ($http_code != 200) {
			// Loga o erro e a resposta completa para depuração
			error_log("Erro ao atualizar evento: HTTP Code: $http_code, Resposta: $data");
	
			// Lança uma exceção com detalhes do erro
			throw new Exception("Error: Failed to update event. HTTP Code: $http_code, Response: " . json_encode($data));
		}
	
		// Fecha a conexão cURL
		curl_close($ch);
	}
	
	

	public function DeleteCalendarEvent($event_id, $calendar_id, $access_token) {
		$url_events = 'https://www.googleapis.com/calendar/v3/calendars/' . $calendar_id . '/events/' . $event_id;

		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $url_events);		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);		
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '. $access_token, 'Content-Type: application/json'));		
		$data = json_decode(curl_exec($ch), true);
		$http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
		if($http_code != 204) 
			throw new Exception('Error : Failed to delete event');
	

			$servername = "localhost";
			$username = "root";
			$password = "";
			$dbname = "controle_salas";
			
			$conn = new mysqli($servername, $username, $password, $dbname);
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			}



			function DeleteEvent($event_id, $conn) {
				// Lógica da função usando $conn
				$sql = "DELETE FROM calendar_api WHERE event_id = ?";
				$stmt = $conn->prepare($sql);
				$stmt->bind_param("s", $event_id);
				$stmt->execute();
				$stmt->close();
			}
			
			DeleteEvent($event_id,$conn);
    
		}


}

?>