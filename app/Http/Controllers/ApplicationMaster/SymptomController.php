<?php

namespace App\Http\Controllers\ApplicationMaster;

use App\Http\Controllers\Controller;
use App\Models\Appmaster\CropDisease;
use App\Models\Appmaster\CropType;
use App\Models\Appmaster\CropName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SymptomController extends Controller
{

    public function index(Request $request)
    {
        // Fetch crop types
        $cropTypes = DB::table('ag_crop_type_master')
                ->select('crop_type_cd', DB::raw('UPPER(crop_type_desc) as crop_type_desc' ))
                ->orderBy('crop_type_desc')->get();

        $cropNames = DB::table('ag_crop_name_master')
                ->select('crop_name_cd', DB::raw('UPPER(crop_name_desc) as crop_name_desc' ), 'crop_name_desc_as')
                ->orderBy('crop_name_desc')->get();
        
        $diseases = DB::table('ag_crop_disease_master')
            ->select('disease_cd', DB::raw('UPPER(disease_name) as disease_name' ) , 'crop_type_cd')
            ->get()
            ->groupBy('crop_type_cd');
       
        // Fetch symptoms data
        $symptoms = DB::table('ag_crop_disease_symptom_master as u')
            ->join('ag_languages_master as l', 'u.language_cd', '=', 'l.lang_code')
            ->join('ag_crop_disease_master as d', 'u.crop_disease_cd', '=', 'd.disease_cd')
            ->select('u.id', 'u.symptom', 'u.language_cd', 'd.disease_cd', 'l.lang_name as lang_name', 'd.disease_name', 'd.crop_type_cd')
            ->get();

        // Fetch languages
        $languages = DB::table('ag_languages_master')->pluck('lang_name', 'lang_code');

        // Return view with all the necessary data
        return view('admin.appmaster.symptom', [
            'cropTypes' => $cropTypes,
            'cropNames' => $cropNames,
            'diseases' => $diseases,
            'symptoms' => $symptoms,
            'languages' => $languages
        ]);
    }


    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer|exists:ag_crop_disease_symptom_master,id',
            'symptom' => 'required|string',
            'language_cd' => 'required|exists:ag_languages_master,lang_code',
            'disease_cd' => 'required|integer|exists:ag_crop_disease_master,disease_cd',
        ], [
            'disease_cd.required' => 'The disease is required.',
            'language_cd.required' => 'The language is required.',
        ]);


        DB::table('ag_crop_disease_symptom_master')
            ->where('id', $validated['id'])
            ->update([
                'symptom' => $validated['symptom'],
                'language_cd' => $validated['language_cd'],
                'crop_disease_cd' => $validated['disease_cd'],
                'updated_at' => now()->setTimezone('Asia/Kolkata'),
            ]);

        return redirect()->route('admin.appmaster.symptom')->with('success', 'Symptom updated successfully.');
    }

    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            // Validate the request
            $validated = $request->validate([
                'symptom' => 'required|string|max:255',
                'language_cd' => 'required|string|max:255',
                'disease_cd' => 'required|integer|exists:ag_crop_type_master,crop_type_cd',
            ], [
                'disease_cd.required' => 'Disease is required.',
                'language_cd.required' => 'Language is required.',
            ]);



            $maxId = DB::table('ag_crop_disease_symptom_master')
                ->max('id');

            $nextId = $maxId ? $maxId + 1 : 1; // Increment the highest ID or start at 1

            //     // Insert the new crop information
            DB::table('ag_crop_disease_symptom_master')->insert([
                'id' => $nextId,
                'symptom' => $validated['symptom'],
                'crop_disease_cd' => $validated['disease_cd'],
                'language_cd' => $validated['language_cd'],
                'updated_at' => now()->setTimezone('Asia/Kolkata'),
                'created_at' => now()->setTimezone('Asia/Kolkata'),
            ]);

            return redirect()->route('admin.appmaster.symptom')->with('success', 'Symptom created successfully.');
        }

        // // Fetch crop types for the dropdown
        $disease = DB::table('ag_crop_disease_master')->pluck('disease_name', 'disease_cd');
        $disease = $disease->map(function ($description) {
            return strtoupper($description); // Convert each crop type description to uppercase
        });

        // Sort the crop types alphabetically
        $disease = $disease->sort();

        $languages = DB::table('ag_languages_master')->pluck('lang_name', 'lang_code');


        return view('admin.appmaster.createsymptom', ['disease' => $disease, 'languages' => $languages]);

    }
    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer|exists:ag_crop_disease_symptom_master,id',
        ]);

        DB::table('ag_crop_disease_symptom_master')
            ->where('id', $validated['id'])
            ->delete();

        return redirect()->route('admin.appmaster.symptom')->with('success', 'Symptom deleted successfully.');
    }

}
