<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Require our autoloader
 */
require __DIR__ . '/vendor/autoload.php';

class Sentry_ext
{
  var $name = 'Sentry Extension';
  var $version = '1.0';
  var $description = 'Enable Sentry logging on website';
  var $settings_exist = 'y';
  var $docs_url = '';

  var $settings = array();

  /**
   * Constructor
   *
   * @param   mixed   Settings array or empty string if none exist.
   */

  function __construct($settings = '')
  {
    $this->settings = $settings;

  }

  function settings()
  {
    $settings = array();

    $settings['sentry_dsn'] = array('i', '', '');
    $settings['sentry_config'] = array('t', array('rows' => '10'), '');

    return $settings;
  }

  /**
   * Activate Extension
   *
   * This function enters the extension into the exp_extensions table
   *
   * @see http://ellislab.com/codeigniter/user-guide/database/index.html for
   * more information on the db class.
   *
   * @return void
   */
  function activate_extension() {
    $ext_template = array(
      'class' => __CLASS__,
      'settings' => '',
      'version' => $this->version,
      'enabled' => 'y'
    );

    $extensions = array(
      array('hook' => 'sessions_end', 'method' => 'sentry_add', 'priority' => 1)
    );

    foreach ($extensions as $extension) {
      ee()->db->insert('extensions', array_merge($ext_template, $extension));
    }
  }

  /**
   * Disable Extension
   *
   * This method removes information from the exp_extensions table
   *
   * @return void
   */
  function disable_extension() {
    ee()->db->where('class', __CLASS__);
    ee()->db->delete('extensions');
  }

  /**
   * Start Sentry
   */
  function sentry_add() {
      if($this->settings['sentry_config']) {
          $config = json_decode($this->settings['sentry_config'], true);
          $client = new Raven_Client($this->settings['sentry_dsn'], $config);
      } else {
          $client = new Raven_Client($this->settings['sentry_dsn']);
      }

      if(isset(ee()->session->userdata->email)) {
          $client->user_context(array(
              'email' => ee()->session->userdata->email
          ));
      }

      $error_handler = new Raven_ErrorHandler($client);
      $error_handler->registerExceptionHandler();
      $error_handler->registerErrorHandler();
      $error_handler->registerShutdownFunction();
  }
}
