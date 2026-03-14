<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Order;
class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = Payment::whereHas('order', function ($query) {
                $query->where('user_id', Auth::id());
            })->where('status', 'paid') ->with('order')->get();
        $unpaidOrders = Auth::user()->orders()
    ->whereIn('status', ['pending', 'cancelled'])
    ->where('payment_status', '!=', 'paid')
    ->with('items.product')
    ->get();
        return view('payments.index', compact('payments', 'unpaidOrders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    //string $order_id ==> /payments/create/1 
    //if use $request->order_id ==> /payments/create?order_id=1
    public function create(Request $request,string $order_id)
    {
        $order = Auth::user()->orders()->findOrFail($order_id);
        return view('payments.create', compact('order'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'order_id' => 'required|exists:orders,order_id',
            'amount'   => 'required|numeric',
            'method'   => 'required|string',
        
            //no need to validate status as it will be set to pending by default
            //'status'   => 'required|string',
        ]);
        $validatedData['payment_date'] = now();
        $validatedData['status'] = 'paid'; // Default status is pending

        $order = Auth::user()->orders()->findOrFail($validatedData['order_id']);

        // Update payment record for the order instead of creating a new one
        $payment = $order->payments()->where('status', 'unpaid')->first();
        if ($payment) {
    // มี record เดิม → update
    $payment->update([
        'status'       => 'paid',
        'method'       => $validatedData['method'],
        'payment_date' => now(),
    ]);

} else {
    // ไม่มี record เดิม → create ใหม่
    $payment = $order->payments()->create([
        'amount'       => $validatedData['amount'],
        'method'       => $validatedData['method'],
        'status'       => 'paid',
        'payment_date' => now(),
    ]);
}

   
    Order::where('order_id', $order->order_id)->update(['payment_status' => 'paid']);
    $order->markAsProcessing();
  
        
        //use getKey() to get the primary key of the newly created payment record, which is payment_id in this case, and pass it to the route for showing payment details
        return redirect()->route('payments.show', $payment->getKey())
                         ->with('success', 'Payment created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $payment = Payment::where('payment_id', $id)
            ->whereHas('order', function ($query) {
                $query->where('user_id', Auth::id());
            })->with('order')->firstOrFail();

        return view('payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $payment = Payment::where('payment_id', $id)
            ->whereHas('order', function ($query) {
                $query->where('user_id', Auth::id());
            })->with('order')->firstOrFail();

        return view('payments.edit', compact('payment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $payment = Payment::where('payment_id', $id)
            ->whereHas('order', function ($query) {
                $query->where('user_id', Auth::id());
            })->with('order')->firstOrFail();

        $validatedData = $request->validate([
            'amount' => 'required|numeric',
            'method' => 'required|string',
            'status' => 'required|string',
        ]);
        $payment->update($validatedData);
        return redirect()->route('payments.index')->with('success', 'Payment updated successfully.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $payment = Payment::where('payment_id', $id)
                         ->where('user_id', Auth::id())
                         ->firstOrFail();
        $payment->delete();

        return redirect()->route('payments.index')->with('status', 'Payment deleted successfully!');
    }

    /**
     * Mark payment as complete
     */
    public function markComplete(string $id)
    {
        $payment = Payment::where('payment_id', $id)
                         ->where('user_id', Auth::id())
                         ->firstOrFail();

        $payment->markAsComplete();

        // Mark order as complete when payment is completed
        $payment->order->markAsComplete();

        return redirect()->route('payments.index')->with('success', 'Payment marked as complete.');
    }

    /**
     * Mark payment as failed
     */
    public function markFailed(string $id)
    {
        $payment = Payment::where('payment_id', $id)
                         ->where('user_id', Auth::id())
                         ->firstOrFail();

        $payment->markAsFailed();

        // Mark order as failed when payment fails and restore product stock
        $payment->order->markAsFailed();
        $payment->order->restoreProductStock();

        return redirect()->route('payments.index')->with('warning', 'Payment marked as failed.');
    }
}
