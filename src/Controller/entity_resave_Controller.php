<?php
/**
 * @file
 * Contains \Drupal\entity_resave\Controller\entity_resave_Controller.
 */
namespace Drupal\entity_resave\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;
use Drupal\taxonomy\Entity\Term;
use \Drupal\user\Entity\User;
use \Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use \GuzzleHttp\Exception\RequestException;
use \Drupal\Component\Utility\Html;

class entity_resave_Controller extends ControllerBase {


    public function completed() {
        \Drupal::messenger()->addMessage('Resave completed');
        return new RedirectResponse('/admin/content');
    }
}
