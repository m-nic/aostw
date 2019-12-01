<?php

namespace App\Services;

use App\Database\SqlLite;

class CrudService
{

    private $db;

    public function __construct(SqlLite $db = null)
    {
        $this->db = $db;
    }

    /**
     * List users.
     *
     * @return array $users
     */
    public function browseUsers()
    {
        return $this->db->select("select * from users");
    }

    /**
     * Read user data based on ID.
     *
     * @param integer $id
     * @return array $user
     */
    public function readUser($id)
    {
        return $this->db->select("select * from users where id=:id", [
            'id' => (string)$id
        ])[0];
    }

    /**
     * Update user data based on ID.
     *
     * @param integer $id
     * @param array $newData
     * @return integer $result
     */
    public function editUser($id, $newData)
    {
        $fields = [];

        $newData = convertSoapArray($newData);

        // @TODO: DON'T RELY ON USER INPUT
        foreach ($newData as $field => $value) {
            $fields[] = "'{$field}'=:{$field}";
        }

        $fields = implode(',', $fields);

        return $this->db->execute("update users set {$fields} where id=:id", array_merge([
            'id' => $id
        ], $newData));
    }


    /**
     * Add user data based on ID.
     *
     * @param array $newData
     * @return int $result
     */
    public function addUser($newData)
    {
        $fields = [];

        $newData = convertSoapArray($newData);

        // @TODO: DON'T RELY ON USER INPUT
        foreach ($newData as $field => $value) {
            $fields[] = ":{$field}";
        }

        $fields = implode(',', $fields);
        $cols = str_replace(':', '', $fields);

        return $this->db->execute("insert into users({$cols}) values ({$fields})", $newData);
    }

    /**
     * Delete user data based on ID.
     *
     * @param integer $id
     * @return integer $status
     */
    public function deleteUser($id)
    {
        return $this->db->execute("delete from users where id=:id", [
            'id' => (string)$id
        ]);
    }

    /**
     * Read the users DB table;
     *
     * @return boolean
     */
    public function resetDb()
    {
        $this->db->execute('drop table if exists users');
        $this->db->execute(get_db_query('create-users-table'));

        for ($i = 1; $i < 12; $i++) {
            $this->db->execute(get_db_query('insert_user'), [
                'first_name' => $i . '-First',
                'last_name'  => $i . '-Last',
                'email'      => $i . '_test@example.com',
                'phone'      => '+40721 000 ' . str_pad($i, 3),
            ]);
        }

        return true;
    }
}