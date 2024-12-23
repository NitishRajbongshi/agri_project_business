<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;

class CropDetailsProtectionController extends Controller
{
    public function cropprotectiondetails()
    {
        $cropTypes = DB::table('ag_crop_type_master')->pluck('crop_type_desc', 'crop_type_cd');
        $cropTypes = $cropTypes->toArray();

        // Sort the crop types alphabetically
        asort($cropTypes);

        // Convert all letters to uppercase
        $cropTypes = array_map(function ($description) {
            return strtoupper($description); // Convert all letters to uppercase
        }, $cropTypes);

        $cropNames = []; // Initialize empty array for crop names

        return view('admin.cropprotectiondetails.cropprotectiondetails', [
            'cropTypes' => $cropTypes,
            'cropNames' => $cropNames // Pass empty crop names initially
        ]);
    }

    public function getCropNam(Request $request)
    {
        $cropTypeCd = $request->input('crop_type_cd');
        $cropNames = DB::table('ag_crop_name_master')
            ->where('crop_type_cd', $cropTypeCd)
            ->pluck('crop_name_desc', 'crop_name_cd');

        return response()->json($cropNames);
    }
    public function getDiseases(Request $request)
    {
        $cropNameCd = $request->input('crop_name_cd');

        // Get the disease IDs associated with the selected crop
        $diseaseIds = DB::table('ag_crop_master_disease_n_crop_name_n_control_measure_mapping')
            ->where('crop_name_cd', $cropNameCd)
            ->pluck('disease_cd');

        // Fetch disease names based on disease IDs
        $diseases = DB::table('ag_crop_disease_master')
            ->whereIn('disease_cd', $diseaseIds)
            ->pluck('disease_name', 'disease_cd');

        return response()->json($diseases);
    }



    public function getCropDisease(Request $request)
    {
        $diseaseNameCd = $request->input('disease_cd');
        $cropNameCd = $request->input('crop_name_cd');  // Retrieve the crop_name_cd from the request



        // Get the crop disease details where both disease_cd and crop_name_cd match
        $cropDisease = DB::table('ag_crop_master_disease_n_crop_name_n_control_measure_mapping')
            ->where('disease_cd', $diseaseNameCd)        // Filter by disease_cd
            ->where('crop_name_cd', $cropNameCd)        // Filter by crop_name_cd
            ->select('mapping_id', 'disease_cd', 'crop_name_cd', 'control_measure', 'control_measure_as', 'imagepath1', 'imagepath2', 'imagepath3')
            ->get()
            ->map(function ($item) {
                $baseUrl = env('APP_URL') . '/external_images';

                $rootPath = config('filesystems.disks.external.root');

                $imagepath1 = $item->imagepath1 ? str_replace($rootPath, '', $item->imagepath1) : '';
                $imagepath2 = $item->imagepath2 ? str_replace($rootPath, '', $item->imagepath2) : '';
                $imagepath3 = $item->imagepath3 ? str_replace($rootPath, '', $item->imagepath3) : '';

                return [
                    'mapping_id' => $item->mapping_id,
                    'disease_cd' => $item->disease_cd ?? '',
                    'crop_name_cd' => $item->crop_name_cd ?? '',
                    'control_measure' => $item->control_measure ?? '',
                    'control_measure_as' => $item->control_measure_as ?? '',
                    'imagepath1' => $imagepath1 ? $baseUrl . $imagepath1 : '',
                    'imagepath2' => $imagepath2 ? $baseUrl . $imagepath2 : '',
                    'imagepath3' => $imagepath3 ? $baseUrl . $imagepath3 : '',
                ];
            });

        return response()->json($cropDisease);
    }




    public function update(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'mapping_id' => 'required|integer|exists:ag_crop_master_disease_n_crop_name_n_control_measure_mapping,mapping_id',
            'control_measure' => 'required|string',
            'control_measure_as' => 'nullable|string',
            'crop_name_id' => 'required|exists:ag_crop_name_master,crop_name_cd',
            'disease_id' => 'required|integer|exists:ag_crop_disease_master,disease_cd',
            'imagepath1' => 'nullable|file|mimes:jpeg,png,jpg',
            'imagepath2' => 'nullable|file|mimes:jpeg,png,jpg',
            'imagepath3' => 'nullable|file|mimes:jpeg,png,jpg',
        ]);


        $existingRecord = DB::table('ag_crop_master_disease_n_crop_name_n_control_measure_mapping')
            ->where('mapping_id', $validated['mapping_id'])
            ->first();

        if (!$existingRecord) {
            return redirect()->back()->withErrors(['error' => 'Record not found.']);
        }

        // Prepare data for update
        $updateData = [
            'control_measure' => $validated['control_measure'],
            'control_measure_as' => $validated['control_measure_as'],
            'disease_cd' => $validated['disease_id'],
            'crop_name_cd' => $validated['crop_name_id'],
            'updated_at' => now()->setTimezone('Asia/Kolkata'),
        ];

        // Folder path logic (reuse existing path or determine dynamically)
        $folderPath = dirname($existingRecord->imagepath1);

        $image1 = $request->file('imagepath1');
        $imageName1 = basename($existingRecord->imagepath1);
        $image1->storeAs($folderPath, $imageName1, 'external');
        $updateData['imagepath1'] = $folderPath . '/' . $imageName1; // Update the database path with the new image location

        dd($image1);
        dd($imageName1);
        dd($updateData);

        $rootPath = config('filesystems.disks.external.root');

        $imagepath1Relative = str_replace($rootPath . '/', '', $existingRecord->imagepath1);
        $imagepath2Relative = str_replace($rootPath . '/', '', $existingRecord->imagepath2);
        $imagepath3Relative = str_replace($rootPath . '/', '', $existingRecord->imagepath3);

        // Check if the folder exists, if not, create it
        if (!Storage::disk('external')->exists($folderPath)) {
            Storage::disk('external')->makeDirectory($folderPath);
        }

        // Check and replace imagepath1 if file is selected
        if ($request->hasFile('imagepath1')) {
            $image1 = $request->file('imagepath1');
            $imageName1 = basename($existingRecord->imagepath1);
            $image1->storeAs($folderPath, $imageName1, 'external');
            $updateData['imagepath1'] = $folderPath . '/' . $imageName1; // Update the database path with the new image location

            dd($image1);
            dd($imageName1);
            dd($updateData);
        } else {
            // If no new image is uploaded, retain the old image path
            $updateData['imagepath1'] = $existingRecord->imagepath1;
            dd($updateData);
        }

        // Check and replace imagepath2 if file is selected
        if ($request->hasFile('imagepath2')) {
            $image2 = $request->file('imagepath2');
            $imageName2 = basename($existingRecord->imagepath2);
            $image2->storeAs($folderPath, $imageName2, 'external');
            $updateData['imagepath2'] = $folderPath . '/' . $imageName2; // Update the database path with the new image location
        } else {
            // If no new image is uploaded, retain the old image path
            $updateData['imagepath2'] = $existingRecord->imagepath2;
            dd($updateData);
        }

        // Check and replace imagepath3 if file is selected
        if ($request->hasFile('imagepath3')) {
            $image3 = $request->file('imagepath3');
            $imageName3 = basename($existingRecord->imagepath3);
            $image3->storeAs($folderPath, $imageName3, 'external');
            $updateData['imagepath3'] = $folderPath . '/' . $imageName3; // Update the database path with the new image location
        } else {
            // If no new image is uploaded, retain the old image path
            $updateData['imagepath3'] = $existingRecord->imagepath3;
        }

        // Update the database record
        // DB::table('ag_crop_master_disease_n_crop_name_n_control_measure_mapping')
        //     ->where('mapping_id', $validated['mapping_id'])
        //     ->update($updateData);

        // $updatedControlMeasure = DB::table('ag_crop_master_disease_n_crop_name_n_control_measure_mapping')
        //     ->where('mapping_id', $validated['mapping_id'])
        //     ->first();


        return redirect()->back()->with('success', 'Control measure updated successfully.');

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Control measure updated successfully.',
        //     'updatedControlMeasure' => $updatedControlMeasure
        // ]);
    }





    public function destroy(Request $request)
    {
        // Validate the request to ensure we have a valid mapping_id
        $validated = $request->validate([
            'mapping_id' => 'required|integer|exists:ag_crop_master_disease_n_crop_name_n_control_measure_mapping',
        ]);

        // Fetch the record from the database to get the image paths
        $record = DB::table('ag_crop_master_disease_n_crop_name_n_control_measure_mapping')
            ->where('mapping_id', $validated['mapping_id'])
            ->first();

        if (!$record) {
            return redirect()->back()->withErrors(['error' => 'Record not found.']);
        }

        $rootPath = config('filesystems.disks.external.root');

        $imagePaths = [
            str_replace($rootPath, '', $record->imagepath1),
            str_replace($rootPath, '', $record->imagepath2),
            str_replace($rootPath, '', $record->imagepath3)
        ];

        // Delete the images if they exist in storage
        foreach ($imagePaths as $imagePath) {
            // Ensure the image path doesn't have a leading slash after removal
            $imagePath = ltrim($imagePath, '/');

            // Check if the image file exists in the external storage disk
            if (Storage::disk('external')->exists($imagePath)) {
                // Delete the image from the storage disk
                Storage::disk('external')->delete($imagePath);
            }
        }
        // Now delete the record from the database
        DB::table('ag_crop_master_disease_n_crop_name_n_control_measure_mapping')
            ->where('mapping_id', $validated['mapping_id'])
            ->delete();

        return redirect()->back()->with('success', 'Control measure and associated images deleted successfully.');
    }






    public function create(Request $request)
    {
        if ($request->isMethod('post')) {

            // Validate the incoming request data
            $validated = $request->validate([
                'control_measure' => 'required|string|max:255',
                'control_measure_as' => 'nullable|string|max:955',
                'disease_cd' => 'required|integer|exists:ag_crop_disease_master,disease_cd',
                'crop_type_cd' => 'required|integer|exists:ag_crop_name_master,crop_type_cd',
                'crop_name_cd' => 'required|integer|exists:ag_crop_name_master,crop_name_cd',
                'imagepath1' => 'required|file|mimes:jpeg,png,jpg', // Ensure it's a file
                'imagepath2' => 'required|file|mimes:jpeg,png,jpg',
                'imagepath3' => 'required|file|mimes:jpeg,png,jpg',
            ], [
                'crop_type_cd.required' => 'The crop type is required.',
                'crop_name_cd.required' => 'The crop name is required.',
                'disease_cd.required' => 'The disease is required.',
                'imagepath1.required' => 'Symptom Image 1 is required.',
                'imagepath2.required' => 'Symptom Image 2 is required.',
                'imagepath3.required' => 'Symptom Image 3 is required.',
            ]);


            // Retrieve crop name description and registry number from ag_crop_name_master
            $disease = DB::table('ag_crop_disease_master')
                ->where('disease_cd', $validated['disease_cd'])
                ->first();

            $crop = DB::table('ag_crop_name_master')
                ->where('crop_name_cd', $validated['crop_name_cd'])
                ->first();

            // Check if crop exists
            if (!$disease) {
                return redirect()->back()->withErrors(['disease_cd' => 'Disease not found.']);
            }

            $cropRegistryNo = $crop->crop_registry_no ?: str_pad(rand(1000000, 9999999), 7, '0', STR_PAD_LEFT);

            // Capitalize the disease name and concatenate with cropRegistryNo
            $folderName = strtoupper($disease->disease_name) . '_' . $cropRegistryNo;

            // Define the folder path
            $folderPath = $folderName;  // Folder path is just the disease name and cropRegistryNo

            // Create folder if it doesn't exist
            if (!Storage::disk('external')->exists($folderPath)) {
                Storage::disk('external')->makeDirectory($folderPath);
            }

            // Get the uploaded files
            $image1 = $request->file('imagepath1');
            $image2 = $request->file('imagepath2');
            $image3 = $request->file('imagepath3');

            // Generate unique names for the images (optional, using timestamps or random values)
            $imageName1 = $folderName . '_' . substr(uniqid(), -7) . '.' . $image1->getClientOriginalExtension();
            $imageName2 = $folderName . '_' . substr(uniqid(), -7) . '.' . $image2->getClientOriginalExtension();
            $imageName3 = $folderName . '_' . substr(uniqid(), -7) . '.' . $image3->getClientOriginalExtension();


            // Store the images in the folder
            $image1->storeAs($folderPath, $imageName1, 'external');
            $image2->storeAs($folderPath, $imageName2, 'external');
            $image3->storeAs($folderPath, $imageName3, 'external');

            $rootPath = config('filesystems.disks.external.root');

            // Get the next mapping ID
            $maxId = DB::table('ag_crop_master_disease_n_crop_name_n_control_measure_mapping')
                ->max('mapping_id');
            $nextmaxid = $maxId ? $maxId + 1 : 1;

            // Insert data into the database with the new image names as strings
            DB::table('ag_crop_master_disease_n_crop_name_n_control_measure_mapping')->insert([
                'mapping_id' => $nextmaxid,
                'control_measure' => $validated['control_measure'],
                'control_measure_as' => $validated['control_measure_as'],
                'crop_name_cd' => $validated['crop_name_cd'],
                'disease_cd' => $validated['disease_cd'],
                'imagepath1' => $rootPath . '/' . $folderPath . '/' . $imageName1,
                'imagepath2' => $rootPath . '/' . $folderPath . '/' . $imageName2,
                'imagepath3' => $rootPath . '/' . $folderPath . '/' . $imageName3,
                'updated_at' => now()->setTimezone('Asia/Kolkata'),
                'created_at' => now()->setTimezone('Asia/Kolkata'),
            ]);

            // return redirect()->route('admin.cropprotectiondetails')->with('success', 'Control measure added successfully.');

            return redirect()->back()->with('success', 'Control measure added successfully.');
        }

        // Fetch crop types for the dropdown
        $cropTypes = DB::table('ag_crop_type_master')->pluck('crop_type_desc', 'crop_type_cd');
        $cropTypes = $cropTypes->map(function ($description) {
            return strtoupper($description); // Convert each crop type description to uppercase
        });

        // Sort the crop types alphabetically
        $cropTypes = $cropTypes->sort();

        // Handle displaying the form
        return view('admin.cropprotectiondetails.createprotection', ['cropTypes' => $cropTypes]);
    }
}
