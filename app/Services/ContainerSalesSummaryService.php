<?php

namespace App\Services;

use App\Models\Container;
use App\Models\CustomerPayment;
use App\Models\Sale;
use Illuminate\Support\Collection;

class ContainerSalesSummaryService
{
    /**
     * Sales that include lines from this container, with amounts attributed by line share of each sale total.
     *
     * @return array{summary: array{total: float, paid: float, remaining: float, count: int}, sales: Collection<int, array<string, mixed>>, customerPayments: Collection<int, array<string, mixed>>, payableSales: Collection<int, array<string, mixed>>}
     */
    public function forContainer(Container $container): array
    {
        $sales = Sale::query()
            ->where('status', '!=', 'cancelled')
            ->whereHas('saleItems', fn ($q) => $q->where('container_id', $container->id))
            ->with('customer')
            ->orderByDesc('sale_date')
            ->get();

        $rows = collect();
        $totalLines = 0.0;
        $totalPaid = 0.0;
        $totalRemaining = 0.0;

        foreach ($sales as $sale) {
            $sale->append(['paid_amount', 'remaining_amount']);
            $linesSum = (float) $sale->saleItems()->where('container_id', $container->id)->sum('line_total');
            if ($linesSum <= 0) {
                continue;
            }
            $saleTotal = (float) $sale->total;
            $ratio = $saleTotal > 0 ? $linesSum / $saleTotal : 1.0;
            $paidPortion = round((float) $sale->paid_amount * $ratio, 2);
            $remainingPortion = round((float) $sale->remaining_amount * $ratio, 2);

            $totalLines += $linesSum;
            $totalPaid += $paidPortion;
            $totalRemaining += $remainingPortion;

            $rows->push([
                'id' => $sale->id,
                'sale_date' => $sale->sale_date?->format('Y-m-d'),
                'customer_name' => $sale->customer?->name,
                'status' => $sale->status,
                'sale_total' => round($saleTotal, 2),
                'lines_subtotal' => round($linesSum, 2),
                'paid_portion' => $paidPortion,
                'remaining_portion' => $remainingPortion,
            ]);
        }

        $remainingRounded = round($totalRemaining, 2);

        $rowSaleIds = $rows->pluck('id')->all();

        $payableSales = $sales
            ->filter(fn (Sale $s) => in_array($s->id, $rowSaleIds, true)
                && in_array($s->status, ['draft', 'confirmed'], true))
            ->map(fn (Sale $s) => [
                'id' => $s->id,
                'customer_name' => $s->customer?->name,
                'sale_date' => $s->sale_date?->format('Y-m-d'),
                'remaining_amount' => round((float) $s->remaining_amount, 2),
            ])
            ->values();

        $customerPayments = collect();
        if ($rowSaleIds !== []) {
            $remainingAfterByPaymentId = [];
            foreach ($rowSaleIds as $sid) {
                $saleForPayments = $sales->firstWhere('id', $sid);
                if (! $saleForPayments) {
                    continue;
                }
                $saleTotal = (float) $saleForPayments->total;
                $ordered = CustomerPayment::query()
                    ->where('sale_id', $sid)
                    ->orderBy('payment_date')
                    ->orderBy('id')
                    ->get();
                $cumulative = 0.0;
                foreach ($ordered as $p) {
                    $cumulative += (float) $p->amount;
                    $remainingAfterByPaymentId[$p->id] = round(max(0, $saleTotal - $cumulative), 2);
                }
            }

            $customerPayments = CustomerPayment::query()
                ->whereIn('sale_id', $rowSaleIds)
                ->with(['sale.customer'])
                ->orderByDesc('payment_date')
                ->orderByDesc('id')
                ->get()
                ->map(fn (CustomerPayment $p) => [
                    'id' => $p->id,
                    'sale_id' => $p->sale_id,
                    'amount' => round((float) $p->amount, 2),
                    'payment_date' => $p->payment_date?->format('Y-m-d'),
                    'method' => $p->method,
                    'reference' => $p->reference,
                    'customer_name' => $p->sale?->customer?->name,
                    'sale_date' => $p->sale?->sale_date?->format('Y-m-d'),
                    'remaining_after' => $remainingAfterByPaymentId[$p->id] ?? null,
                ])
                ->values();
        }

        return [
            'summary' => [
                'total' => round($totalLines, 2),
                'paid' => round($totalPaid, 2),
                'remaining' => $remainingRounded,
                'count' => $rows->count(),
            ],
            'sales' => $rows,
            'customerPayments' => $customerPayments,
            'payableSales' => $payableSales,
        ];
    }
}
