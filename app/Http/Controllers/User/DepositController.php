<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Transaction;
use App\Services\CoinPaymentsService;
use Illuminate\Http\Request;

class DepositController extends Controller
{

    protected $coinPayments;

    public function __construct(CoinPaymentsService $coinPaymentsService)
    {
        $this->coinPayments = $coinPaymentsService;
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('user.deposit.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('user.deposit.create');
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
{
    // Validate the request data
    $request->validate([
        'amount' => 'required|numeric|min:1',
        'transaction_number' => 'required|string',
        'screenshot' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Image validation
    ]);

        // Handle file upload
        $screenshotPath = $request->file('screenshot')->store('screenshots', 'public'); // Store the screenshot file

        // Save the deposit data in the `tid` model
        $tid = \App\Models\tid::create([
            'user_id' => auth()->user()->id, // Current logged-in user
            'amount' => $request->amount,
            'transaction_number' => $request->transaction_number, // Transaction ID entered by user
            'status' => 'pending', // Default status when the deposit is created
            'screenshot' => $screenshotPath, // Store the file path of the screenshot
        ]);

        // Redirect to a success page or a confirmation page
        return back()->with('success','succesfuly');
    
  
}


    public function webhook(Request $request)
    {
        info('Webhook received: ' . json_encode($request->all()));
        try {
            $data = $this->coinPayments->verifyIPN($request->all());

            // Log or update database with transaction details
            info('CoinPayments IPN Verified: ', $data);

            // adding balance to user account

            $payment = Payment::where('txn_id', $data['txn_id'])->firstOrFail();
            if ($data['status'] >= 1) {


                $transaction = Transaction::firstOrCreate([
                    'user_id' => $payment->user_id,
                    'payment_id' => $payment->id,
                    'amount' => $data['amount1'],
                    'status' => 'approved',
                    'type' => 'deposit',
                    'sum' => true,
                    'reference' => "USDT Gateway " . $payment->txn_id . " Deposit",
                ]);

                // check if recently created
                if ($transaction->wasRecentlyCreated) {
                    info("New transaction created");
                    $payment->status = "completed";
                    $payment->save();

                    info("Payment updated");
                } else {
                    info("Transaction already exists");
                }

                return response()->json([
                    'success' => true,
                    'status' => 200,
                    'message' => 'IPN received successfully'
                ]);
            } else {
                info('CoinPayments Status is Still pending ' . $data['status']);
                $payment->status = "pending";
                $payment->save();
            }
        } catch (\Exception $e) {
            info('CoinPayments IPN verification failed: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
