<?php

/**
 * MenuItem Model
 */

require_once BASE_PATH . '/core/Model.php';

class MenuItem extends Model
{
    protected $table = 'menu_item';

    /**
     * Find by code
     */
    public function findByCode($code)
    {
        return $this->findBy('code', $code);
    }
}
