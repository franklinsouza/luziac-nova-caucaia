<?php
  header('Content-Type: application/json');

  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;

  require '../api/mail/PHPMailer/src/PHPMailer.php';
  require '../api/mail/PHPMailer/src/SMTP.php';
  require '../api/mail/PHPMailer/src/Exception.php';


  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = strip_tags($_POST['nome']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $whatsapp = strip_tags($_POST['whatsapp']);

    $requiredfields = [
        'nome' => $nome,
        'email' => $email,
        'whatsapp' => $whatsapp
    ];

    $erros = [];

    if ($requiredfields['nome'] === '') {
        $erros[] = 'Nome é obrigatório';
    }

    if ($requiredfields['email'] === false) {
        $erros[] = 'Email inválido'; 
    }

    if ($requiredfields['whatsapp'] === '') {
      $erros[] = 'Telefone inválido';
    }

    // if (!empty($erros)) {
    //   http_response_code(400);
      
    //   echo json_encode([
    //     'status' => false,
    //     'msg' => 'Erro de validação',
    //     'erros' => $erros
    //   ]);
    //   exit;
    // }
    

    $html = file_get_contents(__DIR__ . '/template-contact.html');

    $html = str_replace(
        ['{{nome}}','{{email}}','{{whatsapp}}','{{ano}}'],
        [$nome, $email, $whatsapp, date('Y')],
        $html
    );

    // $alt = "Novo contato recebido\n\n"
    //    . "Nome: $nome\n"
    //    . "E-mail: $email\n"
    //    . "Telefone: $whatsapp\n"

    try{
      //Server settings
      $mail = new PHPMailer(true);
      $mail->isSMTP();
      $mail->Host       = 'email-ssl.com.br';
      $mail->SMTPAuth   = true;
      $mail->Username   = 'no-reply@sancan.com.br';
      $mail->Password   = 'qtBkqjhx39xQ4r3!';
      $mail->SMTPSecure = 'ssl';
      $mail->Port       = 465;

      //Recipients
      $mail->setFrom('no-reply@sancan.com.br', 'SANCAN');
      $mail->addAddress('novacaucaia9@gmail.com'); //
      $mail->Subject = 'NOVA CAUCAIA';

      $mail->CharSet = 'UTF-8';
      $mail->Encoding = 'base64';

      //Content
      $mail->isHTML(true);
      $mail->Body = $html;
      // $mail->AltBody = $alt;

      $mail->send();

      http_response_code(200);
      echo json_encode(['status' => true]);

    }catch(Exception $e){
        //file_put_contents(__DIR__ . '/mail_error.log', $mail->ErrorInfo . PHP_EOL, FILE_APPEND);

        http_response_code(400);
        echo json_encode(['status' => false]);
    }
  }