<<<<<<< HEAD
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
=======
<?php header("Content-Type: text/html; charset=windows-1251");

		include "sendmail.php";
		
		$mail = new SendMail('������������ ����� ������');

		$name = $_POST['name'];
		$mess = $_POST['message'];
		$email = $_POST['email'];

		$message = '<html><head><title>������ �� ������������</title></head>
    <body>
        <p>'.date("d/m").' � '.date("H:i").' ������� ������ �� ������������ <strong>'.iconv('UTF-8', 'Windows-1251',$name).'</strong>:
		<p>'.iconv('UTF-8', 'Windows-1251',$mess).'<p>E-mail ��� �������� �����:<em>'.$email.'</em></p>
    </body>
</html>';

		$send = $mail->send($message); 
		
		if ($send !== true) {
   			echo '�� ������� ��������� ���������, ���������� ��������� � ���� �� ����������� �����: <br />'.$send->message;
		} else {
		    echo '��������� ���� ���������! � ����� ��������� ����� �� ������� �� ��� ������!';
		}
>>>>>>> 1e1b309bbf9e27aa09f1eef63d7bfeed85150451
?>