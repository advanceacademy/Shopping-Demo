<?php

namespace App\Models;

use App\Helpers\HashPassword;
use PDOException;
use PDO;

class UserModel extends BaseModel
{
    public function findById($id)
    {
        $sql = "SELECT *
                FROM `users`
                WHERE `id` = :id
            ";

        $query = $this->db->prepare($sql);
        $query->execute([
            'id' => $id,
        ]);
        $user = $query->fetch(\PDO::FETCH_ASSOC);

        return $user;
    }

    public function storeUser($name, $surname, $email, $password, $phone, $roles = ['client'])
    {
        $params = [
            'firstname' => $name,
            'lastname' => $surname,
            'email' => $email,
            'password' => HashPassword::hash($password),
            'phone_number' => $phone,
            'token_expired_at' => 0,
        ];

        $sql = "INSERT INTO `users` (
            `firstname`,
            `lastname`,
            `email`,
            `phone_number`,
            `password`,
            `token`,
            `token_expired_at`,
            `created_at`,
            `created_by`,
            `updated_at`,
            `updated_by`
        ) VALUES (
            :firstname,
            :lastname,
            :email,
            :phone_number,
            :password,
            '',
            :token_expired_at,
            NOW(),
            1,
            NOW(),
            1
        )";

        try {
            $query = $this->db->prepare($sql);
            $query->execute($params);
            $userId = $this->db->lastInsertId();

            if ($userId) {
                $userModel = new UserModel();
                $roleList = $userModel->getRolesByName($roles);

                $sql = "INSERT INTO `user_role` (
                            `user_id`,
                            `role_id`
                        ) VALUES ";

                foreach ($roleList as $role) {
                    $sql .= "(?, ?),";
                    $userParams [] = $userId;
                    $userParams [] = $role;
                }

                $sql = rtrim($sql, ",");
                $query = $this->db->prepare($sql);
                $query->execute($userParams);
            }

            return $this->showUser($userId);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    public function updateUser($userId, $name, $surname, $email, $password, $phone, $roles = ['client'], $updaterUser = null)
    {
        $params = [
            'firstname' => $name,
            'lastname' => $surname,
            'email' => $email,
            'phone_number' => $phone,
            'id' => $userId,
            'updated_by' => $updaterUser,
            'token' => '',
        ];

        if ($password) {
            $params['password'] = HashPassword::hash($password);
        }

        $sql = "UPDATE `users` SET
                    `firstname` = :firstname,
                    `lastname` = :lastname,
                    `email` = :email,
                    ".($password ? '`password` = :password,' : '')."
                    `phone_number` = :phone_number,
                    `token` = :token,
                    `updated_at` = NOW(),
                    `updated_by` =  :updated_by
                WHERE
                    `id` = :id
                LIMIT 1
            ";

        try {
            $query = $this->db->prepare($sql);
            $query->execute($params);

            if (!$roles || !count($roles)) {
                return $this->showUser($userId);
            }

            //Get roles list
            $userModel = new UserModel();
            $roleList = $userModel->getRolesByName($roles);

            //Remove roles
            $this->removeRolesByUserId($userId);

            //Add roles
            $sql = "INSERT INTO `user_role` (
                        `user_id`,
                        `role_id`
                    ) VALUES ";

            foreach ($roleList as $role) {
                $sql .= "(?, ?),";
                $userParams[] = $userId;
                $userParams[] = $role;
            }

            $sql = rtrim($sql, ",");
            $query = $this->db->prepare($sql);
            $query->execute($userParams);


            return $this->showUser($userId);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    public function updateUserToken($userId, $token = null, int $tokenExpiredAt = 0)
    {
        $sql = "UPDATE `users` SET
                    `token` = :token,
                    `token_expired_at` = :token_expired_at,
                    `updated_at` = NOW(),
                    `updated_by` = :updated_by
                WHERE
                    `id` = :id
                LIMIT 1
            ";

        $query = $this->db->prepare($sql);
        $query->execute([
            'id' => $userId,
            'updated_by' => $userId,
            'token_expired_at' => $tokenExpiredAt ?? 0,
            'token' => $token ?? ''
        ]);

        return $this->showUser($userId);
    }

    public function showUser($id)
    {
        $sql = "SELECT
                    `id`,
                    `email`,
                    `firstname` as 'name',
                    `lastname` as 'surname',
                    `phone_number` as 'phone',
                    `created_at` as 'created'
                FROM `users`
                WHERE `id` = :id
            ";
        $query = $this->db->prepare($sql);
        $query->execute(['id' => $id]);
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return null;
        }

        $user['created'] = date('c', strtotime($user['created']));
        $userModel = new UserModel();
        $userRoles = $userModel->getRolesByUserId($user['id']);

        if ($userRoles) {
            foreach ($userRoles as $role) {
                $user['roles'][] = $role;
            }
        }

        return $user;
    }

    public function getRolesByUserId($userId) {

        $sql = "SELECT
                    `name`
                FROM `user_role` ur
                LEFT JOIN `roles` r
                    ON ur.`role_id` = r.`id`
                WHERE ur.`user_id` = :user_id
                GROUP BY `name`
            ";

        $query = $this->db->prepare($sql);
        $query->execute(['user_id' => $userId]);

        $userRoles = [];

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $userRoles[] = $row['name'];
        }

        if ($userRoles) {
            return $userRoles;
        } else {
            return null;
        }

        return $userRoles;
    }

    public function getRolesByUserList(array $users = [])
    {
        $values = implode(", ", array_fill(0, count($users), '?'));

        $sql = "SELECT
                    r.`name` as 'role_name',
                    ur.`user_id`
                    FROM `user_role` ur
                    LEFT JOIN `roles` r
                        ON ur.`role_id` = r.`id`
                    WHERE ur.`user_id` IN(" . $values . ")
                    GROUP BY
                        r.`name`,
                        ur.`user_id`
            ";

        $query = $this->db->prepare($sql);
        $query->execute(array_keys($users));

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $users[$row['user_id']]['roles'][] = $row['role_name'];
        }

        return $users;
    }

    public function storeRole(int $userId, $roleId)
    {
        $sql = "INSERT INTO `user_role` (
                    `user_id`,
                    `role_id`
                ) VALUES (
                    :user_id,
                    :role_id
                )";

        $query = $this->db->prepare($sql);
        $query->execute([
            'user_id' => $userId,
            'role_id' => $roleId
        ]);
        $storeRole = $query->fetch(PDO::FETCH_ASSOC);

        return $storeRole;
    }

    public function getRoleByName($name = '')
    {
        $params =  [
            'name' => $name,
        ];

        $sql = "SELECT
                `id`
                FROM `roles`
                WHERE `name` = :name
        ";

        $query = $this->db->prepare($sql);
        $query->execute($params);
        $roleId = $query->fetch(PDO::FETCH_ASSOC);

        return $roleId;
    }

    public function findByEmail($email = '')
    {
        $sql = "SELECT
                    u.`id`,
                    u.`email`,
                    u.`firstname` as 'name',
                    u.`lastname` as 'surname',
                    u.`phone_number` as 'phone',
                    u.`created_at` as 'created',
                    u.`password`
                    FROM `users` u
                    WHERE u.`email` LIKE :email
                ";

        $query = $this->db->prepare($sql);
        $query->execute([
            'email' => $email,
        ]);
        $user = $query->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            return null;
        }
        $id = $user['id'] ?? '';

        $sql = "SELECT
                    r.`name`
                FROM `users` u
                LEFT JOIN `user_role` ur
                    ON u.`id`=ur.`user_id`
                LEFT JOIN `roles` r
                    ON r.`id`=ur.`role_id`
                WHERE u.`id` LIKE :id
                GROUP BY r.`name`
        ";
        $query = $this->db->prepare($sql);
        $query->execute([
            'id' => $id,
        ]);

        $user['created'] = date('c', strtotime($user['created']));
        $user['roles'] = [];

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $user['roles'][] = $row['name'];
        }

        return $user;
    }

    public function listUsers()
    {
        $sql = "SELECT
                    `id`,
                    `email`,
                    `firstname` as 'name',
                    `lastname` as 'surname',
                    `phone_number` as 'phone',
                    `created_at` as 'created'
                FROM `users`
            ";

        $query = $this->db->prepare($sql);
        $query->execute();
        $users = [];

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $users[$row['id']] = $row;
            $users[$row['id']]['created'] = date('c', strtotime($users[$row['id']]['created']));
        }

        $users = $this->getRolesByUserList($users);

        return array_values($users);
    }

    public function deleteUser($id)
    {
        $sql = "DELETE
                    FROM `users`
                    WHERE `users`.`id` = :id
                ";

        $query = $this->db->prepare($sql);

        if ($query->execute(['id' => $id])) {
            $this->removeRolesByUserId($id);
        }

        return;
    }

    public function removeRolesByUserId($userId)
    {
        $sql = "DELETE FROM `user_role`
                WHERE `user_id` = :userId";

        $query = $this->db->prepare($sql);
        $query->execute(['userId' => $userId]);

        return;
    }

    public function getRolesByName($roles = [])
    {
        $sql = "SELECT
                    `id`
                FROM `roles`
                WHERE `name` IN (
        ";
        foreach ($roles as $role) {
            $sql .= "?,";
        }
        $sql = rtrim($sql, ",");
        $sql .= ")";
        $query = $this->db->prepare($sql);
        $query->execute($roles);
        $rolesId = [];
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $rolesId[] = $row['id'];
        }

        return $rolesId;
    }

    public function isUserEmailExists($email, $id = null)
    {
        $sql = "SELECT `id`
                FROM `users`
                WHERE `email` LIKE :email
                " . ($id >= 1 ? ' AND `id` <> :id' : '') . "
        ";

        $params = ['email' => $email];
        if ($id >= 1) {
            $params['id'] = $id;
        }

        $query = $this->db->prepare($sql);
        $query->execute($params);
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            return true;
        } else {
            return false;
        }
    }

    public function isForgotTokenValid(string $token, string $email)
    {
        $sql = "SELECT `id`
                FROM `users`
                WHERE
                    `email` = :email
                    AND `token` = :token
                    AND `token_expired_at` > UNIX_TIMESTAMP()
                LIMIT 1
        ";

        $query = $this->db->prepare($sql);
        $query->execute([
            'email' => $email,
            'token' => $token,
        ]);
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            return true;
        } else {
            return false;
        }
    }
}
