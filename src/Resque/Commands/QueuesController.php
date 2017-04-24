<?php
/**
 * Created by PhpStorm.
 * User: tranch
 * Date: 17/4/22
 * Time: 下午5:16
 */

namespace Resque\Commands;


use Resque\Queue;
use Resque\Redis;
use Yii;
use yii\console\Controller;

class QueuesController extends Controller
{
    /**
     * Get queue statistics
     */
    public function actionIndex() {
        $queues = Redis::instance()->smembers('queues');

        if (empty($queues)) {
            \Yii::warning('<warn>There are no queues.</warn>');
            return;
        }

        $table = new \Resque\Helpers\Table($this);
        $table->setHeaders(array('#', 'Name', 'Queued', 'Delayed', 'Processed', 'Failed', 'Cancelled', 'Total'));

        foreach ($queues as $i => $queue) {
            $stats = Redis::instance()->hgetall(Queue::redisKey($queue, 'stats'));

            $table->addRow(array(
                $i + 1, $queue,
                (int)@$stats['queued'],
                (int)@$stats['delayed'],
                (int)@$stats['processed'],
                (int)@$stats['failed'],
                (int)@$stats['cancelled'],
                (int)@$stats['total']
            ));
        }

        Yii::info((string)$table);
    }

    public function clear($force = null) {

        if ($force || $this->confirm('Continuing will clear all php-resque data from Redis. Are you sure?', false)) {
            $this->stdout('Clearing Redis resque data...');

            $redis = \Resque\Redis::instance();

            $keys = $redis->keys('*');
            foreach ($keys as $key) {
                $redis->del($key);
            }

            $this->stdout('<pop>Done.</pop>');
        }
    }
}