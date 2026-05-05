<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MqttService;

class MqttSubscriber extends Command
{
    protected $signature = 'mqtt:subscribe';
    protected $description = 'Start MQTT Subscriber';

    public function __construct(private MqttService $mqttService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('START MQTT...');

        if (!$this->mqttService->connect()) {
            $this->error('GAGAL CONNECT');
            return self::FAILURE;
        }

        $this->info('CONNECTED');

        // TEST PUBLISH
        $this->mqttService->publish('ESP32-KLM001', 'test', [
            'msg' => 'hello from laravel'
        ]);

        $this->info('SUBSCRIBE...');
        $this->mqttService->subscribeAll();

        $this->info('LISTENING...');
        $this->mqttService->loop();

        return self::SUCCESS;
    }
}