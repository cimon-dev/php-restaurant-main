<?php

/**
 * Recipe Model
 */

require_once BASE_PATH . '/core/Model.php';

class Recipe extends Model
{
    protected $table = 'recipe';

    /**
     * Get all ingredients for a menu item with current stock
     */
    public function getIngredientsByMenu($menuId)
    {
        $sql = "
            SELECT r.*, i.id as ingredient_id, i.name as ingredient_name, i.code, i.unit,
                   COALESCE(SUM(il.qty_change), 0) as current_qty
            FROM recipe r
            LEFT JOIN ingredient i ON r.ingredient_id = i.id
            LEFT JOIN inventory_log il ON i.id = il.ingredient_id
            WHERE r.menu_id = ?
            GROUP BY r.id, i.id, i.name, i.code, i.unit
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$menuId]);
        return $stmt->fetchAll();
    }

    /**
     * Check if there's enough inventory for a menu item
     * Returns array with 'sufficient' (bool) and 'missing' (array of missing ingredients)
     */
    public function checkInventoryForMenu($menuId, $qty = 1)
    {
        $ingredients = $this->getIngredientsByMenu($menuId);
        $missing = [];

        foreach ($ingredients as $ing) {
            $needed = $ing['qty'] * $qty;
            $available = $ing['current_qty'] ?? 0;
            
            if ($available < $needed) {
                $missing[] = [
                    'ingredient_name' => $ing['ingredient_name'],
                    'code' => $ing['code'],
                    'needed' => $needed,
                    'available' => $available,
                    'unit' => $ing['unit']
                ];
            }
        }

        return [
            'sufficient' => empty($missing),
            'missing' => $missing
        ];
    }
}
