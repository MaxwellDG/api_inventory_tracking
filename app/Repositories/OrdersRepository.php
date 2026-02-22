<?php

namespace App\Repositories;

use App\Models\Order;

class OrdersRepository
{
    public function getOrders(int $companyId, ?string $startDate = null, ?string $endDate = null, ?string $status = null, int $page = 1, ?string $label = null)
    {
        $query = Order::with(['items', 'user'])
            ->where('company_id', $companyId);

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($label) {
            $query->where('label', 'LIKE', '%' . $label . '%');
        }

        return $query->paginate(25, ['*'], 'page', $page);
    }
}
