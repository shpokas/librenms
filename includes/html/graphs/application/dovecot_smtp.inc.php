<?php

require 'includes/html/graphs/common.inc.php';

$scale_min = 0;
$nototal = 1;
$unit_text = 'SMTP';
$unitlen = 10;
$bigdescrlen = 20;
$smalldescrlen = 15;
$colours = 'mixed';

$rrd_filename = Rrd::name($device['hostname'], ['app', 'dovecot', $app->app_id, 'smtp']);

$array = [
    'count'  => 'Commands/s',
    'avg'    => 'Duration avg (µs)',
    'median' => 'Duration median (µs)',
    'p95'    => 'Duration p95 (µs)',
];

$rrd_list = [];
$i = 0;
foreach ($array as $ds => $descr) {
    $rrd_list[$i]['filename'] = $rrd_filename;
    $rrd_list[$i]['descr'] = $descr;
    $rrd_list[$i]['ds'] = $ds;
    $i++;
}

require 'includes/html/graphs/generic_multi_line_exact_numbers.inc.php';
