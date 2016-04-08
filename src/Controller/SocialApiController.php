<?php

/**
 * @file
 * Contains Drupal\social_api\Controller\SocialApiController
 */

namespace Drupal\social_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Menu\LocalTaskManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SocialApiController extends ControllerBase
{
  /**
   * @var LocalTaskManager
   */
  private $localTaskManager;

  /**
   * @inheritdoc
   */
  public static function create(ContainerInterface $container) {
    $localTaskManager = $container->get('plugin.manager.menu.local_task');

    return new static($localTaskManager);
  }

  /**
   * SocialApiController constructor.
   * @param LocalTaskManager $localTaskManager
   */
  public function __construct(LocalTaskManager $localTaskManager) {
    $this->localTaskManager = $localTaskManager;
  }

  /**
   * Render the list of plugins for a social network
   *
   * @param string $route
   * @return array
   */
  public function localTaskList($route) {
    $build = [
      '#theme' => 'local_task_list',
    ];

    $taskList = $this->localTaskManager->getDefinitions();

    $items = array();

    foreach($taskList as $taskName => $task) {
      if($task['base_route'] == $route) {
        $items[$taskName]['route_name'] = $task['route_name'];
        $items[$taskName]['title'] = $task['title']->render();
      }
    }

    $build['#items'] = $items;

    return $build;
  }

  /**
   * Set the settings for the login button for the given social networking
   *
   * @param $module
   * @param $route
   * @param $img_path
   */
  public static function setLoginButtonSettings($module, $route, $img_path) {
    $config = \Drupal::configFactory()->getEditable('social_api.settings');

    $img_path = drupal_get_path('module', $module) . '/' . $img_path;

    $config->set('auth.' . $module . '.route', $route)
      ->set('auth.' . $module . '.img_path', $img_path)
      ->save();
  }

  /**
   * Delete the settings for the login button for the given social networking
   *
   * @param $module
   */
  public static function deleteLoginButtonSettings($module) {
    $config = \Drupal::configFactory()->getEditable('social_api.settings');;

    $config->clear('auth.' . $module)
      ->save();
  }
  
}
