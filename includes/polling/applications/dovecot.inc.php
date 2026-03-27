<?php

use LibreNMS\RRD\RrdDefinition;

$name = 'dovecot';

// OID for NET-SNMP-EXTEND-MIB::nsExtendOutputFull."dovecot"
// dovecot = d(100) o(111) v(118) e(101) c(99) o(111) t(116) — length 7
$oid = '.1.3.6.1.4.1.8072.1.3.2.3.1.2.7.100.111.118.101.99.111.116';

$json_raw = snmp_get($device, $oid, '-Ovq');
$metrics = [];

if (empty($json_raw)) {
    update_application($app, 'No data returned from SNMP extend', []);

    return;
}

$raw = json_decode(trim((string) $json_raw, '"'), true);
if (json_last_error() !== JSON_ERROR_NONE || ! is_array($raw)) {
    update_application($app, 'Failed to parse JSON: ' . json_last_error_msg(), []);

    return;
}

// Index by metric_name for easy access
$data = [];
foreach ($raw as $item) {
    if (isset($item['metric_name'])) {
        $data[$item['metric_name']] = $item;
    }
}

// --- connections: connected_users and connected_sessions ---
$category = 'connections';
$fields = [
    'users'    => $data['connected_users']['count'] ?? 0,
    'sessions' => $data['connected_sessions']['count'] ?? 0,
];
$rrd_def = RrdDefinition::make()
    ->addDataset('users', 'GAUGE', 0)
    ->addDataset('sessions', 'GAUGE', 0);
$rrd_name = ['app', $name, $app->app_id, $category];
$metrics[$category] = $fields;
$tags = ['name' => $name, 'app_id' => $app->app_id, 'rrd_def' => $rrd_def, 'rrd_name' => $rrd_name];
app('Datastore')->put($device, 'app', $tags, $fields);

// --- auth: auth_success and auth_failures counts ---
$category = 'auth';
$fields = [
    'success'  => $data['auth_success']['count'] ?? 0,
    'failures' => $data['auth_failures']['count'] ?? 0,
];
$rrd_def = RrdDefinition::make()
    ->addDataset('success', 'DERIVE', 0)
    ->addDataset('failures', 'DERIVE', 0);
$rrd_name = ['app', $name, $app->app_id, $category];
$metrics[$category] = $fields;
$tags = ['name' => $name, 'app_id' => $app->app_id, 'rrd_def' => $rrd_def, 'rrd_name' => $rrd_name];
app('Datastore')->put($device, 'app', $tags, $fields);

// --- auth_duration: response time stats for auth_success and auth_failures (microseconds) ---
$category = 'auth_duration';
$fields = [
    'succ_avg'    => (int) ($data['auth_success']['avg'] ?? 0),
    'succ_median' => (int) ($data['auth_success']['median'] ?? 0),
    'succ_p95'    => (int) ($data['auth_success']['%95'] ?? 0),
    'fail_avg'    => (int) ($data['auth_failures']['avg'] ?? 0),
    'fail_median' => (int) ($data['auth_failures']['median'] ?? 0),
    'fail_p95'    => (int) ($data['auth_failures']['%95'] ?? 0),
];
$rrd_def = RrdDefinition::make()
    ->addDataset('succ_avg', 'GAUGE', 0)
    ->addDataset('succ_median', 'GAUGE', 0)
    ->addDataset('succ_p95', 'GAUGE', 0)
    ->addDataset('fail_avg', 'GAUGE', 0)
    ->addDataset('fail_median', 'GAUGE', 0)
    ->addDataset('fail_p95', 'GAUGE', 0);
$rrd_name = ['app', $name, $app->app_id, $category];
$metrics[$category] = $fields;
$tags = ['name' => $name, 'app_id' => $app->app_id, 'rrd_def' => $rrd_def, 'rrd_name' => $rrd_name];
app('Datastore')->put($device, 'app', $tags, $fields);

// --- imap: IMAP command count and response times ---
$category = 'imap';
$fields = [
    'count'  => $data['imap_command']['count'] ?? 0,
    'avg'    => (int) ($data['imap_command']['avg'] ?? 0),
    'median' => (int) ($data['imap_command']['median'] ?? 0),
    'p95'    => (int) ($data['imap_command']['%95'] ?? 0),
];
$rrd_def = RrdDefinition::make()
    ->addDataset('count', 'DERIVE', 0)
    ->addDataset('avg', 'GAUGE', 0)
    ->addDataset('median', 'GAUGE', 0)
    ->addDataset('p95', 'GAUGE', 0);
$rrd_name = ['app', $name, $app->app_id, $category];
$metrics[$category] = $fields;
$tags = ['name' => $name, 'app_id' => $app->app_id, 'rrd_def' => $rrd_def, 'rrd_name' => $rrd_name];
app('Datastore')->put($device, 'app', $tags, $fields);

// --- smtp: SMTP command count and response times ---
$category = 'smtp';
$fields = [
    'count'  => $data['smtp_command']['count'] ?? 0,
    'avg'    => (int) ($data['smtp_command']['avg'] ?? 0),
    'median' => (int) ($data['smtp_command']['median'] ?? 0),
    'p95'    => (int) ($data['smtp_command']['%95'] ?? 0),
];
$rrd_def = RrdDefinition::make()
    ->addDataset('count', 'DERIVE', 0)
    ->addDataset('avg', 'GAUGE', 0)
    ->addDataset('median', 'GAUGE', 0)
    ->addDataset('p95', 'GAUGE', 0);
$rrd_name = ['app', $name, $app->app_id, $category];
$metrics[$category] = $fields;
$tags = ['name' => $name, 'app_id' => $app->app_id, 'rrd_def' => $rrd_def, 'rrd_name' => $rrd_name];
app('Datastore')->put($device, 'app', $tags, $fields);

// --- delivery: mail delivery count and response times ---
$category = 'delivery';
$fields = [
    'count'  => $data['mail_delivery']['count'] ?? 0,
    'avg'    => (int) ($data['mail_delivery']['avg'] ?? 0),
    'median' => (int) ($data['mail_delivery']['median'] ?? 0),
    'p95'    => (int) ($data['mail_delivery']['%95'] ?? 0),
];
$rrd_def = RrdDefinition::make()
    ->addDataset('count', 'DERIVE', 0)
    ->addDataset('avg', 'GAUGE', 0)
    ->addDataset('median', 'GAUGE', 0)
    ->addDataset('p95', 'GAUGE', 0);
$rrd_name = ['app', $name, $app->app_id, $category];
$metrics[$category] = $fields;
$tags = ['name' => $name, 'app_id' => $app->app_id, 'rrd_def' => $rrd_def, 'rrd_name' => $rrd_name];
app('Datastore')->put($device, 'app', $tags, $fields);

// --- sieve: Sieve action count and response times ---
$category = 'sieve';
$fields = [
    'count'  => $data['sieve_action']['count'] ?? 0,
    'avg'    => (int) ($data['sieve_action']['avg'] ?? 0),
    'median' => (int) ($data['sieve_action']['median'] ?? 0),
    'p95'    => (int) ($data['sieve_action']['%95'] ?? 0),
];
$rrd_def = RrdDefinition::make()
    ->addDataset('count', 'DERIVE', 0)
    ->addDataset('avg', 'GAUGE', 0)
    ->addDataset('median', 'GAUGE', 0)
    ->addDataset('p95', 'GAUGE', 0);
$rrd_name = ['app', $name, $app->app_id, $category];
$metrics[$category] = $fields;
$tags = ['name' => $name, 'app_id' => $app->app_id, 'rrd_def' => $rrd_def, 'rrd_name' => $rrd_name];
app('Datastore')->put($device, 'app', $tags, $fields);

update_application($app, 'OK', $metrics);
