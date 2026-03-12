<?php

namespace App\Repositories;

use App\Models\Order;

class OrdersRepository
{
    public function getOrders(int $companyId, ?string $startDate = null, ?string $endDate = null, ?string $status = null, int $page = 1, ?int $labelId = null)
    {
        $query = Order::with(['items', 'user', 'labels'])
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

        if ($labelId) {
            $query->whereHas('labels', fn($q) => $q->where('labels.id', $labelId));
        }

        return $query->paginate(25, ['*'], 'page', $page);
    }
}
