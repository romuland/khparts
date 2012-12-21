<?php header("Content-Type: text/html; charset=windows-1251");

		include "sendmail.php";
		
		$mail = new SendMail('Пользователь задал вопрос');

		$name = $_POST['name'];
		$mess = $_POST['message'];
		$email = $_POST['email'];

		$message = '<html><head><title>Вопрос от пользователя</title></head>
    <body>
        <p>'.date("d/m").' в '.date("H:i").' получен вопрос от пользователя <strong>'.$name.'</strong>:
		<p>'.$mess.'.<p>E-mail для обратной связи:<em>'.$email.'</em>.</p>
    </body>
</html>';

		$send = $mail->send($message); 
		
		if ($send !== true) {
   			echo 'Не удалось отправить сообщение, пожалуйста свяжитесь с нами по электронной почте: <br />'.$send->message;
		} else {
		    echo 'Сообщение было отправлен! В самое ближайшше время мы ответим на Ваш вопрос!';
		}
?>