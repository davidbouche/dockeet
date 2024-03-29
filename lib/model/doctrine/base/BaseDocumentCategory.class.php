<?php

/**
 * BaseDocumentCategory
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $document_id
 * @property integer $category_id
 * @property Document $Document
 * @property Category $Category
 * 
 * @method integer          getDocumentId()  Returns the current record's "document_id" value
 * @method integer          getCategoryId()  Returns the current record's "category_id" value
 * @method Document         getDocument()    Returns the current record's "Document" value
 * @method Category         getCategory()    Returns the current record's "Category" value
 * @method DocumentCategory setDocumentId()  Sets the current record's "document_id" value
 * @method DocumentCategory setCategoryId()  Sets the current record's "category_id" value
 * @method DocumentCategory setDocument()    Sets the current record's "Document" value
 * @method DocumentCategory setCategory()    Sets the current record's "Category" value
 * 
 * @package    dockeet
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseDocumentCategory extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('document_category');
        $this->hasColumn('document_id', 'integer', null, array(
             'notnull' => true,
             'type' => 'integer',
             ));
        $this->hasColumn('category_id', 'integer', null, array(
             'notnull' => true,
             'type' => 'integer',
             ));


        $this->index('document_category_unique', array(
             'fields' => 
             array(
              0 => 'document_id',
              1 => 'category_id',
             ),
             'type' => 'unique',
             ));
        $this->option('type', 'INNODB');
        $this->option('collate', 'utf8_unicode_ci');
        $this->option('charset', 'utf8');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Document', array(
             'local' => 'document_id',
             'foreign' => 'id',
             'onDelete' => 'cascade',
             'onUpdate' => 'cascade'));

        $this->hasOne('Category', array(
             'local' => 'category_id',
             'foreign' => 'id',
             'onDelete' => 'cascade',
             'onUpdate' => 'cascade'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}