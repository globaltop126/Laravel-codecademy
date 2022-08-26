<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Plan;
use App\Models\UserCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::user()->type == 'admin')
        {
            $coupons = Coupon::get();

            return view('coupon.index', compact('coupons'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Auth::user()->type == 'admin')
        {
            return view('coupon.create');
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(Auth::user()->type == 'admin')
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required',
                                   'discount' => 'required|numeric|min:1|max:100',
                                   'limit' => 'required|numeric|min:1',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            if(empty($request->manualCode) && empty($request->autoCode))
            {
                return redirect()->back()->with('error', __('Coupon code is required'));
            }
            $coupon           = new Coupon();
            $coupon->name     = $request->name;
            $coupon->discount = $request->discount;
            $coupon->limit    = $request->limit;

            if(!empty($request->manualCode))
            {
                $coupon->code = strtoupper($request->manualCode);
            }

            if(!empty($request->autoCode))
            {
                $coupon->code = $request->autoCode;
            }

            $coupon->save();

            return redirect()->route('coupons.index')->with('success', __('Coupon successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Coupon $coupon
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Coupon $coupon)
    {
        if(Auth::user()->type == 'admin')
        {
            $userCoupons = UserCoupon::where('coupon', $coupon->id)->get();

            return view('coupon.view', compact('userCoupons'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Coupon $coupon
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Coupon $coupon)
    {
        if(Auth::user()->type == 'admin')
        {
            return view('coupon.edit', compact('coupon'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Coupon $coupon
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Coupon $coupon)
    {
        if(Auth::user()->type == 'admin')
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required',
                                   'discount' => 'required|numeric|min:1|max:100',
                                   'limit' => 'required|numeric|min:1',
                                   'code' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $coupon           = Coupon::find($coupon->id);
            $coupon->name     = $request->name;
            $coupon->discount = $request->discount;
            $coupon->limit    = $request->limit;
            $coupon->code     = $request->code;

            $coupon->save();

            return redirect()->route('coupons.index')->with('success', __('Coupon successfully updated.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Coupon $coupon
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Coupon $coupon)
    {
        if(Auth::user()->type == 'admin')
        {
            $coupon->delete();

            return redirect()->route('coupons.index')->with('success', __('Coupon successfully deleted.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function applyCoupon(Request $request)
    {
        $plan = Plan::find($request->plan_id);
        if($plan && $request->coupon != '')
        {
            $frequency_price = $plan->{$request->frequency . '_price'};
            $original_price  = self::formatPrice($frequency_price);
            $coupons         = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();

            if(!empty($coupons))
            {
                $usedCoupun = $coupons->used_coupon();

                if($coupons->limit == $usedCoupun)
                {
                    return response()->json(
                        [
                            'is_success' => false,
                            'final_price' => $original_price,
                            'price' => number_format($frequency_price, 2),
                            'message' => __('This coupon code has expired.'),
                        ]
                    );
                }
                else
                {
                    $discount_value = ($frequency_price / 100) * $coupons->discount;
                    $plan_price     = $frequency_price - $discount_value;
                    $price          = self::formatPrice($frequency_price - $discount_value);
                    $discount_value = '-' . self::formatPrice($discount_value);

                    return response()->json(
                        [
                            'is_success' => true,
                            'discount_price' => $discount_value,
                            'final_price' => $price,
                            'price' => number_format($plan_price, 2),
                            'message' => __('Coupon code has applied successfully.'),
                        ]
                    );
                }
            }
            else
            {
                return response()->json(
                    [
                        'is_success' => false,
                        'final_price' => $original_price,
                        'price' => number_format($frequency_price, 2),
                        'message' => __('This coupon code is invalid or has expired.'),
                    ]
                );
            }
        }
    }

    public function formatPrice($price)
    {
        return env('CURRENCY') . number_format($price);
    }
}
