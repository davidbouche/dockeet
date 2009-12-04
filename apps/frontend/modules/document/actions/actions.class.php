<?php

/**
 * document actions.
 *
 * @package    dockeet
 * @subpackage document
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class documentActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
  	$document = Doctrine::getTable('Document')->findOneBy('slug', $request->getParameter('slug', ''));
  	
  	$this->forward404Unless($document instanceof Document, "Unknown document.");
  	
  	$this->document = $document;
  }
  
 /**
  * Executes add action
  *
  * @param sfRequest $request A request object
  */
  public function executeAdd(sfWebRequest $request)
  {
    $document = Doctrine::getTable('Document')->findOneBy('slug', $request->getParameter('slud', ''));
    
  	$form = new DocumentFrontendAddForm($document);
  	
  	if ($request->isMethod('post') && $form->bindAndSave($request->getParameter($form->getName()), $request->getFiles($form->getName())))
  	{
  		$this->redirect('document/edit?id=' . $form->getObject()->id);
  	}
  	$this->form = $form;
  }
  
 /**
  * Executes edit action
  *
  * @param sfRequest $request A request object
  */
  public function executeEdit(sfWebRequest $request)
  {
    $document = Doctrine::getTable('Document')->find($request->getParameter('id', ''));
    if (!$document instanceof Document)
    {
    	$document = Doctrine::getTable('Document')->findOneBy('slug', $request->getParameter('slug', ''));
    }
    
    $form = new DocumentFrontendForm($document);
    if ($request->isMethod('post') && $form->bindAndSave($request->getParameter($form->getName()), $request->getFiles($form->getName())))
    {
      
    }
    
    $this->form = $form;
  }
  
 /**
  * Executes delete action
  *
  * @param sfRequest $request A request object
  */
  public function executeDelete(sfWebRequest $request)
  {
    $document = Doctrine::getTable('Document')->findOneBy('slug', $request->getParameter('slug', ''));
    ;
    $this->logMessage("#### passage dans l'action de suppresion pour le document avec l'id : " . $document->id);
    $document->delete();
    
    $this->getUser()->setFlash('notice', 'File has been deleted.');
    $this->redirect('@homepage');
  }
  
 /**
  * Executes download action
  *
  * @param sfRequest $request A request object
  */
  public function executeDownload (sfWebRequest $request)
  {
    $document = Doctrine::getTable('Document')->findOneBy('slug', $request->getParameter('slug', ''));
    if (!$document instanceof Document)
    {
      throw new sfException("Bad slug.");
    }
    
    $version = ($request->hasParameter('version')) ? Doctrine::getTable('DocumentVersion')->find($request->getParameter('version')) : null;
  
    $this->setLayout(false);
	  sfConfig::set('sf_web_debug', false);
	  
	  $file_path = ($version instanceof DocumentVersion) ? $document->getFilePath($version->id) : $document->getFilePath();
	  if (! file_exists($file_path) || ! is_readable($file_path))
	  {
	  	throw new sfException(sprintf("File %s doesn't exist or read access denied.", $document->getFilePath()));
	  }
	  
	  
	  // Adding the file to the Response object
	  $this->getResponse()->clearHttpHeaders();
	  $this->getResponse()->setHttpHeader('Pragma: public', true);
	  $this->getResponse()->setHttpHeader('Content-Disposition', 'attachment; filename=' . (($version instanceof DocumentVersion) ? $version->file : $document->file));
	  $this->getResponse()->setContentType(($version instanceof DocumentVersion) ? $document->getMimeType($version->id) : $document->getMimeType());
	  $this->getResponse()->sendHttpHeaders();
	  $this->getResponse()->setContent(readfile($file_path));
	
	  return sfView::NONE;
  }
  
  /*
  * Executes search action
  *
  * @param sfRequest $request A request object
  */
  public function executeSearch (sfWebRequest $request)
  {
    $form = new DocumentSearchForm();
    
    $form->bind(array('q' => $request->getParameter('q')));
    $this->getUser()->setFlash('q', $request->getParameter('q'));
    
    if ($form->isValid())
    {
      $documents_query = $this->getUser()->getDocumentsQuery();
      $documents_query = Doctrine_Core::getTable('Document')->search($form->getValue('q'), $documents_query);

      $pager = new sfDoctrinePager('Document');
      $pager->setQuery($documents_query);
      $pager->setPage($request->getParameter('page', 1));
      $pager->init();
    }
    else
    {
      $pager = null;
    }
    
    $this->pager = $pager;
    $this->form = $form;
  }
  
  /**
   * 
   * @param sfWebRequest $request
   */
  public function executeAddCategory (sfWebRequest $request)
  {
    $document = Doctrine::getTable('Document')->find($request->getParameter('document[id]', ''));
    if (!$document instanceof Document)
    {
      throw new sfException("Bad slug.");
    }
    
    $form = new DocumentCategoryAddForm($document);
    //if ($request->isMethod('post') && 
    if ($form->bindAndSave($request->getParameter($form->getName())))
    {
      $this->getUser()->setFlash('notice_document_category', 'Document successfully added in category');
    }
    
    $this->renderPartial('document_categories', array('form' => $form));
    return sfView::NONE;
  }
  
  /**
   * 
   * @param sfWebRequest $request
   */
  public function executeDeleteCategory (sfWebRequest $request)
  {
    $document = Doctrine::getTable('Document')->findOneBy('slug', $request->getParameter('slug', ''));
    if (!$document instanceof Document)
    {
      throw new sfException("Bad slug.");
    }
    
    if (1 < count($document->Categories))
    {
      $document->unlink('Categories', array($request->getParameter('category_id')), true);
      $this->getUser()->setFlash('notice_document_category', 'Document successfully removed from category');
    }
    else
    {
      $this->getUser()->setFlash('notice_document_category', 'Forbidden : the document must be present at least in one category.');
    }
    $this->renderPartial('document_categories', array('form' => new DocumentCategoryAddForm($document)));
    return sfView::NONE;
  }
}
