<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ContractAttechment;
use App\Models\ContractComment;
use App\Models\ContractNote;
use App\Models\ContractType;
use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class ContractController extends Controller
{

    public function __construct()
    {
        $this->middleware(
            [
                'auth',
            ]
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (Auth::user()->type == 'owner') {
            $contracts = Contract::where('created_by', '=', \Auth::user()->id)->get();
       
              
            // dd($contracts);

            $curr_month = Contract::where('created_by', '=', \Auth::user()->getCreatedBy())->whereMonth('start_date', '=', date('m'))->get();
            $curr_week = Contract::where('created_by', '=', \Auth::user()->getCreatedBy())->whereBetween(
                'start_date', [
                    \Carbon\Carbon::now()->startOfWeek(),
                    \Carbon\Carbon::now()->endOfWeek(),
                ]
            )->get();
            $last_30days = Contract::where('created_by', '=', \Auth::user()->id)->whereDate('start_date', '>', \Carbon\Carbon::now()->subDays(30))->get();

            // Contracts Summary
            $cnt_contract = [];
            $cnt_contract['total'] = \App\Models\Contract::getContractSummary($contracts);
            $cnt_contract['this_month'] = \App\Models\Contract::getContractSummary($curr_month);
            $cnt_contract['this_week'] = \App\Models\Contract::getContractSummary($curr_week);
            $cnt_contract['last_30days'] = \App\Models\Contract::getContractSummary($last_30days);

            return view('contracts.index', compact('contracts', 'cnt_contract'));

        } elseif (Auth::user()->user_contacts->role == 'client') {
            $contracts = Contract::where('client', '=', \Auth::user()->id)->get();

            $curr_month = Contract::where('client', '=', \Auth::user()->id)->whereMonth('start_date', '=', date('m'))->get();
            $curr_week = Contract::where('client', '=', \Auth::user()->id)->whereBetween(
                'start_date', [
                    \Carbon\Carbon::now()->startOfWeek(),
                    \Carbon\Carbon::now()->endOfWeek(),
                ]
            )->get();
            $last_30days = Contract::where('client', '=', \Auth::user()->id)->whereDate('start_date', '>', \Carbon\Carbon::now()->subDays(30))->get();

            // Contracts Summary
            $cnt_contract = [];
            $cnt_contract['total'] = \App\Models\Contract::getContractSummary($contracts);
            $cnt_contract['this_month'] = \App\Models\Contract::getContractSummary($curr_month);
            $cnt_contract['this_week'] = \App\Models\Contract::getContractSummary($curr_week);
            $cnt_contract['last_30days'] = \App\Models\Contract::getContractSummary($last_30days);

            return view('contracts.index', compact('contracts', 'cnt_contract'));
        } else {
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
        if (Auth::user()->type == 'owner') {

            // dd('hiii');
            $clients_data = User::where('type', '=', 'user')->where('created_by', \Auth::user()->id)->get();

            $client = [];
            foreach ($clients_data as $clients) {
                $role = $clients->usrRole()['role'];
                if ($role == 'Client') {
                    $client[$clients->id] = $clients->name;
                }
            }
            $contractType = ContractType::where('created_by', '=', \Auth::user()->id)->get()->pluck('name', 'id');
            $projects = Project::where('created_by', \Auth::user()->id)->pluck('name', 'id');
             
            // dd($projects);
             

            return view('contracts.create', compact('contractType', 'client', 'projects'));
        } else {
            //  dd('hello');
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
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
                'client_name' => 'required',
                'value' => 'required',
                'type' => 'required',
                'start_date' => 'required',
                'end_date' => 'required',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->route('contract.index')->with('error', $messages->first());
        }

        $contract = new Contract();
        $contract->id = $this->contractNumber();
        $contract->client = $request->client_name;
        $contract->project = $request->project;
        $contract->subject = $request->subject;
        $contract->value = $request->value;
        $contract->type = $request->type;
        $contract->start_date = $request->start_date;
        $contract->end_date = $request->end_date;
        $contract->notes = $request->descriptions;
        $contract->created_by = \Auth::user()->id;
        $contract->status = $request->status;

        $contract->save();

        return redirect()->back()->with('success', __('Contract successfully created!'));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $contract = Contract::find($id);

        $client = $contract->client;

        //  $resp = Utility::sendEmailTemplate('User Invite', [$user->id => $user->email], $uArr);

        return view('contracts.show', compact('contract', 'client'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        if (Auth::user()->type == 'owner') {
            $contract = Contract::find($id);
            $clients_data = User::where('type', '=', 'user')->where('created_by', \Auth::user()->id)->get();

            $client = [];
            foreach ($clients_data as $clients) {
                $role = $clients->usrRole()['role'];
                if ($role == 'Client') {
                    $client[$clients->id] = $clients->name;
                }
            }
            $contractType = ContractType::where('created_by', '=', \Auth::user()->id)->get()->pluck('name', 'id');
            $project = Project::pluck('name', 'id');

            return view('contracts.edit', compact('contract', 'contractType', 'client', 'project'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        // dd($request->all());
        $validator = \Validator::make(
            $request->all(), [
                'client_name' => 'required',
                'value' => 'required',
                'type' => 'required',
                'start_date' => 'required',
                'end_date' => 'required',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->route('contract.index')->with('error', $messages->first());
        }

        // $contract->name        = $request->name;

        $contract = Contract::find($id);
        $contract->client = $request->client_name;
        // dd($contract);
        $contract->project = $request->project;
         $contract->subject = $request->subject;
        $contract->value = $request->value;
        $contract->type = $request->type;
        $contract->start_date = $request->start_date;
        $contract->end_date = $request->end_date;
        $contract->notes = $request->notes;
        $contract->status = $request->status;
        $contract->created_by = \Auth::user()->id;
        // dd($contract);
        $contract->save();

        return redirect()->back()->with('success', __('Contract successfully updated!'));
        // return redirect()->route('contract.index')->with('success', __('Contract successfully updated!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contract $contract, $id)
    {
        if (Auth::user()->type == 'owner') {
            $contract = Contract::find($id);
            $contract->delete();

            return redirect()->back()->with('success', __('Contract successfully deleted!!'));
        }
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

    }

    public function descriptionStore($id, Request $request)
    {

        // dd($request->all());
        // if(\Auth::user()->type == 'Owner')
        // {
        $contract = Contract::find($id);
        $contract->contract_description = $request->contract_description;
        $contract->save();
        return redirect()->back()->with('success', __('Note successfully saved.'));
        // }
        // else
        // {
        //     return redirect()->back()->with('error', __('Permission denied'));
        // }
    }

    public function fileUpload($id, Request $request)
    {
        //   dd($id);
        $request->validate(
            ['file' => 'required|mimes:jpeg,jpg,png,gif,svg,pdf,txt,doc,docx,zip,rar|max:20480']
        );

        $fileName = $id . time() . "_" . $request->file->getClientOriginalName();
        $request->file->storeAs('contract_attechment', $fileName);
        $post['contract_id'] = $id;

        $post['files'] = $fileName;
        $post['name'] = time() . $request->file->getClientOriginalName();
        $post['extension'] = $request->file->getClientOriginalExtension();
        $post['file_size'] = round(($request->file->getSize() / 1024) / 1024, 2) . ' MB';
        $post['user_id'] = \Auth::user()->id;
        $post['created_by'] = \Auth::user()->id;

        $TaskFile = ContractAttechment::create($post);

        $TaskFile->deleteUrl = '';

        $TaskFile->deleteUrl = route(
            'contracts.file.delete', [$id]
        );
        $return['status'] = "success";
        $return['msg'] = __("Atttachment added successfully");
        return $return;
     
    }

    public function fileDelete($id)
    {

        $contract_file = ContractAttechment::find($id);
        // dd($contract_file);
        $path = storage_path('contract_attechment/' . $contract_file->files);

        if (file_exists($path)) {
            \File::delete($path);
        }
        $contract_file->delete();

        return redirect()->back()->with('success', __('Attachments successfully deleted!'));
    }

    public function fileDownload($id, $file_id)
    {
        // if(\Auth::user()->can('Edit Deal'))
        // {

        $contract = Contract::find($id);
        if ($contract->created_by == \Auth::user()->id) {
            $file = ContractAttechment::find($file_id);
            if ($file) {
                $file_path = storage_path('contract_attechment/' . $file->files);

                // $files = $file->files;

                return \Response::download(
                    $file_path, $file->files, [
                        'Content-Length: ' . filesize($file_path),
                    ]
                );
            } else {
                return redirect()->back()->with('error', __('File is not exist.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
        // }
        // else
        // {
        //     return redirect()->back()->with('error', __('Permission Denied.'));
        // }
    }

    public function commentStore(Request $request, $id)
    {
        // dd($id);
        // if(\Auth::user()->can('view project task'))
        // {

        $validator = \Validator::make(
            $request->all(), [
                'comment' => 'required',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', __('please add commments'));
        }

        $contract = new ContractComment();
        $contract->comment = $request->comment;
        $contract->contract_id = $id;
        $contract->user_id = \Auth::user()->id;
        $contract->created_by = \Auth::user()->id;
        $contract->save();

        // $return['status']="success";
        // $return['msg']= __('comments successfully created!');
        // return json_encode($return);

        return redirect()->back()->with('success', __('Comments successfully created!') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''))->with('status', 'comments');
        // }
        // else
        // {
        //     return redirect()->back()->with('error', __('Permission Denied.'));
        // }
    }

    public function commentDestroy($id)
    {
        if (Auth::user()->type == 'owner') {
            $contract = ContractComment::find($id);
            $contract->delete();
            return redirect()->back()->with('success', __('Comment successfully deleted!'));
        } else {
            $contract = ContractComment::where('created_by', \Auth::user()->id)->where('id', $id)->delete();
            return redirect()->back()->with('success', __('Comment successfully deleted!'));
        }
    }

    public function noteStore($id, Request $request)
    {

        $validator = \Validator::make(
            $request->all(), [
                'note' => 'required',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', __('Please add notes'));
        }

        $contract = Contract::find($id);
        $notes = new ContractNote();
        $notes->contract_id = $contract->id;
        $notes->note = $request->note;
        $notes->user_id = \Auth::user()->id;
        $notes->created_by = \Auth::user()->id;
        // dd($notes);
        $notes->save();

        return redirect()->back()->with('success', __('Note successfully saved.'));

    }

    public function noteDestroy($id)
    {
        if (Auth::user()->type == 'owner') {
            $contract = ContractNote::find($id);

            $contract->delete();
            return redirect()->back()->with('success', __('Note successfully deleted!'));
        } else {
            $contract = ContractNote::where('created_by', \Auth::user()->id)->where('id', $id)->delete();
            return redirect()->back()->with('success', __('Note successfully deleted!'));
        }
    }

    public function clientByProject(Request $request)
    {

        $project_data = [];
        $projects = ProjectUser::where('user_id', $request->client_id)->pluck('project_id', 'id');
        foreach ($projects as $key => $value) {
            $projectname = Project::where('id', $value)->first();
            $project_data[$projectname->id] = $projectname->name;

        }

        return \Response::json($project_data);

    }

    public function contractNumber()
    {
        $latest = Contract::where('created_by', '=', \Auth::user()->id)->latest()->first();
        if (!$latest) {
            return 1;
        }

        return $latest->id + 1;
    }

    public function copycontract($id)
    {

        $contract = Contract::find($id);
        $clients_data = User::where('type', '=', 'user')->where('created_by', \Auth::user()->id)->get();

        $client = [];
        foreach ($clients_data as $clients) {
            $role = $clients->usrRole()['role'];
            if ($role == 'Client') {
                $client[$clients->id] = $clients->name;
            }
        }
        $contractType = ContractType::where('created_by', '=', \Auth::user()->id)->get()->pluck('name', 'id');
        $project = Project::pluck('name', 'id');

        return view('contracts.copy', compact('contract', 'contractType', 'client', 'project'));

    }

    public function copycontractstore(Request $request, $id)
    {
        $validator = \Validator::make(
            $request->all(), [
                'client_name' => 'required',
                'value' => 'required',
                'type' => 'required',
                'start_date' => 'required',
                'end_date' => 'required',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->route('contract.index')->with('error', $messages->first());
        }

        $contract = new Contract();
        $contract->id = $this->contractNumber();
        $contract->client = $request->client_name;
        $contract->project = $request->project;
         $contract->subject = $request->subject;
        $contract->value = $request->value;
        $contract->type = $request->type;
        $contract->start_date = $request->start_date;
        $contract->end_date = $request->end_date;
        $contract->notes = $request->notes;
        $contract->created_by = \Auth::user()->id;
        $contract->status = $request->status;

        $contract->save();

        return redirect()->back()->with('success', __('Contract successfully created.'));

    }

    public function pdffromcontract($contract_id)
    {
        // if(\Auth::user()->can('Manage Invoices'))
        // {

        $id = Crypt::decrypt($contract_id);
        $contract = Contract::findOrFail($id);

        $logo = asset(\Storage::url('logo/'));
        $dark_logo = Utility::getValByName('dark_logo');
        $img = asset($logo . '/' . (isset($dark_logo) && !empty($dark_logo) ? $dark_logo : 'logo-dark.png'));

        return view('contracts.template', compact('contract','img'));

        // }
        // else
        // {
        //     return redirect()->back()->with('error', __('Permission denied.'));
        // }
    }

    public function printContract($id)
    {

        $contract = Contract::findOrFail($id);

        $client = $contract->client_name;

        //Set your logo
        $logo = asset(\Storage::url('logo/'));
        $dark_logo = Utility::getValByName('dark_logo');
        $img = asset($logo . '/' . (isset($dark_logo) && !empty($dark_logo) ? $dark_logo : 'logo-dark.png'));

        return view('contracts.contract_view', compact('contract', 'client', 'img'));

    }

    public function signature($id)
    {
        $contract = Contract::find($id);

        return view('contracts.signature', compact('contract'));

    }

    public function signatureStore(Request $request)
    {

        if (Auth::user()->type == 'owner') {
            $contract = Contract::find($request->contract_id);
            $contract->owner_signature = $request->owner_signature;

            $contract->save();

            $return['status'] = "success";
            $return['msg'] = __("Owner signature added successfully");
            return $return;
        } else {
            $contract = Contract::find($request->contract_id);
            $contract->client_signature = $request->client_signature;

            $contract->save();

            $return['status'] = "success";
            $return['msg'] = __("Client signature added successfully");
            return $return;
        }

    }

    public function sendmailContract($id, Request $request)
    {

        if (Auth::user()->type == 'owner') {

            $contract = Contract::find($id);

            $client = User::where('id', $contract->client)->first();

            $project = Project::where('id', $contract->project)->first();

            $project_type = ContractType::where('id', $contract->type)->first();

            try {
                $uArr = [
                    'client_name' => $client->name,
                    'contract_name' => $project->name,
                    'contract_type' => $project_type->name,
                    'contract_value' => $contract->value,
                    'start_date' => $contract->start_date,
                    'end_date' => $contract->end_date,
                ];

            //    dd($client->id,$client->email,$uArr);
                $resp = Utility::sendEmailTemplate('Contract Assign', [$client->id => $client->email], $uArr);

            } catch (\Exception $e) {
                $smtp_error = "<br><span class='text-danger'>" . __('E-Mail has been not sent due to SMTP configuration') . '</span>';
            }

            return redirect()->back()->with('success', __('E-Mail send successfully!'));
            // return redirect()->route('contract.show', $contract->id)->with('success', __('Send successfully!') . (($resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

}
