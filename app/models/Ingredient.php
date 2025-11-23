<?php

/**
 * Ingredient Model
 */

require_once BASE_PATH . '/core/Model.php';

class Ingredient extends Model
{
    protected $table = 'ingredient';

    /**
     * Find ingredient by code
     */
    public function findByCode($code)
    {
        return $this->findBy('code', $code);
    }
}
