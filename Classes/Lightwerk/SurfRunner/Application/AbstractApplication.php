<?php
namespace Lightwerk\SurfRunner\Application;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Lightwerk.SurfRunner".  *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Surf\Domain\Model\Deployment;
use TYPO3\Surf\Domain\Model\Workflow;

abstract class AbstractApplication extends \TYPO3\Surf\Domain\Model\Application {

	/**
	 * @var array
	 */
	protected $taskOptions = array();

	/**
	 * @var array
	 */
	protected $tasks = array();

	/**
	 * @param array $options
	 * @return void
	 */
	public function addOptions(array $options) {
		foreach ($options as $key => $value) {
			$this->setOption($key, $value);
		}
	}

	/**
	 * @param Workflow $workflow
	 * @param Deployment $deployment
	 * @return void
	 */
	public function registerTasks(Workflow $workflow, Deployment $deployment) {
		$this->defineTasks($workflow, $deployment);

		foreach ($this->tasks as $stage => $tasks) {
			$previousTask = NULL;
			foreach ($tasks as $task) {
				if (isset($this->taskOptions[$task])) {
					$task = $this->getTaskNameForApplication($task);
				}
				if ($previousTask === NULL) {
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
	protected function defineTasks(Workflow $workflow, Deployment $deployment) {
		foreach ($this->taskOptions as $task => $taskSettings) {
			if (empty($taskSettings['baseTask'])) {
				$taskSettings['baseTask'] = $task;
			}
			if ($taskSettings['baseTask'] !== $task && isset($this->taskOptions[$task])) {
				$taskSettings['baseTask'] = $this->getTaskNameForApplication($taskSettings['baseTask']);
			}
			if (!isset($taskSettings['options']) || !is_array($taskSettings['options'])) {
				$taskSettings['options'] = array();
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
	private function getTaskNameForApplication($taskName) {
		return 'a' . intval(trim(substr($this->getName(), 1, 2))) . '-' . $taskName;
	}

	/**
	 * @param array $tasks
	 * @return void
	 */
	public function addTasks($tasks) {
		$this->tasks = array_merge_recursive($this->tasks, $tasks);
	}

	/**
	 * @param array $taskOptions
	 * @return void
	 */
	public function addTaskOptions($taskOptions) {
		$this->taskOptions = array_merge_recursive($this->taskOptions, $taskOptions);
	}
}