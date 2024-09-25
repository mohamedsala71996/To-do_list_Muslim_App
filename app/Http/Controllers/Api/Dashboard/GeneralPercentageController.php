<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\GeneralPercentage;
use Illuminate\Http\Request;

class GeneralPercentageController extends Controller
{

    public function storeOrUpdate(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'percentage' => 'required|numeric|min:0|max:100',
        ]);

        // Find the existing general percentage, if any
        $generalPercentage = GeneralPercentage::first();

        if ($generalPercentage) {
            // Update the existing general percentage
            $generalPercentage->update([
                'percentage' => $validatedData['percentage'],
            ]);

            return response()->json([
                'message' => 'General percentage updated successfully',
                'data' => $generalPercentage,
            ], 200);
        } else {
            // Create a new general percentage
            $newGeneralPercentage = GeneralPercentage::create([
                'percentage' => $validatedData['percentage'],
            ]);

            return response()->json([
                'message' => 'General percentage created successfully',
                'data' => $newGeneralPercentage,
            ], 201);
        }
    }


    public function destroy()
    {
        // Find the general percentage by its ID
        $generalPercentage = GeneralPercentage::first();

        // If the record doesn't exist, return a 404 response
        if (!$generalPercentage) {
            return response()->json([
                'message' => 'General percentage not found',
            ], 404);
        }

        // Delete the record
        $generalPercentage->delete();

        return response()->json([
            'message' => 'General percentage deleted successfully',
        ], 200);
    }
}
