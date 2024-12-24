<?php

namespace App\Http\Controllers\FreshleeMarket;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemReportController extends Controller
{
    public function index()
    {
        // Row query for futher use

        // SELECT
        // 	    smartag_market.tbl_customer_booking_details.cust_id,
        // 	    smartag_market.tbl_customer_booking_details.order_date,
        //      smartag_market.tbl_customer_booking_details.booking_ref_no, 
        // 	    smartag_market.tbl_customer_booking_details.order_date,
        // 	    smartag_market.tbl_customer_booking_details.is_delivered,
        // 	    smartag_market.tbl_user_login.full_name,
        //      smartag_market.tbl_user_login.phone_no,
        // 	    smartag_market.tbl_user_address.address_line1,
        //      JSON_AGG(
        //         JSON_BUILD_OBJECT(
        //             'item_cd', smartag_market.tbl_customer_booking_details.item_cd,
        //             'item_name', smartag_market.tbl_item_master.item_name,
        //             'item_unit', smartag_market.tbl_item_master.unit_min_order_qty,
        //             'item_quantity', smartag_market.tbl_customer_booking_details.item_quantity
        //         )
        //      ) AS order_items
        // FROM 
        //     smartag_market.tbl_customer_booking_details
        // LEFT JOIN
        //     smartag_market.tbl_user_login ON
        //     smartag_market.tbl_customer_booking_details.cust_id = smartag_market.tbl_user_login.user_id
        // LEFT JOIN
        //     smartag_market.tbl_user_address ON
        //     smartag_market.tbl_customer_booking_details.delivery_address_cd = smartag_market.tbl_user_address.address_cd
        // LEFT JOIN
        //     smartag_market.tbl_item_master ON
        //     smartag_market.tbl_customer_booking_details.item_cd = smartag_market.tbl_item_master.item_cd
        // WHERE smartag_market.tbl_customer_booking_details.booking_ref_no = '600239075420240229100856195266'
        // GROUP BY 
        // 	    smartag_market.tbl_customer_booking_details.cust_id,
        // 	    smartag_market.tbl_customer_booking_details.order_date,
        //      smartag_market.tbl_customer_booking_details.booking_ref_no,
        // 	    smartag_market.tbl_customer_booking_details.order_date,
        // 	    smartag_market.tbl_customer_booking_details.is_delivered,
        // 	    smartag_market.tbl_user_login.full_name,
        //      smartag_market.tbl_user_login.phone_no,
        // 	    smartag_market.tbl_user_address.address_line1;

        // SELECT 
        //     smartag_market.tbl_customer_booking_details.item_cd, 
        // 	   smartag_market.tbl_item_master.item_name,
        //     smartag_market.tbl_item_master.unit_min_order_qty,
        //     SUM(smartag_market.tbl_customer_booking_details.item_quantity) AS total_quantity
        // FROM 
        //     smartag_market.tbl_customer_booking_details
        // LEFT JOIN
        // 	   smartag_market.tbl_item_master
        // ON
        // 	   smartag_market.tbl_customer_booking_details.item_cd = smartag_market.tbl_item_master.item_cd
        // GROUP BY 
        //     smartag_market.tbl_customer_booking_details.item_cd,
        // 	   smartag_market.tbl_item_master.item_name,
        //     smartag_market.tbl_item_master.unit_min_order_qty;


        // get the neccessary dates
        $today = Carbon::today()->toDateString();;
        $start = Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString();
        $first = Carbon::now()->startOfMonth()->toDateString();
        $data = DB::table('smartag_market.tbl_customer_booking_details')
            ->leftJoin(
                'smartag_market.tbl_user_login',
                'smartag_market.tbl_customer_booking_details.cust_id',
                '=',
                'smartag_market.tbl_user_login.user_id'
            )
            ->leftJoin(
                'smartag_market.tbl_user_address',
                'smartag_market.tbl_customer_booking_details.delivery_address_cd',
                '=',
                'smartag_market.tbl_user_address.address_cd'
            )
            ->leftJoin(
                'smartag_market.tbl_item_master',
                'smartag_market.tbl_customer_booking_details.item_cd',
                '=',
                'smartag_market.tbl_item_master.item_cd'
            )
            ->select(
                'smartag_market.tbl_customer_booking_details.cust_id',
                DB::raw('DATE(smartag_market.tbl_customer_booking_details.order_date) as order_date'),
                'smartag_market.tbl_customer_booking_details.booking_ref_no',
                'smartag_market.tbl_customer_booking_details.is_delivered',
                'smartag_market.tbl_user_login.full_name',
                'smartag_market.tbl_user_login.phone_no',
                'smartag_market.tbl_user_address.address_line1',
                DB::raw("JSON_AGG(
                JSON_BUILD_OBJECT(
                    'item_cd', smartag_market.tbl_customer_booking_details.item_cd,
                    'item_name', smartag_market.tbl_item_master.item_name,
                    'item_unit', smartag_market.tbl_item_master.unit_min_order_qty,
                    'item_quantity', smartag_market.tbl_customer_booking_details.item_quantity
                )
            ) AS order_items")
            )
            ->whereBetween(DB::raw('DATE(smartag_market.tbl_customer_booking_details.order_date)'), [$start, $today])
            ->groupBy(
                'smartag_market.tbl_customer_booking_details.cust_id',
                'smartag_market.tbl_customer_booking_details.order_date',
                'smartag_market.tbl_customer_booking_details.booking_ref_no',
                'smartag_market.tbl_customer_booking_details.is_delivered',
                'smartag_market.tbl_user_login.full_name',
                'smartag_market.tbl_user_login.phone_no',
                'smartag_market.tbl_user_address.address_line1'
            )
            ->orderBy('smartag_market.tbl_customer_booking_details.order_date', 'desc')
            ->get();

        $itemCounts = DB::table('smartag_market.tbl_customer_booking_details')
            ->select(
                'smartag_market.tbl_customer_booking_details.item_cd',
                'smartag_market.tbl_item_master.item_name',
                'smartag_market.tbl_item_master.unit_min_order_qty',
                DB::raw('SUM(smartag_market.tbl_customer_booking_details.item_quantity) AS total_quantity')
            )
            ->leftJoin(
                'smartag_market.tbl_item_master',
                'smartag_market.tbl_customer_booking_details.item_cd',
                '=',
                'smartag_market.tbl_item_master.item_cd'
            )
            ->groupBy(
                'smartag_market.tbl_customer_booking_details.item_cd',
                'smartag_market.tbl_item_master.item_name',
                'smartag_market.tbl_item_master.unit_min_order_qty'
            )
            ->whereBetween(DB::raw('DATE(smartag_market.tbl_customer_booking_details.order_date)'), [$start, $today])
            ->get();

        $picupAddress = DB::table("smartag_market.tbl_default_delivery_address")
            ->select('address_line1', 'address_line2', 'state_cd', 'district_cd', 'pin_code', 'latitude', 'longitude')
            ->get()
            ->first();

        return view('admin.freshleeMarket.userOrder', [
            'data' => $data,
            'start' => $start,
            'first' => $first,
            'today' => $today,
            'picupAddress' => $picupAddress,
            'itemCounts' => $itemCounts
        ]);
    }

    public function history(Request $request)
    {
        // dd($request->all());
        $start = $request->start_date;
        $today = $request->end_date;
        $data = DB::table('smartag_market.tbl_customer_booking_details')
            ->leftJoin(
                'smartag_market.tbl_user_login',
                'smartag_market.tbl_customer_booking_details.cust_id',
                '=',
                'smartag_market.tbl_user_login.user_id'
            )
            ->leftJoin(
                'smartag_market.tbl_user_address',
                'smartag_market.tbl_customer_booking_details.delivery_address_cd',
                '=',
                'smartag_market.tbl_user_address.address_cd'
            )
            ->leftJoin(
                'smartag_market.tbl_item_master',
                'smartag_market.tbl_customer_booking_details.item_cd',
                '=',
                'smartag_market.tbl_item_master.item_cd'
            )
            ->select(
                'smartag_market.tbl_customer_booking_details.cust_id',
                DB::raw('DATE(smartag_market.tbl_customer_booking_details.order_date) as order_date'),
                'smartag_market.tbl_customer_booking_details.booking_ref_no',
                'smartag_market.tbl_customer_booking_details.is_delivered',
                'smartag_market.tbl_user_login.full_name',
                'smartag_market.tbl_user_login.phone_no',
                'smartag_market.tbl_user_address.address_line1',
                DB::raw("JSON_AGG(
                JSON_BUILD_OBJECT(
                    'item_cd', smartag_market.tbl_customer_booking_details.item_cd,
                    'item_name', smartag_market.tbl_item_master.item_name,
                    'item_unit', smartag_market.tbl_item_master.unit_min_order_qty,
                    'item_quantity', smartag_market.tbl_customer_booking_details.item_quantity
                )
            ) AS order_items")
            )
            ->whereBetween(DB::raw('DATE(smartag_market.tbl_customer_booking_details.order_date)'), [$start, $today])
            ->groupBy(
                'smartag_market.tbl_customer_booking_details.cust_id',
                'smartag_market.tbl_customer_booking_details.order_date',
                'smartag_market.tbl_customer_booking_details.booking_ref_no',
                'smartag_market.tbl_customer_booking_details.is_delivered',
                'smartag_market.tbl_user_login.full_name',
                'smartag_market.tbl_user_login.phone_no',
                'smartag_market.tbl_user_address.address_line1'
            )
            ->orderBy('smartag_market.tbl_customer_booking_details.order_date', 'desc')
            ->get();
        // dd($data);
        return view('admin.freshleeMarket.orderHistory', [
            'data' => $data,
            'first' => $start,
            'today' => $today
        ]);
    }
}