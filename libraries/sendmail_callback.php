<<<<<<< HEAD
<?php header("Content-Type: text/html; charset=windows-1251");

		include "sendmail.php";
		
		$mail = new SendMail("Запрос на обратный звонок");
		//$subject = iconv('UTF-8', 'windows-1251', $jc->sitename.);
		//$subject = '=?koi8-r?B?'.base64_encode(convert_cyr_string($subject, "w","k")).'?='; 
		
		$name = $_POST['name'];
		$phone = $_POST['phone'];
		$time = $_POST['time'];
		
		$message = '<html><head><title>Запрос на обратный звонок</title></head>
    <body>
        <p>Получен запрос об обратном звонке</p>
		<p>Обратный звонок заказан '.date("d/m").' в '.date("H:i").' на номер: <em>'.$phone.'</em>, Имя: <strong>'.$name.'</strong>, удобное время звонка: '.$time.'.</p>
    </body>
</html>';

		$send = $mail->send($message); 
		
		if ($send !== true) {
   			echo 'К сожалению, мы не смогли принять вопрос, возникла неполадка. Вы можете связаться с нами по электронной почте: '.$jc->mailfrom.'<br />'.$send->message;
		} else {
		    echo 'Спасибо за Ваш запрос, наши специалисты свяжутся с Вами в указанное время!';
		}
?>
=======
<?php header("Content-Type: text/html; charset=windows-1251");

		include "sendmail.php";
		
		$mail = new SendMail("������ �� �������� ������");
		//$subject = iconv('UTF-8', 'windows-1251', $jc->sitename.);
		//$subject = '=?koi8-r?B?'.base64_encode(convert_cyr_string($subject, "w","k")).'?='; 
		
		$name = iconv('UTF-8', 'Windows-1251',$_POST['name']);
		$phone = $_POST['phone'];
		$time = $_POST['time'];
		
		$message = '<html><head><title>������ �� �������� ������</title></head>
    <body>
        <p>������� ������ �� �������� ������</p>
		<p>�������� ������ ������� '.date("d/m").' � '.date("H:i").' �� �����: <em>'.$phone.'</em>, ���: <strong>'.$name.'</strong>, ������� ����� ������: '.$time.'.</p>
    </body>
</html>';

		$send = $mail->send($message); 
		
		if ($send !== true) {
   			echo '� ���������, �� �� ������ ������� ������, �������� ���������. �� ������ ��������� � ���� �� ����������� �����: '.$jc->mailfrom.'<br />'.$send->message;
		} else {
		    echo '������� �� ��� ������, ���� ����������� �������� � ���� � ��������� �����!';
		}
?>
>>>>>>> 1e1b309bbf9e27aa09f1eef63d7bfeed85150451
