<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect()->route('settings');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('taxes.create');
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
        $validator = \Validator::make(
            $request->all(), [
                               'name' => 'required',
                               'rate' => 'required|numeric|min:1|max:100',
                           ]
        );

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->route('settings')->with('error', $messages->first());
        }

        $tax             = new Tax();
        $tax->name       = $request->name;
        $tax->rate       = number_format($request->rate, 2);
        $tax->created_by = Auth::user()->id;
        $tax->save();

        return redirect()->route('settings')->with('success', __('Tax successfully created!'));
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Tax $tax
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Tax $tax)
    {
        return redirect()->route('settings');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Tax $tax
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Tax $tax)
    {      
        // dd($tax->created_by);
        if(Auth::user()->id == $tax->created_by)
        {
            return view('taxes.edit', compact('tax'));
        }
        else
        {
            return redirect()->route('settings')->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Tax $tax
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tax $tax)
    {
        if(Auth::user()->id == $tax->created_by)
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required',
                                   'rate' => 'required|numeric|min:1|max:100',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('settings')->with('error', $messages->first());
            }

            $tax->name       = $request->name;
            $tax->rate       = number_format($request->rate, 2);
            $tax->created_by = Auth::user()->id;
            $tax->save();

            return redirect()->route('settings')->with('success', __('Tax successfully updated!'));
        }
        else
        {
            return redirect()->route('settings')->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Tax $tax
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tax $tax)
    {
        if(Auth::user()->id == $tax->created_by)
        {
            $tax->delete();

            return redirect()->route('settings')->with('success', __('Tax successfully deleted.'));
        }
        else
        {
            return redirect()->route('settings')->with('error', __('Permission Denied.'));
        }
    }
}
