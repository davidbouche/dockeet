<?php

/**
 * api actions.
 *
 * @package    dockeet
 * @subpackage api
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class apiActions extends sfActions
{
  /**
   * Executes an application defined process prior to execution of this sfAction object.
   *
   * By default, this method is empty.
   */
  public function preExecute()
  {
    $api_access = Doctrine::getTable('ApiAccess')->findOneBy('api_key', $this->getRequest()->getParameter('api_key', ''));

    if (!$api_access instanceof ApiAccess)
    {
      throw new sfException("Wrong API Key");
    }

    $parameters = $this->getRequest()->getParameterHolder()->getAll();
    ksort($parameters);

    $plain_sig = $api_access->api_secret;
    foreach ($parameters as $key => $value)
    {
      if (in_array($key, array('api_sig', 'module', 'action'))) continue;
      $plain_sig .= $key . $value;
    }

    if ($this->getRequest()->getParameter('api_sig') !== md5($plain_sig))
    {
      throw new sfException("Wrong API signature");
    }

    if (!$api_access->User->isNew())
    {
      $this->getUser()->login($api_access->User);
    }
    $this->api_access = $api_access;
  }

  public function executeCategories (sfWebRequest $request)
  {
    $tree = array();
    foreach($this->getUser()->getCategories(true) as $category)
    {
      $tree[$category->slug] = array(
        'title'     => $category->title,
        'children'  => $category->getDescendantsForAPI()
      );
    }
    return $this->renderText(sfYaml::dump($tree, 999));
  }

 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeList (sfWebRequest $request)
  {
    $documentsQuery = array();
    $documentsQuery['category'] = $request->hasParameter('slug') ? Doctrine::getTable('Category')->findOneBy('slug', $request->getParameter('slug', '')) : null;
    $documentsQuery['mime_types'] = $request->hasParameter('type') ? $request->getParameter('type') : array();
    $documentsQuery['public'] = $request->getParameter('public', true);
    $documentsQuery['limit'] = $request->getParameter('limit', false);
    $documentsQuery['search'] = $request->getParameter('search');
    $documentsQuery['tags'] = $request->getParameter('tags');
    $documentsQuery['deep'] = $request->getParameter('deep', false);
    $documentsQuery['updated_after'] = $request->getParameter('updated_after');
    $documentsQuery['last'] = $request->getParameter('last');


    // order_by
    if ($request->hasParameter('order_by'))
    {
      $documentsQuery['order_by'] = 'd.'.$request->getParameter('order_by');
    }

    $document_query = $this->getUser()->getDocumentsQuery($documentsQuery);

    if($limit = $request->getParameter('limit'))
    {
      $document_query->limit($limit);
    }

    // Caches results
//    ksort($documentsQuery);
//    $document_query->useQueryCache(true, 604800)
//                   ->useResultCache(true, 604800, 'docatl_'.Doctrine_Inflector::urlize(http_build_query($documentsQuery)).'_limit_'.$request->getParameter('limit'));

    $count = $document_query->count();
    if($count > 100)
    {
      $document_query->limit(100);
    }

    $document_yaml = array();
    foreach ($document_query->execute() as $document)
    {
      $document_yaml[$document->slug] = array(
        'title' => $document->title,
        'updated_at' => $document->updated_at,
        'size' => $document->size,
        'description' => $document->description,
        'thumbnail' => $document->genThumbnail($request->getParameter('width', 125)),
        'mime_type' => $document->mime_type,
        'download' => '/api/download?slug=' . $document->slug .($document->mime_type == 'application/x-shockwave-flash' ? '&attachment=0' : ''). '&api_key=' . $this->api_access->api_key . '&api_sig=' . md5($this->api_access->api_secret . 'api_key' . $this->api_access->api_key.($document->mime_type == 'application/x-shockwave-flash' ? 'attachment0' : '') . 'slug' . $document->slug)
      );
    }

    sfConfig::set('sf_web_debug', false);
    return $this->renderText(sfYaml::dump($document_yaml));
  }

  /**
   * Download via API
   * @param sfWebRequest $request
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
      throw new sfException(sprintf("File %s doesn't exist or read access denied.", $file_path));
    }

    // Adding the file to the Response object
    $this->getResponse()->clearHttpHeaders();
    $this->getResponse()->setHttpHeader('Pragma: public', true);

    $this->getResponse()->setContentType(($version instanceof DocumentVersion) ? $version->mime_type : $document->mime_type);
    $this->getResponse()->setHttpHeader('Content-Disposition', !$request->hasParameter('attachment') || $request->getParameter('attachment') == 1 ? 'attachment' : 'inline; filename=' . $document->title . substr($document->file, strrpos($document->file, '.')));
    $this->getResponse()->sendHttpHeaders();
    $this->getResponse()->setContent(readfile($file_path));

    return sfView::NONE;
  }

  /**
   * Subscribe via a remote web site.
   * @param sfWebRequest $request
   */
  public function executeSubscribe (sfWebRequest $request)
  {
  	$document = Doctrine::getTable('Document')->findOneBy('slug', $request->getParameter('slug', ''));
    if (!$document instanceof Document)
    {
      throw new sfException("Bad document slug.");
    }

  	$user = Doctrine::getTable('User')->findOneBy('email', $request->getParameter('email'));
    if (!$user instanceof User)
    {
      throw new sfException("Unknown user email.");
    }

    if (0 == Doctrine::getTable('UserDocument')->createQuery('u')->where('user_id = ? AND document_id = ?', array($user->id, $document->id))->count())
    {
    	$user_document = new UserDocument();
    	$user_document->User = $user;
    	$user_document->Document = $document;
    	$user_document->save();
    }

    sfConfig::set('sf_web_debug', false);
    return $this->renderText('ok');
  }

  /**
   * Unsubscribe via a remote web site.
   * @param sfWebRequest $request
   */
  public function executeUnsubscribe (sfWebRequest $request)
  {
  	$document = Doctrine::getTable('Document')->findOneBy('slug', $request->getParameter('slug', ''));
    if (!$document instanceof Document)
    {
      throw new sfException("Bad document slug.");
    }

  	$user = Doctrine::getTable('User')->findOneBy('email', $request->getParameter('email'));
    if (!$user instanceof User)
    {
      throw new sfException("Unknown user email.");
    }

    if (0 < Doctrine::getTable('UserDocument')->createQuery('u')->where('user_id = ? AND document_id = ?', array($user->id, $document->id))->count())
    {
    	Doctrine::getTable('UserDocument')->createQuery('u')->where('user_id = ? AND document_id = ?', array($user->id, $document->id))->delete();
    }

    sfConfig::set('sf_web_debug', false);
    return $this->renderText('ok');
  }



}
