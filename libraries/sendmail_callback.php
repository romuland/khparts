<<<<<<< HEAD
<?php header("Content-Type: text/html; charset=windows-1251");

		include "sendmail.php";
		
		$mail = new SendMail("Р—Р°РїСЂРѕСЃ РЅР° РѕР±СЂР°С‚РЅС‹Р№ Р·РІРѕРЅРѕРє");
		//$subject = iconv('UTF-8', 'windows-1251', $jc->sitename.);
		//$subject = '=?koi8-r?B?'.base64_encode(convert_cyr_string($subject, "w","k")).'?='; 
		
		$name = $_POST['name'];
		$phone = $_POST['phone'];
		$time = $_POST['time'];
		
		$message = '<html><head><title>Р—Р°РїСЂРѕСЃ РЅР° РѕР±СЂР°С‚РЅС‹Р№ Р·РІРѕРЅРѕРє</title></head>
    <body>
        <p>РџРѕР»СѓС‡РµРЅ Р·Р°РїСЂРѕСЃ РѕР± РѕР±СЂР°С‚РЅРѕРј Р·РІРѕРЅРєРµ</p>
		<p>РћР±СЂР°С‚РЅС‹Р№ Р·РІРѕРЅРѕРє Р·Р°РєР°Р·Р°РЅ '.date("d/m").' РІ '.date("H:i").' РЅР° РЅРѕРјРµСЂ: <em>'.$phone.'</em>, РРјСЏ: <strong>'.$name.'</strong>, СѓРґРѕР±РЅРѕРµ РІСЂРµРјСЏ Р·РІРѕРЅРєР°: '.$time.'.</p>
    </body>
</html>';

		$send = $mail->send($message); 
		
		if ($send !== true) {
   			echo 'Рљ СЃРѕР¶Р°Р»РµРЅРёСЋ, РјС‹ РЅРµ СЃРјРѕРіР»Рё РїСЂРёРЅСЏС‚СЊ РІРѕРїСЂРѕСЃ, РІРѕР·РЅРёРєР»Р° РЅРµРїРѕР»Р°РґРєР°. Р’С‹ РјРѕР¶РµС‚Рµ СЃРІСЏР·Р°С‚СЊСЃСЏ СЃ РЅР°РјРё РїРѕ СЌР»РµРєС‚СЂРѕРЅРЅРѕР№ РїРѕС‡С‚Рµ: '.$jc->mailfrom.'<br />'.$send->message;
		} else {
		    echo 'РЎРїР°СЃРёР±Рѕ Р·Р° Р’Р°С€ Р·Р°РїСЂРѕСЃ, РЅР°С€Рё СЃРїРµС†РёР°Р»РёСЃС‚С‹ СЃРІСЏР¶СѓС‚СЃСЏ СЃ Р’Р°РјРё РІ СѓРєР°Р·Р°РЅРЅРѕРµ РІСЂРµРјСЏ!';
		}
?>
=======
<?php header("Content-Type: text/html; charset=windows-1251");

		include "sendmail.php";
		
		$mail = new SendMail("Запрос на обратный звонок");
		//$subject = iconv('UTF-8', 'windows-1251', $jc->sitename.);
		//$subject = '=?koi8-r?B?'.base64_encode(convert_cyr_string($subject, "w","k")).'?='; 
		
		$name = iconv('UTF-8', 'Windows-1251',$_POST['name']);
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
>>>>>>> 1e1b309bbf9e27aa09f1eef63d7bfeed85150451
