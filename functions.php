<?php
function nxt_ablaufinfo_shortcode($atts, $content = null) {
    global $wpdb;
    $user_id = get_current_user_id();

    $result = $wpdb->get_results("SELECT prod.id, prod.access_granted_for_days, prod.name, prouser.last_pay_date FROM {$wpdb->prefix}digimember_product prod LEFT JOIN {$wpdb->prefix}digimember_user_product prouser ON (prod.id = prouser.product_id) WHERE prouser.user_id = $user_id AND prouser.deleted IS NULL AND prod.access_granted_for_days > 0");

    $return_string = '';
    // print_r($result);
    foreach($result as $single_result) {
		$heute = new DateTime(date('Y-m-d H:i:s'));
		$kaufzeit = new DateTime($single_result->last_pay_date);
		$difference = $heute->diff($kaufzeit);
        // echo "<!-- nxt debug: Differenz: " . $difference->format('%a') . "-->";
        $days_left = max( 0, $single_result->access_granted_for_days - $difference->format('%a'));
        if($days_left == 0) $return_string .= "<p>Dein Zugriff auf den Kurs <strong>$single_result->name</strong> ist leider abgelaufen. Solltest du weiterhin auf den Kurs zugreifen wollen, so erneuere bitte (vergünstigt) dein Abo für den Kurs. Weitere Informationen zur Abo-Verlängerung stellen wir hier in Kürze zur Verfügung.</p>"; else $return_string .= "<p>Auf den Kurs <strong>$single_result->name</strong> hast du noch $days_left Tage Zugriff.</p>";
    }
    if($return_string != '') {
        $return_string = '<div class="nxt_ablaufinfo"><h4>Hinweis:</h4>' . $return_string . '</div>';
    }
    return $return_string;
}
add_shortcode('ablaufinfo', 'nxt_ablaufinfo_shortcode');
?>
