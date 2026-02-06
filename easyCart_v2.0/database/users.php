<?php
/**
 * User Database Operations
 * Phase 6 - Database Integration
 */

require_once __DIR__ . '/db.php';

/**
 * Get user by email
 */
function getUserByEmailDB($email)
{
    $sql = "SELECT * FROM customer_entity WHERE email = :email";
    return fetchOne($sql, [':email' => $email]);
}

/**
 * Get user by ID
 */
function getUserByIdDB($userId)
{
    $sql = "SELECT * FROM customer_entity WHERE entity_id = :id";
    return fetchOne($sql, [':id' => $userId]);
}

/**
 * Create new user
 */
function createUserDB($email, $password, $name)
{
    // Hash password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $userData = [
        'email' => $email,
        'password_hash' => $passwordHash,
        'name' => $name,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];

    return dbInsert('customer_entity', $userData);
}

/**
 * Verify user credentials
 */
function verifyUserCredentialsDB($email, $password)
{
    $user = getUserByEmailDB($email);

    if (!$user) {
        return ['success' => false, 'message' => 'Invalid email or password'];
    }

    // Verify password
    if (password_verify($password, $user['password_hash'])) {
        return [
            'success' => true,
            'user_id' => $user['entity_id'],
            'name' => $user['name'],
            'email' => $user['email']
        ];
    }

    return ['success' => false, 'message' => 'Invalid email or password'];
}

/**
 * Update user
 */
function updateUserDB($userId, $data)
{
    $data['updated_at'] = date('Y-m-d H:i:s');

    dbUpdate(
        'customer_entity',
        $data,
        'entity_id = :id',
        [':id' => $userId]
    );

    return true;
}
