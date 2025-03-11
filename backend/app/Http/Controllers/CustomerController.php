<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Customer\CustomerRepositoryInterface;

class CustomerController extends Controller
{
    private CustomerRepositoryInterface $customerRepository;

    public function __construct(CustomerRepositoryInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function index()
    {
        return response()->json($this->customerRepository->all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:customers',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $customer = $this->customerRepository->create($request->all());

        return response()->json($customer, 201);
    }

    public function show($id)
    {
        $customer = $this->customerRepository->find($id);
        if (!$customer) {
            return response()->json(['message' => 'Cliente não encontrado'], 404);
        }

        return response()->json($customer);
    }

    public function update(Request $request, $id)
    {
        $customer = $this->customerRepository->find($id);
        if (!$customer) {
            return response()->json(['message' => 'Cliente não encontrado'], 404);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|unique:customers,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $customer = $this->customerRepository->update($id, $request->all());

        return response()->json($customer);
    }

    public function destroy($id)
    {
        $customer = $this->customerRepository->find($id);
        if (!$customer) {
            return response()->json(['message' => 'Cliente não encontrado'], 404);
        }

        $this->customerRepository->delete($id);

        return response()->json(['message' => 'Cliente excluído com sucesso']);
    }
}
