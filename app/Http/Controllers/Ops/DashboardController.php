<?php

namespace App\Http\Controllers\Ops;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\QuickTicket;
use Twilio\Rest\Client;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();

        $orderCheck = Ticket::where('status_id', 2)->whereDate('created_at', $today)->count();
        $mailAMC = Ticket::where('status_id', 6)->whereDate('created_at', $today)->count();
        $dealSlipReceived = Ticket::where('status_id', 9)->whereDate('created_at', $today)->count();
        $UnitsReceived = Ticket::whereIn('status_id', [12, 13])->whereDate('created_at', $today)->count();

        $sellExecuted = Ticket::where('status_id', '>', 8)
                    ->where('type', 2)
                    ->where('payment_type', 1)
                    ->whereDate('updated_at', $today)
                    ->sum('actual_total_amt');

        $buyQuickTicket = QuickTicket::where(function($query) use ($today) {
                                $query->whereDate('updated_at', $today)
                                    ->orWhereDate('created_at', $today);
                            })
                            ->where('type', 1)
                            ->sum('actual_total_amt');

        $sellQuickTicket = QuickTicket::where(function($query) use ($today) {
                                $query->whereDate('updated_at', $today)
                                    ->orWhereDate('created_at', $today);
                            })
                            ->where('type', 2)
                            ->sum('actual_total_amt');

        // Units To Be Transfered
        $unitsToBeTransfered = Ticket::where('type', 2)
                    ->whereBetween('status_id', [2, 5])
                    ->whereDate('updated_at', $today)
                    ->count();

        // Units Transfered
        $unitsTransfered = Ticket::where('type', 2)
                    ->where('status_id', '>', 6)
                    ->whereDate('updated_at', $today)
                    ->count();  

        // Redemption Amount Receivable
        $redemptionAmountReceivable = Ticket::where('type', 2)
                                            ->where('payment_type', 1)
                                            ->where('status_id', '>', 9)
                                            ->whereDate('updated_at', $today)
                                            ->sum('refund');

        // Redemption Amount Received
        $redemptionAmountReceived = Ticket::where('type', 2)
                                            ->wherePaymentType(1)
                                            ->where('status_id', '>', 12)
                                            ->whereDate('updated_at', $today)
                                            ->sum('refund');

        // Refund Amount Received
        $refundAmountReceived = Ticket::where('type', 1)
                                            ->wherePaymentType(1)
                                            ->where('status_id', '>', 11)
                                            ->whereDate('updated_at', $today)
                                            ->sum('refund');

        // Graph
        $statuses = [
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14
        ];

        $arrangedBuyCounts = [];
        $arrangedSellCounts = [];
        foreach ($statuses as $status) {
            $buyCount = Ticket::whereStatusId($status)
                            ->where('type', 1)
                            ->where('payment_type', 1)
                            ->whereDate('updated_at', $today)
                            ->count();
            $arrangedBuyCounts[] = $buyCount;

            $sellCount = Ticket::whereStatusId($status)
                            ->where('type', 2)
                            ->where('payment_type', 1)
                            ->whereDate('updated_at', $today)
                            ->count();
            $arrangedSellCounts[] = $sellCount;
        }

        $data = [
            'orderCheck' => $orderCheck,
            'mailAMC' => $mailAMC,
            'dealSlipReceived' => $dealSlipReceived,
            'UnitsReceived' => $UnitsReceived,
            'redemptionAmountReceivable' => $redemptionAmountReceivable,
            'redemptionAmountReceived' => $redemptionAmountReceived,
            'refundAmountReceived' => $refundAmountReceived,           
            'arrangedBuyCounts' => $arrangedBuyCounts,
            'arrangedSellCounts' => $arrangedSellCounts,
            'unitsToBeTransfered' => $unitsToBeTransfered,
            'unitsTransfered' => $unitsTransfered
        ];
        return view('ops.dashboard', compact('data'));
    }
}
