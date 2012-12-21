<<<<<<< HEAD
<?php header("Content-Type: text/html; charset=windows-1251");

		include "sendmail.php";
		
		$mail = new SendMail('РџРѕР»СЊР·РѕРІР°С‚РµР»СЊ Р·Р°РґР°Р» РІРѕРїСЂРѕСЃ');

		$name = $_POST['name'];
		$mess = $_POST['message'];
		$email = $_POST['email'];

		$message = '<html><head><title>Р’РѕРїСЂРѕСЃ РѕС‚ РїРѕР»СЊР·РѕРІР°С‚РµР»СЏ</title></head>
    <body>
        <p>'.date("d/m").' РІ '.date("H:i").' РїРѕР»СѓС‡РµРЅ РІРѕРїСЂРѕСЃ РѕС‚ РїРѕР»СЊР·РѕРІР°С‚РµР»СЏ <strong>'.$name.'</strong>:
		<p>'.$mess.'.<p>E-mail РґР»СЏ РѕР±СЂР°С‚РЅРѕР№ СЃРІСЏР·Рё:<em>'.$email.'</em>.</p>
    </body>
</html>';

		$send = $mail->send($message); 
		
		if ($send !== true) {
   			echo 'РќРµ СѓРґР°Р»РѕСЃСЊ РѕС‚РїСЂР°РІРёС‚СЊ СЃРѕРѕР±С‰РµРЅРёРµ, РїРѕР¶Р°Р»СѓР№СЃС‚Р° СЃРІСЏР¶РёС‚РµСЃСЊ СЃ РЅР°РјРё РїРѕ СЌР»РµРєС‚СЂРѕРЅРЅРѕР№ РїРѕС‡С‚Рµ: <br />'.$send->message;
		} else {
		    echo 'РЎРѕРѕР±С‰РµРЅРёРµ Р±С‹Р»Рѕ РѕС‚РїСЂР°РІР»РµРЅ! Р’ СЃР°РјРѕРµ Р±Р»РёР¶Р°Р№С€С€Рµ РІСЂРµРјСЏ РјС‹ РѕС‚РІРµС‚РёРј РЅР° Р’Р°С€ РІРѕРїСЂРѕСЃ!';
		}
=======
<?php header("Content-Type: text/html; charset=windows-1251");

		include "sendmail.php";
		
		$mail = new SendMail('Пользователь задал вопрос');

		$name = $_POST['name'];
		$mess = $_POST['message'];
		$email = $_POST['email'];

		$message = '<html><head><title>Вопрос от пользователя</title></head>
    <body>
        <p>'.date("d/m").' в '.date("H:i").' получен вопрос от пользователя <strong>'.iconv('UTF-8', 'Windows-1251',$name).'</strong>:
		<p>'.iconv('UTF-8', 'Windows-1251',$mess).'<p>E-mail для обратной связи:<em>'.$email.'</em></p>
    </body>
</html>';

		$send = $mail->send($message); 
		
		if ($send !== true) {
   			echo 'Не удалось отправить сообщение, пожалуйста свяжитесь с нами по электронной почте: <br />'.$send->message;
		} else {
		    echo 'Сообщение было отправлен! В самое ближайшше время мы ответим на Ваш вопрос!';
		}
>>>>>>> 1e1b309bbf9e27aa09f1eef63d7bfeed85150451
?>