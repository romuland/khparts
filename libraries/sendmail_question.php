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
?>