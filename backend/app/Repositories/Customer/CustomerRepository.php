<?php

namespace App\Repositories\Customer;

use App\Models\Customer;
use App\Repositories\AbstractEloquentRepository;

class CustomerRepository extends AbstractEloquentRepository implements CustomerRepositoryInterface
{
    public function __construct(Customer $customer)
    {
        parent::__construct($customer);
    }
}
