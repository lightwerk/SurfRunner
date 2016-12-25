<?php
namespace Lightwerk\SurfRunner\Domain\Model\Application;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Lightwerk.SurfRunner".  *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Reflection\ObjectAccess;
use TYPO3\Surf\Domain\Model\Application;
use TYPO3\Surf\Domain\Model\Deployment;
use TYPO3\Surf\Domain\Model\Workflow;

/**
 * Abstract Application
 *
 * @package Lightwerk\SurfRunner
 */
abstract class AbstractApplication extends Application
{
    /**
     * @var string
     */
    protected $releasesDirectory = '';

    /**
     * @var array
     */
    protected $options = [
        'useApplicationWorkspace' => true,
        'releasesDirectory' => '',
    ];

    /**
     * @var array
     */
    protected $taskOptions = [];

    /**
     * 1. initialize: Initialize directories etc. (first time deploy)
     * 2. package: Local preparation of and packaging of application assets
     * 3. transfer: Transfer of application assets to the node
     * 4. update: Update the application assets on the node
     * 5. migrate: Migrate (Doctrine, custom)
     * 6. finalize: Prepare final release (e.g. warmup)
     * 7. test: Smoke test
     * 8. switch: Do symlink to current release
     * 9. cleanup: Delete temporary files or previous releases
     *
     * @var array
     */
    protected $tasks = [];

    /**
     * @param array $options
     * @return void
     */
    public function addOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $setterName = $setterName = ObjectAccess::buildSetterMethodName($key);
            if (method_exists($this, $setterName)) {
                $this->$setterName($value);
            } else {
                $this->setOption($key, $value);
            }
        }
    }

    /**
     * @param Workflow $workflow
     * @param Deployment $deployment
     * @return void
     */
    public function registerTasks(Workflow $workflow, Deployment $deployment)
    {
        $this->defineTasks($workflow, $deployment);

        foreach ($this->tasks as $stage => $tasks) {
            $previousTask = null;
            foreach ($tasks as $task) {
                if (isset($this->taskOptions[$task])) {
                    $task = $this->getTaskNameForApplication($task);
                }
                if ($previousTask === null) {
                    $workflow->addTask($task, $stage, $this);
                } else {
                    $workflow->afterTask($previousTask, $task, $this);
                }
                $previousTask = $task;
            }
        }
    }

    /**
     * @param Workflow $workflow
     * @param Deployment $deployment
     * @return void
     */
    protected function defineTasks(Workflow $workflow, Deployment $deployment)
    {
        foreach ($this->taskOptions as $task => $taskSettings) {
            if (empty($taskSettings['baseTask'])) {
                $taskSettings['baseTask'] = $task;
            }
            if ($taskSettings['baseTask'] !== $task && isset($this->taskOptions[$taskSettings['baseTask']])) {
                $taskSettings['baseTask'] = $this->getTaskNameForApplication($taskSettings['baseTask']);
            }
            if (!isset($taskSettings['options']) || !is_array($taskSettings['options'])) {
                $taskSettings['options'] = [];
            }
            $workflow->defineTask(
                $this->getTaskNameForApplication($task),
                $taskSettings['baseTask'],
                $taskSettings['options']
            );
        }
    }

    /**
     * @param string $taskName
     * @return string
     */
    private function getTaskNameForApplication($taskName)
    {
        return 'a' . intval(trim(substr($this->getName(), 1, 2))) . '-' . $taskName;
    }

    /**
     * @param array $tasks
     * @return void
     */
    public function addTasks($tasks)
    {
        $this->tasks = array_merge_recursive($this->tasks, $tasks);
    }

    /**
     * @param array $taskOptions
     * @return void
     */
    public function addTaskOptions($taskOptions)
    {
        $this->taskOptions = array_replace_recursive($this->taskOptions, $taskOptions);
    }
}
