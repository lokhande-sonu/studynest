<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Charge;
use Illuminate\Http\Request;

class ChargesController extends Controller
{
    /**
     * Display a listing of the charges.
     */
    public function index()
    {
        $charges = Charge::orderBy('charge_id', 'desc')->get();
        return view('management.charges', compact('charges'));
    }

    /**
     * Store a newly created charge.
     */
    public function store(Request $request)
    {
        $request->validate([
            'charge_name' => 'required|string|max:255',
            'charge_type' => 'required|string', // e.g., 'Percentage', 'Fixed Amount'
            'charge_value' => 'required|numeric',
            'charge_status' => 'required|in:0,1',
        ]);

        Charge::create([
            'charge_name' => $request->charge_name,
            'charge_type' => $request->charge_type,
            'charge_value' => $request->charge_value,
            'charge_status' => $request->charge_status,
            'charge_created_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Charge created successfully.');
    }

    /**
     * Update the specified charge.
     */
    public function update(Request $request, $id)
    {
        $charge = Charge::findOrFail($id);

        $request->validate([
            'charge_name' => 'required|string|max:255',
            'charge_type' => 'required|string',
            'charge_value' => 'required|numeric',
            'charge_status' => 'required|in:0,1',
        ]);

        $charge->update([
            'charge_name' => $request->charge_name,
            'charge_type' => $request->charge_type,
            'charge_value' => $request->charge_value,
            'charge_status' => $request->charge_status,
            'charge_updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Charge updated successfully.');
    }

    /**
     * Update the status of the specified charge.
     */
    public function updateStatus(Request $request, $id)
    {
        $charge = Charge::findOrFail($id);
        
        if ($request->has('status')) {
            $charge->charge_status = $request->status;
            $charge->charge_updated_at = now();
            $charge->save();
            return redirect()->back()->with('success', 'Charge status updated successfully.');
        }

        return redirect()->back()->with('error', 'Status not provided.');
    }

    /**
     * Remove the specified charge from storage.
     */
    public function destroy($id)
    {
        $charge = Charge::findOrFail($id);
        $charge->delete();

        return redirect()->back()->with('success', 'Charge deleted successfully.');
    }
}
