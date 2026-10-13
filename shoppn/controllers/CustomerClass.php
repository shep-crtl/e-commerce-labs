<?php

require_once __DIR__ . '/../core/db_class.php';

class CustomerClass extends Database
{
    public function emailExists($email)
    {
        $stmt = $this->conn->prepare(
            'SELECT customer_email
             FROM customer
             WHERE customer_email = ?'
        );

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param('s', $email);

        $stmt->execute();

        $result = $stmt->get_result();

        $exists = $result->num_rows > 0;

        $stmt->close();

        return $exists;
    }


    public function addCustomer(
        $name,
        $email,
        $pass,
        $country,
        $city,
        $contact
    ) {
        $hash = password_hash($pass, PASSWORD_BCRYPT);

        $stmt = $this->conn->prepare(
            'INSERT INTO customer
            (
                customer_name,
                customer_email,
                customer_pass,
                customer_country,
                customer_city,
                customer_contact
            )
            VALUES (?, ?, ?, ?, ?, ?)'
        );

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            'ssssss',
            $name,
            $email,
            $hash,
            $country,
            $city,
            $contact
        );

        $success = $stmt->execute();

        if (!$success) {
            $stmt->close();
            return false;
        }

        $newCustomerId = $this->conn->insert_id;

        $stmt->close();

        return $newCustomerId;
    }


    public function getCustomerByEmail($email)
    {
        $stmt = $this->conn->prepare(
            'SELECT *
             FROM customer
             WHERE customer_email = ?'
        );

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param('s', $email);

        $stmt->execute();

        $result = $stmt->get_result();

        $row = $result->fetch_assoc();

        $stmt->close();

        return $row ?: false;
    }


    public function login($email, $pass)
    {
        $row = $this->getCustomerByEmail($email);

        if (!$row) {
            return false;
        }

        if (password_verify($pass, $row['customer_pass'])) {
            return $row;
        }

        return false;
    }
}

?>