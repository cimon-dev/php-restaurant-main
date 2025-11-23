<?php

/**
 * IngredientCategory Model
 */

require_once BASE_PATH . '/core/Model.php';

class IngredientCategory extends Model
{
    protected $table = 'ingredient_category';
    public function findByName($name)
    {
        return $this->findBy('name', $name);
    }
}
