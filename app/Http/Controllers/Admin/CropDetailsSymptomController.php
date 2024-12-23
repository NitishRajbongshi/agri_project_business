<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CropDetailsSymptomController extends Controller
{
    public function cropsymptomdetails()
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

        return view('admin.cropsymptomdetails.cropsymptomdetails', [
            'cropTypes' => $cropTypes,
            'cropNames' => $cropNames // Pass empty crop names initially
        ]);
    }

    public function getCrop(Request $request)
    {
        $cropTypeCd = $request->input('crop_type_cd');
        $cropNames = DB::table('ag_crop_name_master')
            ->where('crop_type_cd', $cropTypeCd)
            ->pluck('crop_name_desc', 'crop_name_cd');

        return response()->json($cropNames);
    }

    public function getDisease(Request $request)
    {
        $cropNameCd = $request->input('crop_name_cd');

        // Get the disease IDs associated with the selected crop
        $diseaseIds = DB::table('ag_crop_disease_symptom_and_crop_names_mapping')
            ->where('crop_name_cd', $cropNameCd)
            ->pluck('disease_cd');

        // Fetch disease names based on disease IDs
        $diseases = DB::table('ag_crop_disease_master')
            ->whereIn('disease_cd', $diseaseIds)
            ->pluck('disease_name', 'disease_cd');

        return response()->json($diseases);
    }
    public function getCropDiseaseSymptom(Request $request)
    {
        $diseaseNameCd = $request->input('disease_cd');

        // Fetch symptoms for both English (en) and Assamese (as)
        $cropDisease = DB::table('ag_crop_disease_symptom_master')
            ->where('crop_disease_cd', $diseaseNameCd)
            ->select('id', 'crop_disease_cd', 'symptom', 'language_cd')
            ->get()
            ->groupBy('language_cd'); // Group by language_cd

        return response()->json($cropDisease);
    }



    public function update(Request $request)
    {
        // dd($request);

        try {
            $validated = $request->validate([
                'id' => 'required|integer|exists:ag_crop_disease_symptom_master,id',
                'symptom' => 'required|string',
                'language_cd' => 'required|exists:ag_languages_master,lang_code',
                'disease_id' => 'required|integer|exists:ag_crop_disease_master,disease_cd',
            ], [
                'disease_id.required' => 'The disease is required.',
                'language_cd.required' => 'The language is required.',
            ]);


            DB::table('ag_crop_disease_symptom_master')
                ->where('id', $validated['id'])
                ->update([
                    'symptom' => $validated['symptom'],
                    'language_cd' => $validated['language_cd'],
                    'crop_disease_cd' => $validated['disease_id'],
                    'updated_at' => now()->setTimezone(value: 'Asia/Kolkata'),
                ]);

            return redirect()->back()->with('success', 'Symptom updated successfully.');
        } catch (Exception $e) {
            dd($e);
        }
    }

    public function destroy(Request $request)
    {

        $validated = $request->validate([
            'id' => 'required|integer|exists:ag_crop_disease_symptom_master',
        ]);

        DB::table('ag_crop_disease_symptom_master')
            ->where('id', $validated['id'])
            ->delete();

        return redirect()->back()->with('success', 'Symptom deleted successfully.');

    }


    public function create(Request $request)
    {

        if ($request->isMethod('post')) {

            $validated = $request->validate([
                'symptom' => 'required|string|max:255',
                'language_cd' => 'required|string|max:255',
                'disease_cd' => 'required|integer|exists:ag_crop_disease_master,disease_cd',
                'crop_type_cd' => 'required|integer|exists:ag_crop_name_master,crop_type_cd',
                'crop_name_cd' => 'required|integer|exists:ag_crop_name_master,crop_name_cd',
            ], [
                'crop_type_cd.required' => 'The crop type is required.',
                'crop_name_cd.required' => 'The crop name is required.',
                'disease_cd.required' => 'The disease is required.',
                'language_cd.required' => 'The language is required.',
                'symptom.required' => 'Symptom is required.',
            ]);




            $maxId = DB::table('ag_crop_disease_symptom_master')
                ->max('id');
            $nextmaxid = $maxId ? $maxId + 1 : 1;


            DB::table('ag_crop_disease_symptom_master')->insert([
                'id' => $nextmaxid,
                'symptom' => $validated['symptom'],
                'language_cd' => $validated['language_cd'],
                'crop_disease_cd' => $validated['disease_cd'],
                'updated_at' => now()->setTimezone('Asia/Kolkata'),
                'created_at' => now()->setTimezone('Asia/Kolkata'),
            ]);

            return redirect()->route('admin.cropsymptomdetails')->with('success', 'Symptom added successfully.');
        }

        // Fetch crop types for the dropdown
        $cropTypes = DB::table('ag_crop_type_master')->pluck('crop_type_desc', 'crop_type_cd');
        $cropTypes = $cropTypes->map(function ($description) {
            return strtoupper($description); // Convert each crop type description to uppercase
        });

        // Sort the crop types alphabetically
        $cropTypes = $cropTypes->sort();
        // Handle displaying the form
        return view('admin.cropsymptomdetails.createsymptom', ['cropTypes' => $cropTypes]);

    }


}
