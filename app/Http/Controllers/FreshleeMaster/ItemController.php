<?php

namespace App\Http\Controllers\FreshleeMaster;

use GuzzleHttp\Client;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function __construct()
    {
        Log::info('ItemController Initialized');
    }

    public function index()
    {
        try {
            $marketItems = DB::table('smartag_market.tbl_item_master')
                ->leftJoin('smartag_market.tbl_item_perishability_master', 'smartag_market.tbl_item_master.perishability_cd', '=', 'smartag_market.tbl_item_perishability_master.perishability_cd')
                ->leftJoin('smartag_market.tbl_item_category_master', 'smartag_market.tbl_item_master.item_category_cd', '=', 'smartag_market.tbl_item_category_master.item_category_cd')
                ->leftJoin('smartag_market.tbl_product_type_master', 'smartag_market.tbl_item_master.product_type_cd', '=', 'smartag_market.tbl_product_type_master.product_code')
                ->select('smartag_market.tbl_item_master.*', 'smartag_market.tbl_item_perishability_master.perishability_descr', 'smartag_market.tbl_item_category_master.item_category_desc', 'smartag_market.tbl_product_type_master.product_name')
                ->orderBy('smartag_market.tbl_item_master.item_cd', 'desc')
                ->get();
            $perishabilityTypes = DB::table('smartag_market.tbl_item_perishability_master')
                ->pluck('perishability_descr', 'perishability_cd');
            $itemCategories = DB::table('smartag_market.tbl_item_category_master')
                ->pluck('item_category_desc', 'item_category_cd');
            $productTypes = DB::table('smartag_market.tbl_product_type_master')
                ->pluck('product_name', 'product_code');
            return view('admin.freshleeMaster.items.index', [
                'marketItems' => $marketItems,
                'perishabilityTypes' => $perishabilityTypes,
                'itemCategories' => $itemCategories,
                'productTypes' => $productTypes
            ]);
        } catch (\Exception $e) {
            // Log::error($e->getMessage(), [
            //     'file' => $e->getFile(),
            //     'line' => $e->getLine(),
            // ]);
            Log::error($e->getMessage());
            return view('errors.generic');
        }
    }

    public function create()
    {
        try {
            $perishabilityType = DB::table('smartag_market.tbl_item_perishability_master')
                ->pluck('perishability_descr', 'perishability_cd');
            $itemCategories = DB::table('smartag_market.tbl_item_category_master')
                ->pluck('item_category_desc', 'item_category_cd');
            $productTypes = DB::table('smartag_market.tbl_product_type_master')
                ->pluck('product_name', 'product_code');

            return view('admin.freshleeMaster.items.create', [
                'perishabilityType' => $perishabilityType,
                'itemCategories' => $itemCategories,
                'productTypes' => $productTypes
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return view('errors.generic');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_name' => 'required',
            'perishability_cd' => 'required',
            'item_category_cd' => 'required',
            'product_type_cd' => 'required',
            'farm_life_in_days' => 'required',
            'min_qty_to_order' => 'required',
            'unit_min_order_qty' => 'required',
            'item_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        try {
            $item = DB::table('smartag_market.tbl_item_master')
                ->where('item_name', $request->item_name)
                ->first();
            if ($item) {
                return redirect()->route('admin.freshlee.master.item.create')
                    ->with('error', 'Item name already exist.');
            }

            // Generate the next crop_name_cd
            $maxId = DB::table('smartag_market.tbl_item_master')
                ->max('item_cd');
            $nextId = $maxId ? $maxId + 1 : 1;

            // code to store item image
            $folderName = strtoupper($request->item_name) . '_' . $nextId;
            $folderPath = $folderName;
            // Create folder if it doesn't exist
            if (!Storage::disk('freshleeItems')->exists($folderPath)) {
                Storage::disk('freshleeItems')->makeDirectory($folderPath);
            }
            // Get the uploaded files
            $image1 = $request->file('item_image');
            $imageName1 = $folderName . '_' . substr(uniqid(), -7) . '.' . $image1->getClientOriginalExtension();
            $image1->storeAs($folderPath, $imageName1, 'freshleeItems');
            $rootPath = config('filesystems.disks.freshleeItems.root');

            DB::table('smartag_market.tbl_item_master')->insert([
                'item_cd' => $nextId,
                'item_name' => $request->item_name,
                'item_image_file_path' => $rootPath . '/' . $folderPath . '/' . $imageName1,
                'file_type' => $request->item_image->extension(),
                'perishability_cd' => $request->perishability_cd,
                'item_category_cd' => $request->item_category_cd,
                'product_type_cd' => $request->product_type_cd,
                'farm_life_in_days' => $request->farm_life_in_days,
                'min_qty_to_order' => $request->min_qty_to_order,
                'unit_min_order_qty' => $request->unit_min_order_qty,
            ]);

            return redirect()->route('admin.freshlee.master.item')
                ->with('success', 'Item created successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return view('errors.generic');
        }
    }

    public function update(Request $request)
    {
        try {
            $item = DB::table('smartag_market.tbl_item_master')
                ->where('item_cd', $request->item_cd)
                ->first();
            if (!$item) {
                return redirect()->back()
                    ->with('error', 'Item not found.');
            }
            $itemName = DB::table('smartag_market.tbl_item_master')
                ->where('item_name', $request->item_name)
                ->where('item_cd', '!=', $request->item_cd)
                ->first();
            if ($itemName) {
                return redirect()->back()
                    ->with('error', 'Item name already exist.');
            }
            if ($request->item_image == null) {
                Log::info('No image');
                $status = DB::table('smartag_market.tbl_item_master')
                    ->where('item_cd', $request->item_cd)
                    ->update([
                        'item_name' => $request->item_name,
                        'perishability_cd' => $request->perishability_cd,
                        'item_category_cd' => $request->item_category_cd,
                        'product_type_cd' => $request->product_type_cd,
                        'farm_life_in_days' => $request->farm_life,
                        'min_qty_to_order' => $request->min_order,
                        'unit_min_order_qty' => $request->item_unit,
                    ]);
                return redirect()->back()
                    ->with('success', 'Item updated successfully.');
            }
            // code to store item image
            $folderName = strtoupper($request->item_name) . '_' . $request->item_cd;
            $folderPath = $folderName;
            // Create folder if it doesn't exist
            if (!Storage::disk('freshleeItems')->exists($folderPath)) {
                Storage::disk('freshleeItems')->makeDirectory($folderPath);
            }
            // Get the uploaded files
            $image1 = $request->file('item_image');
            $imageName1 = $folderName . '_' . substr(uniqid(), -7) . '.' . $image1->getClientOriginalExtension();
            $image1->storeAs($folderPath, $imageName1, 'freshleeItems');
            $rootPath = config('filesystems.disks.freshleeItems.root');
            $status = DB::table('smartag_market.tbl_item_master')
                ->where('item_cd', $request->item_cd)
                ->update([
                    'item_name' => $request->item_name,
                    'item_image_file_path' => $rootPath . '/' . $folderPath . '/' . $imageName1,
                    'file_type' => $request->item_image->extension(),
                    'perishability_cd' => $request->perishability_cd,
                    'item_category_cd' => $request->item_category_cd,
                    'product_type_cd' => $request->product_type_cd,
                    'farm_life_in_days' => $request->farm_life,
                    'min_qty_to_order' => $request->min_order,
                    'unit_min_order_qty' => $request->item_unit,
                ]);
            return redirect()->back()
                ->with('success', 'Item updated successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return view('errors.generic');
        }
    }

    public function getItemImage(Request $request)
    {
        try {
            Log::info('Request: ' . $request->item_cd);
            $client = new Client();
            $itemImageEndpoint = config('customconfig.MASTER_ITEM_IMAGE');
            $response = $client->request('GET', $itemImageEndpoint, [
                'query' => [
                    'item_cd' => $request->item_cd
                ]
            ]);
            $responseBody = json_decode($response->getBody()->getContents());
            if ($responseBody->item_cd) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Image found.',
                    'item_name' => $responseBody->item_name,
                    'image_src' => $responseBody->item_image_url
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Item not found.',
                    'item_name' => null,
                    'image_src' => null
                ]);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred.'
            ]);
        }
    }
}
