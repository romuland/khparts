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
