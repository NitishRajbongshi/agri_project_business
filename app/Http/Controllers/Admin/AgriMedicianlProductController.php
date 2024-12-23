<?php

namespace App\Http\Controllers\Admin;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class AgriMedicianlProductController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
        DB::enableQueryLog();
        Log::info('AgriMedicianlProductController');
        $query = DB::getQueryLog();
        Log::info($query);
    }

    public function index()
    {
        try {
            $medicialProdDetails = DB::table('ag_crop_master_medicinal_products as M')
                ->select('M.*', 'T.product_type_descr', 'S.status_descr')
                ->leftJoin('ag_crop_master_medicinal_product_type as T', 'M.product_type_cd', '=', 'T.product_type_cd')
                ->leftJoin('ag_crop_master_medicinal_product_status as S', 'M.status', '=', 'S.status_code')
                ->orderBy('M.product_type_cd', 'desc')
                ->orderBy('M.technical_code', 'desc')
                ->orderBy('M.trade_code', 'desc')->get();
            $prod_type_dtls = DB::table('ag_crop_master_medicinal_product_type')
                ->select('product_type_cd', DB::raw('UPPER(product_type_descr) as product_type_descr'))
                ->orderBy('product_type_descr')->get();


            return view(
                'admin.agriMedicinalProducts.medicinalProducts',
                compact(
                    'medicialProdDetails',
                    'prod_type_dtls'
                )
            );

        } catch (Exception $e) {
            Log::error("Error: ", [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            Log::error($e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return view('errors.generic');
        }
    }
}
