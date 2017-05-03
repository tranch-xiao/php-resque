<?php

namespace Resque;

use Resque\Commands\JobController;
use Resque\Commands\QueuesController;
use Resque\Commands\SocketController;
use Resque\Commands\WorkerController;
use Resque\Redis;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\Module as BaseModule;

class Module extends BaseModule implements BootstrapInterface
{
    public $controllerNamespace = 'Resque\Commands';

    public $redis = 'redis';

    public function init()
    {
        Yii::setAlias('Resque', __DIR__);

        $redisConnection = Yii::createObject($this->redis);
        // Predis style
        if (isset($redisConnection->parameters)) {
            Redis::setConfig(array(
                'parameters' => $redisConnection->parameters
            ));

            if (isset($redisConnection->options)) {
                Redis::setConfig(array(
                    'options' => $redisConnection->options
                ));
            }
        } else {
            // common style
            Redis::setConfig(array(
                'host' => $redisConnection->hostname,
                'port' => $redisConnection->port,
                'password' => $redisConnection->password,
                'database' => $redisConnection->database,
            ));
        }
        parent::init();
    }

    public function bootstrap($app)
    {
        if ($app instanceof \yii\console\Application) {
            foreach ($this->controllerMaps() as $idSuffix => $className) {
                $id = implode('-', array($this->id, $idSuffix));
                $app->controllerMap[$id] = ['class' => $className];
            }
        }
    }


    public function controllerMaps() {
        return [
            'job' => JobController::class,
            'worker' => WorkerController::class,
            'queue' => QueuesController::class,
            'socket' => SocketController::class
        ];
    }
}