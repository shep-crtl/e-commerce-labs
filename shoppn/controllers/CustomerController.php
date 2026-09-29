<?php

require_once __DIR__ . '/../classes/CustomerClass.php';

class CustomerController
{
    private $customerModel;


    public function __construct()
    {
        $this->customerModel = new CustomerClass();
    }


    public function register($data)
    {
        if ($this->customerModel->emailExists($data['email'])) {
            return [
                'success' => false,
                'error' => 'Email already registered.'
            ];
        }

        $customerId = $this->customerModel->addCustomer(
            $data['name'],
            $data['email'],
            $data['pass'],
            $data['country'],
            $data['city'],
            $data['contact']
        );

        if ($customerId !== false) {
            return [
                'success' => true,
                'customer_id' => $customerId
            ];
        }

        return [
            'success' => false,
            'error' => 'Registration failed. Please try again.'
        ];
    }


    public function login($email, $pass)
    {
        $customer = $this->customerModel->login(
            $email,
            $pass
        );

        if ($customer) {
            return $customer;
        }

        return [
            'success' => false,
            'error' => 'Invalid email or password.'
        ];
    }
}

?>