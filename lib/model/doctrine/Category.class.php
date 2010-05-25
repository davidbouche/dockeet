<?php

/**
 * Category
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    dockeet
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 6508 2009-10-14 06:28:49Z jwage $
 */
class Category extends BaseCategory
{
  /**
   * User subscription
   * @param User $user
   */
  public function subscribe (User $user)
  {
    $user_category = Doctrine::getTable('UserCategory')->createQuery('u')->where('category_id = ? AND user_id = ?', array($this->id, $user->id))->fetchOne();

    if (!$user_category instanceof UserCategory)
    {
      $user_category = new UserCategory();
      $user_category->Category = $this;
      $user_category->User = $user;
    }

    $user_category->subscribe = true;
    $user_category->save();
  }

  /**
   * User unsubscription
   * @param User $user
   */
  public function unsubscribe (User $user)
  {
    $user_category = Doctrine::getTable('UserCategory')->createQuery('u')->where('category_id = ? AND user_id = ?', array($this->id, $user->id))->fetchOne();

    if (!$user_category instanceof UserCategory)
    {
      throw new sfException('No relation between Category ' . $this->title . ' and user ' . $user->username);
    }

    $user_category->subscribe = false;
    $user_category->save();
  }

  /**
   * Test if the user has subscribed to this category update
   * @param User $user
   * @return boolean
   */
  public function hasSubscribed (User $user)
  {
    $query_has_subscribed  = Doctrine::getTable('UserCategory')->createQuery('u')->where('u.user_id = ? AND u.category_id = ? AND u.subscribe = ?');
    return 1 === $query_has_subscribed->count(array($user->id, $this->id, 1));
  }

  /**
   * Return the last part of the title
   * @return string
   */
  public function getPublicTitle ()
  {
  	return false != strpos($this->title, '|') ? substr($this->title, strrpos($this->title, '|')+1) : $this->title;
  }

  /**
   * Return the breadcrumb
   * @return array
   */
  public function getBreadcrumb ()
  {
  	$breadcrumb = array();
  	$current_title = '';
  	foreach (explode('|', $this->title) as $title)
  	{
  		$current_title .= (0 == strlen($current_title) ? '' : '|') . $title;
  		if ($current_title === $this->title) continue;
  		$current_category = Doctrine::getTable('Category')->findOneBy('title', $current_title);
  		if (!$current_category instanceof Category)
  		{
  			 continue;
  		}
  		$breadcrumb[$current_category->slug] = $current_category->getPublicTitle();
  	}
  	return $breadcrumb;
  }

  /**
   * Count document in category & childs if deep
   * @param boolean $deep
   * @return integer
   */
  public function countDocument($deep = false, User $user = null)
  {
  	if (!$deep)
  	{
  	  $count_document = count($this->Documents);
  	}
  	else
  	{
  		if (null !== $user)
  		{
  			$count_document = Doctrine::getTable('Document')->createQuery('d')->leftJoin('d.Categories c')->where('c.id = ?', $this->id)->count();
  		}
  		else
  		{
  			$count_document = 0;
  			foreach (Doctrine::getTable('Category')->createQuery('c')
  			->leftJoin('c.Documents d')
  			->select('c.*, count(d.id) AS count_documents')
  			->addGroupBy('c.id')
  			->where("title LIKE ?", $this->title . '%')->execute(array(), Doctrine_Core::HYDRATE_ARRAY) as $sub_category)
  			{
  				$count_document += $sub_category['count_documents'];
  			}
  		}
  	}
  	return $count_document;
  }

  /**
   * @return DoctrineCollection
   */
  public function getChildren()
  {
    return Doctrine::getTable('Category')
      ->createQuery()
      ->where('title REGEXP ?', '^'.str_replace('|', '\\|', $this->title).'\\|[^|]+$')
      ->execute();
  }

  /**
   * @return array
   */
  public function getDescendantsForAPI()
  {
    $descendants = array();
    foreach($this->getChildren() as $category)
    {
      $descendants[$category->slug] = array(
        'title'     => $category->getPublicTitle(),
        'children'  => $category->getDescendantsForAPI()
      );
    }
    return $descendants;
  }

  /**
   * Delete category and, if $erase_document, its documents
   * @param boolean $erase_document
   */
  public function userDelete($erase_document = false)
  {
  	foreach (Doctrine::getTable('Category')->createQuery('c')->where('c.title LIKE ?', $this->title . '|%')->execute() as $child_category)
  	{
  		if ($erase_document)
  		{
  			foreach ($child_category->Documents as $child_document)
  			{
  				$child_document->delete();
  			}
  		}
  		$child_category->delete();
  	}
    if ($erase_document)
    {
      foreach ($this->Documents as $child_document)
      {
        $child_document->delete();
      }
    }
    $this->delete();
  }
}
