<?php

/**
 * RestaurantTable Model
 */

require_once BASE_PATH . '/core/Model.php';

class RestaurantTable extends Model
{
    protected $table = 'restaurant_table';

    public function findByNumber($number)
    {
        return $this->findBy('number', $number);
    }

    public function findByToken($token)
    {
        return $this->findBy('order_token', $token);
    }
}
