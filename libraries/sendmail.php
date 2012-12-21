<?php header("Content-Type: text/html; charset=windows-1251");

		class SendMail{
			private $subject = '';
			private $from = '';
			function __construct($s){
				include($_SERVER['DOCUMENT_ROOT'].'/configuration.php');
				$jc = new JConfig();
				$this->subject = $s;
				$this->from = $jc->mailfrom;
			}
			
			function send($message){

				$headers  = "Content-type: text/html; charset=windows-1251 \r\n";
				$headers .= "From: ".$_SERVER['SERVER_NAME'];
				return mail($this->from, $this->subject, $message, $headers); 
				
			}
		}
?>
