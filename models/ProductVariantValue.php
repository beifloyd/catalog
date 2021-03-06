<?php
if (!defined('IN_CMS')) { exit(); }

/**
 * Catalog
 * 
 * The catalog plugin adds a catalog or webshop to Wolf CMS.
 * 
 * @package     Plugins
 * @subpackage  catalog
 * 
 * @author      Nic Wortel <nic.wortel@nth-root.nl>
 * @copyright   Nic Wortel, 2012
 * @version     0.1.0
 */

use_helper('ActiveRecord');

class ProductVariantValue extends ActiveRecord {
    const TABLE_NAME = 'catalog_product_variant_value';
    
    static $belongs_to = array(
        'product_variant' => array(
            'class_name' => 'ProductVariant',
            'foreign_key' => 'product_variant_id'
        ),
        'attribute' => array(
            'class_name' => 'Attribute',
            'foreign_key' => 'attribute_id'
        ),
        'unit' => array(
            'class_name' => 'AttributeUnit',
            'foreign_key' => 'attribute_unit_id'
        )
    );
    
    public $id;
    public $product_variant_id;
    public $attribute_id;
    public $attribute_unit_id;
    public $flat_value = '';
    
    public function __construct() {
        if (!isset($this->attribute)) {
            $this->attribute = Attribute::findById($this->attribute_id);
        }
    }
    
    public function beforeSave() {
        $this->attribute_unit_id = (isset($this->unit)) ? $this->unit : null;
        $this->flat_value = (isset($this->value)) ? $this->value : null;
        
        return true;
    }
    
    public function afterSave() {
        $casted_value_class = 'Value' . ucfirst(strtolower($this->attribute->type->data_type));
        
        if ($value = $casted_value_class::findByProductVariantValueId($this->id)) {
            
        }
        else {
            $value = new $casted_value_class();
        }
        
        $value->value = $this->flat_value;
        $value->product_variant_value_id = $this->id;
        
        if (!$value->save()) {
            print_r($value);
            die;
        }
        
        return true;
    }
    
    public function getColumns() {
        return array(
            'id', 'product_variant_id', 'attribute_id', 'attribute_unit_id', 'flat_value'
        );
    }
}