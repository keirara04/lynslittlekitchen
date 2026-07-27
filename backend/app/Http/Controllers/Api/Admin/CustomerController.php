<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $base = DB::table('orders')
            ->leftJoin('users', 'users.id', '=', 'orders.user_id')
            ->groupByRaw('COALESCE(CAST(orders.user_id AS VARCHAR), orders.guest_email)')
            ->selectRaw('
                COALESCE(CAST(orders.user_id AS VARCHAR), orders.guest_email) as customer_key,
                MAX(COALESCE(users.name, orders.guest_name)) as name,
                MAX(COALESCE(users.email, orders.guest_email)) as email,
                MAX(orders.guest_phone) as phone,
                COUNT(*) as orders_count,
                SUM(orders.total) as total_spent,
                MAX(orders.created_at) as last_order_at
            ');

        $query = DB::query()->fromSub($base, 'customers');

        if ($search = $request->string('search')->trim()->value()) {
            $needle = '%'.mb_strtolower($search).'%';

            $query->where(function ($query) use ($needle) {
                $query->whereRaw('LOWER(name) LIKE ?', [$needle])
                    ->orWhereRaw('LOWER(email) LIKE ?', [$needle])
                    ->orWhereRaw('LOWER(phone) LIKE ?', [$needle]);
            });
        }

        $cutoff = now()->subDays(90);

        if ($status = $request->string('status')->value()) {
            if ($status === 'active') {
                $query->where('last_order_at', '>=', $cutoff);
            } elseif ($status === 'inactive') {
                $query->where('last_order_at', '<', $cutoff);
            }
        }

        $paginator = $query->orderBy('last_order_at', 'desc')
            ->paginate($request->integer('per_page', 20));

        $paginator->getCollection()->transform(fn ($row) => [
            'id' => $row->customer_key,
            'name' => $row->name,
            'email' => $row->email,
            'phone' => $row->phone,
            'orders_count' => (int) $row->orders_count,
            'total_spent' => (float) $row->total_spent,
            'last_order_at' => $row->last_order_at,
            'status' => Carbon::parse($row->last_order_at)->gte($cutoff) ? 'active' : 'inactive',
        ]);

        return response()->json([
            'data' => $paginator->items(),
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'from' => $paginator->firstItem(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'to' => $paginator->lastItem(),
                'total' => $paginator->total(),
            ],
        ]);
    }
}
