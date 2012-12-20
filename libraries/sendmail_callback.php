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
