<?php

$graphs = [
    'dovecot_connections'   => 'Connected Users &amp; Sessions',
    'dovecot_auth'          => 'Authentication (rate)',
    'dovecot_auth_duration' => 'Authentication Duration (&micro;s)',
    'dovecot_imap'          => 'IMAP Commands',
    'dovecot_smtp'          => 'SMTP Commands',
    'dovecot_delivery'      => 'Mail Delivery',
    'dovecot_sieve'         => 'Sieve Actions',
];

foreach ($graphs as $key => $text) {
    $graph_array['height'] = '100';
    $graph_array['width'] = '215';
    $graph_array['to'] = time();
    $graph_array['id'] = $app['app_id'];
    $graph_array['type'] = 'application_' . $key;
    echo '<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">' . $text . '</h3>
    </div>
    <div class="panel-body">
    <div class="row">';
    include 'includes/html/print-graphrow.inc.php';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}
