<?php
/**
 * User Model
 */

require_once BASE_PATH . '/core/Model.php';

class User extends Model {
    
    protected $table = 'users';
    
    /**
     * Find user by username
     */
    public function findByUsername($username) {
        return $this->findBy('username', $username);
    }
    
    /**
     * Authenticate user
     */
    public function authenticate($username, $password) {
        $user = $this->findByUsername($username);
        
        if (!$user) {
            return false;
        }
        
        // Check if user is active
        if (!$user['active']) {
            return false;
        }
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    /**
     * Create user
     */
    public function createUser($data) {
        // Hash password
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        return $this->insert($data);
    }
    
    /**
     * Update user
     */
    public function updateUser($id, $data) {
        // Hash password if provided
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }
        
        return $this->update($id, $data);
    }
    
    /**
     * Get active users
     */
    public function getActiveUsers() {
        return $this->where(['active' => 1], 'username', 'ASC');
    }
    
    /**
     * Generate JWT payload from user data
     */
    public function generateJWTPayload($user) {
        return [
            'id' => $user['id'],
            'username' => $user['username'],
            'fullname' => $user['fullname'],
            'role' => $user['role'],
            'active' => (bool)$user['active']
        ];
    }
}
