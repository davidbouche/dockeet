<?php

/**
 * Category form.
 *
 * @package    dockeet
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id$
 */
class CategoryFrontendForm extends CategoryForm
{
	/**
	 * Configure les widgets/validators
	 */
  public function configure()
  {
  	parent::configure();
  	$this->useFields(array('title', 'description'));
  	$this->setWidget('path', new sfWidgetFormInputHidden());
  	$this->setValidator('path', new sfValidatorString(array('required' => false)));
  }

  /**
   * Set defaults
   * @param mixed $defaults
   */
  public function setDefaults($defaults)
  {
    if (array() !== $defaults && !isset($defaults['path']))
    {
      $defaults['path'] = (false === strrpos($defaults['title'], '|')) ? $defaults['title'] : substr($defaults['title'], 0, strrpos($defaults['title'], '|')+1);
      $defaults['title'] = (false === strrpos($defaults['title'], '|')) ? $defaults['title'] : substr($defaults['title'], strrpos($defaults['title'], '|')+1);
    }

  	return parent::setDefaults($defaults);
  }

  /**
   * Concat path and title
   * @param unknown_type $con
   */
  public function save($con = null)
  {
  	$this->values['title'] = $this->values['path'] . $this->values['title'];
  	return parent::save($con);
  }




}
