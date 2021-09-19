<?php
function convertUtf8($string)
{
	return iconv(mb_detect_encoding($string, mb_detect_order(), true), "UTF-8", $string);
}

echo '"'.__d('credit', 'Request Date').'","'.__d('credit', 'Name').'","'.__d('credit', 'Email').'","'.__d('credit', 'Amount').' ('.html_entity_decode(Configure::read('Config.currency')['Currency']['currency_code'],ENT_QUOTES,'UTF-8').')","'.__d('credit', 'Payment Details').'","'.__d('credit', 'Status').'"'."\n";

foreach ($list_item as $i => $row):
	$tmp = array();
	$tmp[] = '"' . convertUtf8(preg_replace('/"/','""', $this->Time->event_format($row['CreditWithdrawPayment']['request_date'], '%B %d, %Y') ) ) . '"';
	$tmp[] = '"' . preg_replace('/"/','""',$row['User']['name']) . '"';
	$tmp[] = '"' . convertUtf8(preg_replace('/"/','""',$row['User']['email'])) . '"';
	$tmp[] = '"' . preg_replace('/"/','""',$row['CreditWithdrawPayment']['request_amount']) . '"';
	$tmp[] = '"' . preg_replace('/"/','""',$row['CreditWithdrawPayment']['bank_info']) . '"';

	switch ($row['CreditWithdrawPayment']['status']) {
		case 'pending':
			$tmp[] = '"' . preg_replace('/"/','""',__d('credit', 'Pending')) . '"';
			break;

		case 'accepted':
			$tmp[] = '"' . preg_replace('/"/','""',__d('credit', 'Accepted')) . '"';
			break;

		case 'reject':
			$tmp[] = '"' . preg_replace('/"/','""',__d('credit', 'Rejected')) . '"';
			break;
		
		default:
			break;
	}
	
	echo implode(',', $tmp) . "\n";
endforeach;

?>