<?php

/**
 * ApiAccess form.
 *
 * @package    dockeet
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ApiAccessForm extends BaseApiAccessForm
{
  public function configure()
  {
  	$this->useFields(array('api_key', 'api_secret', 'user_id'));
  }
}
