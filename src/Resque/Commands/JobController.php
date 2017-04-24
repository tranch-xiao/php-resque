<?php
namespace Resque\Commands;


use Resque;
use Yii;
use yii\console\Controller;

class JobController extends Controller
{
    /**
     * Queue a new job to run with optional delay
     *
     * @param $job string The job to run.
     * @param $queue string The queue to add the job to.
     * @param $args array The arguments to send with the job.
     * @param $delay int|null The amount of time or a unix time to delay execution of job till.
     * @return void
     */
    public function actionQueue($job, $queue = null, $delay = false, $args = array())
    {
        if (!$delay or filter_var($delay, FILTER_VALIDATE_INT)) {
            $delay = (int)$delay;
        } else {
            Yii::error('Delay option "' . $delay . '" is invalid type "' . gettype($delay) . '", value must be an integer.');
            return;
        }

        if ($delay) {
            if ($job = Resque::later($delay, $job, $args, $queue)) {
                Yii::info('Job <pop>' . $job . '</pop> will be queued at <pop>' . date('r', $job->getDelayedTime()) . '</pop> on <pop>' . $job->getQueue() . '</pop> queue.');
                return;
            }
        } else {
            if ($job = Resque::push($job, $args, $queue)) {
                Yii::info('Job <pop>' . $job . '</pop> added to <pop>' . $job->getQueue() . '</pop> queue.');
                return;
            }
        }
    }
}