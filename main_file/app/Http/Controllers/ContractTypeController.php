<?php

namespace App\Http\Controllers;

use App\Models\ContractType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractTypeController extends Controller
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
        return view('contracttype.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = \Validator::make(
            $request->all(), [
                               'name' => 'required',
                           ]
        );

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->route('settings')->with('error', $messages->first());
        }

        $ContractType             = new ContractType();
        $ContractType->name       = $request->name;
       
        $ContractType->created_by = Auth::user()->id; 

        $ContractType->save();

        return redirect()->route('settings')->with('success', __('ContractType successfully created!'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ContractType $contracttype)
    {
        return redirect()->route('settings');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(ContractType $contracttype,$id)
    {
        $contracttype=ContractType::find($id);
        // dd($contracttype);
        if(Auth::user()->id == $contracttype->created_by)
        {   
            return view('contracttype.edit', compact('contracttype'));
        }
        else
        {
            return redirect()->route('settings')->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id , ContractType $contracttype)
    {     
        $contracttype=ContractType::find($id);
        if(Auth::user()->id == $contracttype->created_by)
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('settings')->with('error', $messages->first());
            }

            $contracttype->name       = $request->name;
            $contracttype->created_by = Auth::user()->id; 

            
            $contracttype->save();  

            

            return redirect()->route('settings')->with('success', __('ContractType successfully updated!'));
        }
        else
        {
            return redirect()->route('settings')->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ContractType $contracttype, $id)
    {   
        $contracttype=ContractType::find($id);
      
        if(Auth::user()->id == $contracttype->created_by)
        {
            $contracttype->delete();

            return redirect()->route('settings')->with('success', __('ContractType successfully deleted.'));
        }
        else
        {
            return redirect()->route('settings')->with('error', __('Permission Denied.'));
        }
    }
}
