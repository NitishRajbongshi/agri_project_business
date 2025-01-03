<?php

namespace App\Http\Controllers\ApplicationMaster;

use App\Http\Controllers\Controller;
use App\Models\Appmaster\CropDisease;
use App\Models\Appmaster\CropType;
use App\Models\Appmaster\CropName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class RecommendationController extends Controller
{

    public function index()
    {
        $data = DB::table('ag_crop_master_disease_n_crop_name_n_control_measure_mapping as u')
            ->select('u.mapping_id', 'u.disease_cd', 'u.crop_name_cd', 'u.control_measure', 'u.control_measure_as')
            ->get();

        $disease = DB::table('ag_crop_disease_master')
            ->pluck('disease_name', 'disease_cd');


        $disease = $disease->map(function ($name) {
            return strtoupper($name); // Convert each disease name to uppercase
        });

        // Sort the diseases alphabetically
        $disease = $disease->sort();

        $crops = DB::table('ag_crop_name_master')->pluck('crop_name_desc', 'crop_name_cd');

        $crops = $crops->toArray();

        // Sort the crop types alphabetically
        asort($crops);

        // Convert all letters to uppercase
        $crops = array_map(function ($description) {
            return strtoupper($description); // Convert all letters to uppercase
        }, $crops);

        return view('admin.appmaster.recommendation', [
            'data' => $data,
            'disease' => $disease,
            'crops' => $crops
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'mapping_id' => 'required|integer|exists:ag_crop_master_disease_n_crop_name_n_control_measure_mapping,mapping_id',
            'control_measure' => 'required|string',
            'control_measure_as' => 'nullable|string',
            'crop_name_cd' => 'required|exists:ag_crop_name_master,crop_name_cd',
            'disease_cd' => 'required|integer|exists:ag_crop_disease_master,disease_cd',
        ], [
            'disease_cd.required' => 'The disease is required.',
            'crop_name_cd.required' => 'The crop name is required.',
        ]);


        DB::table('ag_crop_master_disease_n_crop_name_n_control_measure_mapping')
            ->where('mapping_id', $validated['mapping_id'])
            ->update([
                'control_measure' => $validated['control_measure'],
                'control_measure_as' => $validated['control_measure_as'],
                'crop_name_cd' => $validated['crop_name_cd'],
                'disease_cd' => $validated['disease_cd'],
                'updated_at' => now()->setTimezone('Asia/Kolkata'),
            ]);

        $cropRecom = DB::table('ag_crop_master_disease_n_crop_name_n_control_measure_mapping')
            ->where('mapping_id', $validated['mapping_id'])
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'Recommendation updated successfully.',
            'cropRecom' => $cropRecom
        ]);

        // return redirect()->route('admin.appmaster.recommendation')->with('success', 'Recommendation updated successfully.');
    }


    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'control_measure' => 'required|string|max:255',
                'control_measure_as' => 'nullable|string|max:255',
                'crop_name_cd' => 'required|integer|exists:ag_crop_name_master,crop_name_cd',
                'disease_cd' => 'required|integer|exists:ag_crop_disease_master,disease_cd',
            ], [
                'disease_cd.required' => 'Disease is required.',
                'crop_name_cd.required' => 'Crop name is required.',
            ]);

            $maxId = DB::table('ag_crop_master_disease_n_crop_name_n_control_measure_mapping')
                ->max('mapping_id');

            $nextId = $maxId ? $maxId + 1 : 1;

            DB::table('ag_crop_master_disease_n_crop_name_n_control_measure_mapping')->insert([
                'mapping_id' => $nextId,
                'control_measure' => $validated['control_measure'],
                'control_measure_as' => $validated['control_measure_as'],
                'disease_cd' => $validated['disease_cd'],
                'crop_name_cd' => $validated['crop_name_cd'],
                'updated_at' => now()->setTimezone('Asia/Kolkata'),
                'created_at' => now()->setTimezone('Asia/Kolkata'),
            ]);

            return redirect()->back()->with('success', 'Recommendation created successfully.');

            // return redirect()->route('admin.appmaster.recommendation')->with('success', 'Recommendation created successfully.');
        }


        $disease = DB::table('ag_crop_disease_master')->pluck('disease_name', 'disease_cd');
        $disease = $disease->map(function ($description) {
            return strtoupper($description); // Convert each crop type description to uppercase
        });

        // Sort the crop types alphabetically
        $disease = $disease->sort();


        $crops = DB::table('ag_crop_name_master')->pluck('crop_name_desc', 'crop_name_cd');

        $crops = $crops->map(function ($description) {
            return strtoupper($description); // Convert each crop type description to uppercase
        });

        // Sort the crop types alphabetically
        $crops = $crops->sort();

        return view('admin.appmaster.createrecommendation', ['disease' => $disease, 'crops' => $crops]);

    }

    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'mapping_id' => 'required|integer|exists:ag_crop_master_disease_n_crop_name_n_control_measure_mapping,mapping_id',
        ]);

        DB::table('ag_crop_master_disease_n_crop_name_n_control_measure_mapping')
            ->where('mapping_id', $validated['mapping_id'])
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Recommendation deleted successfully.',
            'mapping_id' => $validated['mapping_id']
        ]);

        // return redirect()->route('admin.appmaster.recommendation')->with('success', 'Recommendation deleted successfully.');
    }

}
