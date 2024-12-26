<?php

namespace App\Http\Controllers\FreshleeMaster;

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
            return view('admin.freshleeMaster.items.index', [
                'marketItems' => $marketItems
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
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
}
