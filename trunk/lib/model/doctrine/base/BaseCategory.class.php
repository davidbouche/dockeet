<?php

/**
 * BaseCategory
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property clob $title
 * @property clob $description
 * @property Doctrine_Collection $Users
 * @property Doctrine_Collection $Documents
 * @property Doctrine_Collection $UserCategory
 * @property Doctrine_Collection $DocumentCategory
 * 
 * @method clob                getTitle()            Returns the current record's "title" value
 * @method clob                getDescription()      Returns the current record's "description" value
 * @method Doctrine_Collection getUsers()            Returns the current record's "Users" collection
 * @method Doctrine_Collection getDocuments()        Returns the current record's "Documents" collection
 * @method Doctrine_Collection getUserCategory()     Returns the current record's "UserCategory" collection
 * @method Doctrine_Collection getDocumentCategory() Returns the current record's "DocumentCategory" collection
 * @method Category            setTitle()            Sets the current record's "title" value
 * @method Category            setDescription()      Sets the current record's "description" value
 * @method Category            setUsers()            Sets the current record's "Users" collection
 * @method Category            setDocuments()        Sets the current record's "Documents" collection
 * @method Category            setUserCategory()     Sets the current record's "UserCategory" collection
 * @method Category            setDocumentCategory() Sets the current record's "DocumentCategory" collection
 * 
 * @package    dockeet
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseCategory extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('category');
        $this->hasColumn('title', 'clob', null, array(
             'notnull' => true,
             'type' => 'clob',
             ));
        $this->hasColumn('description', 'clob', null, array(
             'type' => 'clob',
             ));

        $this->option('type', 'INNODB');
        $this->option('collate', 'utf8_unicode_ci');
        $this->option('charset', 'utf8');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('User as Users', array(
             'refClass' => 'UserCategory',
             'local' => 'category_id',
             'foreign' => 'user_id'));

        $this->hasMany('Document as Documents', array(
             'refClass' => 'DocumentCategory',
             'local' => 'category_id',
             'foreign' => 'document_id'));

        $this->hasMany('UserCategory', array(
             'local' => 'id',
             'foreign' => 'category_id'));

        $this->hasMany('DocumentCategory', array(
             'local' => 'id',
             'foreign' => 'category_id'));

        $sluggable0 = new Doctrine_Template_Sluggable(array(
             'fields' => 
             array(
              0 => 'title',
             ),
             'unique' => true,
             'canUpdate' => true,
             ));
        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($sluggable0);
        $this->actAs($timestampable0);
    }
}