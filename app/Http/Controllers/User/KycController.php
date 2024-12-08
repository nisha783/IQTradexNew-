<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Kyc;
use Illuminate\Http\Request;

class KycController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("user.kyc.index");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the form inputs
        $request->validate([
            'name' => 'required|string|max:255',
            'document_number' => 'required|string|max:255|unique:kycs,document_number',
            'document_type' => 'required|string|max:255|in:identity_card,passport,driving_license,other',
            'address' => 'required|string|max:255',
            'selfie' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'front' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'back' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        // Handle the file uploads
        $selfiePath = $request->file('selfie')->store('kyc/selfies', 'public');
        $frontPath = $request->file('front')->store('kyc/fronts', 'public');
        $backPath = null;
        if ($request->hasFile('back')) {
            $backPath = $request->file('back')->store('kyc/backs', 'public');
        }

        // Store the KYC data in the database
        Kyc::create([
            'user_id' => auth()->id(),
            'name' => $request->input('name'),
            'document_number' => $request->input('document_number'),
            'document_type' => $request->input('document_type'),
            'address' => $request->input('address'),
            'selfie' => $selfiePath,
            'front' => $frontPath,
            'back' => $backPath,
            'status' => 'pending',
        ]);

        // Return back with a success message
        return redirect()->back()->with('success', 'KYC details submitted successfully!');
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
