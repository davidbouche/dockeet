<?php

/**
 * Document form.
 *
 * @package    dockeet
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id$
 */
class DocumentFrontendAddForm extends DocumentForm
{
  public function configure()
  {
  	$this->useFields(array('file', 'categories_list'));
  	
  	$this->widgetSchema['file'] = new sfWidgetFormInputFile();
  	$this->validatorSchema['file'] = new sfValidatorFile();
  	
  	$this->widgetSchema['categories_list'] = new sfWidgetFormDoctrineChoice(array('model' => 'Category'));
  }

  /**
   * Updates and saves the current object.
   *
   * If you want to add some logic before saving or save other associated
   * objects, this is the method to override.
   *
   * @param mixed $con An optional connection object
   */
  protected function doSave($con = null)
  {
  	$file = $this->getValue('file');
  	$filename =  sha1(date('U') . $file->getOriginalName()) . $file->getExtension($file->getOriginalExtension());
  	
  	$this->values['file'] = $filename;
  	  	
  	if ($this->getObject()->isNew() || in_array($this->values['title'], array(null, ''), true))
  	{
  		$title = sfInflector::humanize($file->getOriginalName());
      $this->values['title'] = $title;
      $i = 0;
  		while (0 !== Doctrine::getTable('Document')->createQuery('d')->where('d.title = ?', $this->values['title'])->count())
  		{
  		  $this->values['title'] = $title . ' (' . ++$i .')';
  		}
  	}
  	 
  	parent::doSave($con);
  	
    $path = dirname($this->getObject()->getFilePath());
    if (! is_dir($path))
    {
      mkdir($path, 0777, true);
    }
    
    if (! is_writable($path))
    {
      throw new sfException("Write directory access denied");
    }
    
    $file->save($path . '/' . $filename);
  }
}
