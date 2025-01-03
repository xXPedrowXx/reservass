<?php
include 'inc/query.php'; 

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
include ('./teste_email/vendor/autoload.php');



function cancelEmail($reserva_id, $conn){

 

$resultado = select_email($reserva_id, $conn);

  if ($resultado && $resultado->num_rows > 0) {
      $row = $resultado->fetch_assoc();
      $dono_nome = $row['nome_usuario'];

      $membros_nome = $row['membros'];
      $membros_email = $row['membros_emails'];
      $sala_nome = $row['nome_sala'];
      $periodo = $row['periodo_reserva'];
      $temp_membros_emails = $row['user_temp_email'];


                            $mesI = substr($periodo, 5, 2); // Do 5º ao 14º caractere
                        $diaI = substr($periodo, 8, 2); // Do 7º ao 8º caractere
                        $horaI = substr($periodo, 11, 5); // Do 9º ao 13º caractere
                        
                        $mesF = substr($periodo, 27, 2); // Do 27º ao 28º caractere
                        $diaF = substr($periodo, 30, 2); // Do 29º ao 30º caractere
                        $horaF = substr($periodo, 33, 5); // Do 31º ao 35º caractere
                        




      $nome_usuario = explode(',', $membros_nome);
      $email_usuario = explode(',', $membros_email);
      $email_membros_emails = $temp_membros_emails ? explode(',', $temp_membros_emails) : [];
  } else {
      echo "Nenhum resultado encontrado ou erro na consulta.";
  }
//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings

    $mail->isSMTP();                                          //Send using SMTP
    $mail->Host       = 'smtp.acessoseguro.net';             //Set the SMTP server to send through
    $mail->SMTPAuth   = true;       
    $mail->Port       = 465;                                 //Enable SMTP authentication
                  //SMTP password
  
   $mail->Username   = 'integracao@apklog.com.br';                     //SMTP username
   $mail->Password   = 'Apkintegracao2024@';  
  
    $mail->CharSet    = 'utf8';                               // utf8 / iso-8859-1
    $mail->SMTPSecure = "ssl";

    //Recipients
    $mail->setFrom('integracao@apklog.com.br' , 'Ti');
 

    foreach ($email_usuario as $index => $email) {
      $mail->addAddress($email, $nome_usuario[$index]);       // Add a recipient
  }

  foreach ($email_membros_emails as $index => $email) {
    $mail->addAddress($email, $email);       
}
  
 

  
    

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Cancelamento de Reunião';
    $mail->Body    = ' 
    
   <style media="all" type="text/css">
    /* -------------------------------------
    GLOBAL RESETS
------------------------------------- */
    
    body {
      font-family: Helvetica, sans-serif;
      -webkit-font-smoothing: antialiased;
      font-size: 16px;
      line-height: 1.3;
      -ms-text-size-adjust: 100%;
      -webkit-text-size-adjust: 100%;
    }
    
    table {
      border-collapse: separate;
      mso-table-lspace: 0pt;
      mso-table-rspace: 0pt;
      width: 100%;
    }
    
    table td {
      font-family: Helvetica, sans-serif;
      font-size: 16px;
      vertical-align: top;
    }
    /* -------------------------------------
    BODY & CONTAINER
------------------------------------- */
    
    body {
      background-color: #f4f5f6;
      margin: 0;
      padding: 0;
    }
    
    .body {
      background-color: #f4f5f6;
      width: 100%;
    }
    
    .container {
      margin: 0 auto !important;
      max-width: 600px;
      padding: 0;
      padding-top: 24px;
      width: 600px;
    }
    
    .content {
      box-sizing: border-box;
      display: block;
      margin: 0 auto;
      max-width: 600px;
      padding: 0;
    }
    /* -------------------------------------
    HEADER, FOOTER, MAIN
------------------------------------- */
    
    .main {
      background: #ffffff;
      border: 1px solid #eaebed;
      border-radius: 16px;
      width: 100%;
    }
    
    .wrapper {
      box-sizing: border-box;
      padding: 24px;
    }
    
    .footer {
      clear: both;
      padding-top: 24px;
      text-align: center;
      width: 100%;
    }
    
    .footer td,
    .footer p,
    .footer span,
    .footer a {
      color: #9a9ea6;
      font-size: 16px;
      text-align: center;
    }
    /* -------------------------------------
    TYPOGRAPHY
------------------------------------- */
    
    p {
      font-family: Helvetica, sans-serif;
      font-size: 16px;
      font-weight: normal;
      margin: 0;
      margin-bottom: 16px;
    }
    
    a {
      color: #0867ec;
      text-decoration: none;
      margin-bottom: 10px;
      padding 8px;
      font-size: 14px;
    }
    /* -------------------------------------
    BUTTONS
------------------------------------- */
    
    .btn {
      box-sizing: border-box;
      min-width: 100% !important;
      width: 100%;
    }
    
    .btn > tbody > tr > td {
      padding-bottom: 16px;
    }
    
    .btn table {
      width: auto;
    }
    
    .btn table td {
      background-color: #ffffff;
      border-radius: 4px;
      text-align: center;
    }
    
    .btn a {
      background-color: #ffffff;
      border: solid 2px #0867ec;
      border-radius: 4px;
      box-sizing: border-box;
      color: #0867ec;

      display: inline-block;
      font-size: 16px;
      font-weight: bold;
      margin: 0;
      padding: 12px 24px;
      text-decoration: none;
      text-transform: capitalize;
    }
    
    .btn-primary table td {
      background-color: #0867ec;
    }
    
    .btn-primary a {
      background-color: #0867ec;
      border-color: #0867ec;
      color: #ffffff;
    }
    
    @media all {
      .btn-primary table td:hover {
        background-color: #ec0867 !important;
      }
      .btn-primary a:hover {
        background-color: #ec0867 !important;
        border-color: #ec0867 !important;
      }
    }
    
    /* -------------------------------------
    OTHER STYLES THAT MIGHT BE USEFUL
------------------------------------- */
    
    .last {
      margin-bottom: 0;
    }
    
    .first {
      margin-top: 0;
    }
    
    .align-center {
      text-align: center;
    }
    
    .align-right {
      text-align: right;
    }
    
    #left {
      text-align: center;
    }
    
    .text-link {
      color: #0867ec !important;
      text-decoration: underline !important;
    }
    
    .clear {
      clear: both;
    }
    
    .mt0 {
      margin-top: 0;
    }
    
    .mb0 {
      margin-bottom: 0;
    }
    
    .preheader {
      color: transparent;
      display: none;
      height: 0;
      max-height: 0;
      max-width: 0;
      opacity: 0;
      overflow: hidden;
      mso-hide: all;
      visibility: hidden;
      width: 0;
    }
    
    .powered-by a {
      text-decoration: none;
    }
    
    /* -------------------------------------
    RESPONSIVE AND MOBILE FRIENDLY STYLES
------------------------------------- */
    
    @media only screen and (max-width: 640px) {
      .main p,
      .main td,
      .main span {
        font-size: 16px !important;
      }
      .wrapper {
        padding: 8px !important;
      }
      .content {
        padding: 0 !important;
      }
      .container {
        padding: 0 !important;
        padding-top: 8px !important;
        width: 100% !important;
      }
      .main {
        border-left-width: 0 ;
        border-radius: 0 !important;
        border-right-width: 0 !important;
      }
      .btn table {
        max-width: 100% !important;
        width: 100% !important;
      }
      .btn a {
        font-size: 16px !important;
        max-width: 100% !important;
        width: 100% !important;
      }
    }
    /* -------------------------------------
    PRESERVE THESE STYLES IN THE HEAD
------------------------------------- */
    
    @media all {
      .ExternalClass {
        width: 100%;
      }
      .ExternalClass,
      .ExternalClass p,
      .ExternalClass span,
      .ExternalClass font,
      .ExternalClass td,
      .ExternalClass div {
        line-height: 100%;
      }
      .apple-link a {
        color: inherit !important;
        font-family: inherit !important;
        font-size: inherit !important;
        font-weight: inherit !important;
        line-height: inherit !important;
        text-decoration: none !important;
      }
      #MessageViewBody a {
        color: inherit;
        text-decoration: none;
        font-size: inherit;
        font-family: inherit;
        font-weight: inherit;
        line-height: inherit;
      }
    }
    </style>
      <meta charset="UTF-8">
  </head>
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td>&nbsp;</td>
        <td class="container">
          <div class="content">


            <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="main">

              <!-- START MAIN CONTENT AREA -->
              <tr>
                <td class="wrapper">
                  <p> Infelizmente uma reuniao que você era membro foi cancelada  </p>
                  <p> O usuario '.$dono_nome.' acabou de cancelar a reuniao  .</p>

                  <p> A reuniao aconteceria na '.$sala_nome.' no periodo de  ' . $mesI . '/' . $diaI . ' '  . $horaI . ' Até '  . $mesF . '/' . $diaF . ' ' . $horaF . '</p>
                  <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                    <tbody>
                      <tr>
                        <td id="left">
                          <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                            <tbody>
                              <tr>
                             

                              </tr>
                            </tbody>
                          </table>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  <p>Clique no link e ative nas suas configuracoes a agenda automatica para adicionar um lembrete em sua conta email.</p>
                  <p>Good luck! I Hope it works :P</p>
                </td>
              </tr>

              <!-- END MAIN CONTENT AREA -->
              </table>

            
<!-- END CENTERED WHITE CONTAINER --></div>
        </td>
        <td>&nbsp;</td>
      </tr>
    </table>
  </body>
</html>
       
       
       
       
       ';
  
    $mail->send();
 
      header('Location: index.php');
    echo 'Message has been sent';
    return true;
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    return false;
}
}
