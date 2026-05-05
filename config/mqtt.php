<?php
// ============================================================
// config/mqtt.php
// Konfigurasi MQTT untuk HiveMQ Cloud
// ============================================================
return [
    'host'      => env('MQTT_HOST', 'YOUR-CLUSTER.s1.eu.hivemq.cloud'),
    'port'      => env('MQTT_PORT', 8883),
    'username'  => env('MQTT_USERNAME', ''),
    'password'  => env('MQTT_PASSWORD', ''),
    'client_id' => env('MQTT_CLIENT_ID', 'aquasync-server-' . gethostname()),
    'tls'       => env('MQTT_TLS', true),
    'clean_session' => true,
    'keepalive' => 60,
];
 