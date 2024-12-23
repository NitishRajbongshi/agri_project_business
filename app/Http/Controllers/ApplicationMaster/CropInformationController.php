<?php

namespace App\Http\Controllers\ApplicationMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CropInformationController extends Controller
{
    public function cropinformation()
    {
        $data = DB::table('ag_crop_name_master as u')
            ->select('u.crop_name_cd', 'u.crop_name_desc', 'u.crop_name_desc_as', 'u.crop_registry_no', 'u.scientific_name')
            ->get();

        return view('admin.appmaster.cropinformation', ["data" => $data]);

    }
    public function update(Request $request)
    {
        $validated = $request->validate([
            'crop_name_cd' => 'required|integer',
            'crop_name_desc' => 'required|string',
            'crop_name_desc_as' => 'nullable|string',
            'crop_registry_no' => 'nullable|string',
            'scientific_name' => 'nullable|string',
        ]);

        // Check for duplicate Crop Registry Number only if it's provided
        if ($validated['crop_registry_no']) {
            $exists = DB::table('ag_crop_name_master')
                ->where('crop_registry_no', $validated['crop_registry_no'])
                ->where('crop_name_cd', '<>', $validated['crop_name_cd'])
                ->exists();

            if ($exists) {
                return redirect()->route('admin.appmaster.cropinformation')->with('error', 'The Crop Registry No is already in use.');
            }
        }

        DB::table('ag_crop_name_master')
            ->where('crop_name_cd', $validated['crop_name_cd'])
            ->update([
                'crop_name_desc' => $validated['crop_name_desc'],
                'crop_name_desc_as' => $validated['crop_name_desc_as'],
                'crop_registry_no' => $validated['crop_registry_no'],
                'scientific_name' => $validated['scientific_name'],
                'updated_at' => now()->setTimezone('Asia/Kolkata'),
            ]);

        return redirect()->route('admin.appmaster.cropinformation')->with('success', 'Crop information updated successfully.');
    }


    public function checkRegistryNo(Request $request)
    {
        $request->validate([
            'registry_no' => 'required|string',
            'crop_name_cd' => 'required|integer'
        ]);

        $exists = DB::table('ag_crop_name_master')
            ->where('crop_registry_no', $request->input('registry_no'))
            ->where('crop_name_cd', '<>', $request->input('crop_name_cd'))
            ->exists();

        return response()->json(['exists' => $exists]);
    }
    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            // Validate the request
            $validated = $request->validate([
                'crop_name' => 'required|string|max:255',
                'crop_name_assamese' => 'nullable|string|max:255',
                'crop_registry_no' => 'nullable|string|max:255',
                'scientific_name' => 'nullable|string|max:255',
                'crop_type_cd' => 'required|integer|exists:ag_crop_type_master,crop_type_cd',
            ], [
                'crop_type_cd.required' => 'The crop type is required.',
            ]);

            // Check for duplicate Crop Registry Number
            if ($validated['crop_registry_no']) {
                $exists = DB::table('ag_crop_name_master')
                    ->where('crop_registry_no', $validated['crop_registry_no'])
                    ->exists();

                if ($exists) {
                    return redirect()->back()->withInput()->withErrors(['crop_registry_no' => 'The Crop Registry No is already in use.']);
                }
            }

            // Generate the next crop_name_cd
            $maxId = DB::table('ag_crop_name_master')
                ->max('crop_name_cd');

            $nextId = $maxId ? $maxId + 1 : 1; // Increment the highest ID or start at 1

            // Insert the new crop information
            DB::table('ag_crop_name_master')->insert([
                'crop_name_cd' => $nextId,
                'crop_name_desc' => $validated['crop_name'],
                'crop_name_desc_as' => $validated['crop_name_assamese'],
                'crop_registry_no' => $validated['crop_registry_no'],
                'scientific_name' => $validated['scientific_name'],
                'crop_type_cd' => $validated['crop_type_cd'], // Add the crop type
                'updated_at' => now()->setTimezone('Asia/Kolkata'),
                'created_at' => now()->setTimezone('Asia/Kolkata'),
            ]);

            return redirect()->route('admin.appmaster.cropinformation')->with('success', 'Crop information created successfully.');
        }

        // Fetch crop types for the dropdown
        $cropTypes = DB::table('ag_crop_type_master')->pluck('crop_type_desc', 'crop_type_cd');
        $cropTypes = $cropTypes->map(function ($description) {
            return strtoupper($description); // Convert each crop type description to uppercase
        });

        // Sort the crop types alphabetically
        $cropTypes = $cropTypes->sort();
        // Handle displaying the form
        return view('admin.appmaster.createcrop', ['cropTypes' => $cropTypes]);
    }

    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'crop_name_cd' => 'required|integer|exists:ag_crop_name_master,crop_name_cd',
        ]);

        DB::table('ag_crop_name_master')
            ->where('crop_name_cd', $validated['crop_name_cd'])
            ->delete();

        return redirect()->route('admin.appmaster.cropinformation')->with('success', 'Crop information deleted successfully.');
    }
}
