<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Expense;
use App\Models\Project;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($project_id)
    {
        $permissions = Auth::user()->getPermission($project_id);

        if(isset($permissions) && (in_array('create expense', $permissions) || in_array('show expense', $permissions)))
        {
            if(Auth::user()->type != 'admin')
            {
                $project     = Project::find($project_id);
                $amount      = $project->expense->sum('amount');
                $expense_cnt = Utility::projectCurrencyFormat($project_id, $amount) . '/' . Utility::projectCurrencyFormat($project_id, $project->budget);

                return view('expenses.index', compact('project', 'expense_cnt'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
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
    public function create($project_id)
    {
        $permissions = Auth::user()->getPermission($project_id);

        if(isset($permissions) && in_array('create expense', $permissions))
        {
            $project = Project::find($project_id);

            return view('expenses.create', compact('project'));
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
    public function store(Request $request, $project_id)
    {
        $usr       = \Auth::user();
        $validator = Validator::make(
            $request->all(), [
                               'name' => 'required|max:120',
                               'amount' => 'required|numeric|min:0',
                           ]
        );

        if($validator->fails())
        {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $post               = $request->all();
        $post['project_id'] = $project_id;
        $post['date']       = (!empty($request->date)) ? $request->date : null;
        $post['created_by'] = $usr->id;

        if($request->hasFile('attachment'))
        {
            $fileNameToStore    = time() . '.' . $request->attachment->getClientOriginalExtension();
            $path               = $request->file('attachment')->storeAs('expense', $fileNameToStore);
            $post['attachment'] = $path;
        }

        $expense = Expense::create($post);

        // Make entry in activity log
        ActivityLog::create(
            [
                'user_id' => $usr->id,
                'project_id' => $project_id,
                'log_type' => 'Create Expense',
                'remark' => json_encode(['title' => $expense->name]),
            ]
        );

        return redirect()->back()->with('success', __('Expense added successfully.'));
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Expense $expense
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Expense $expense)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Expense $expense
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($project_id, $expense_id)
    {
        $project = Project::find($project_id);
        $expense = Expense::find($expense_id);

        return view('expenses.edit', compact('project', 'expense'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Expense $expense
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $project_id, $expense_id)
    {
        $validator = Validator::make(
            $request->all(), [
                               'name' => 'required|max:120',
                               'amount' => 'required|numeric|min:0',
                           ]
        );

        if($validator->fails())
        {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $post    = $request->all();
        $expense = Expense::find($expense_id);

        if($request->hasFile('attachment'))
        {
            Utility::checkFileExistsnDelete([$expense->attachment]);

            $fileNameToStore    = time() . '.' . $request->attachment->getClientOriginalExtension();
            $path               = $request->file('attachment')->storeAs('expense', $fileNameToStore);
            $post['attachment'] = $path;
        }

        $expense->update($post);

        return redirect()->back()->with('success', __('Expense Updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Expense $expense
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($expense_id)
    {
        $expense = Expense::find($expense_id);
        Utility::checkFileExistsnDelete([$expense->attachment]);
        $expense->delete();

        return redirect()->back()->with('success', __('Expense Deleted successfully.'));
    }

    // Expense List
    public function expenseList()
    {
        $usr = Auth::user();
        if($usr->type != 'admin')
        {
            $user_projects = $usr->projects()->pluck('project_id')->toArray();

            // remove project id they don't assign permission to show expense to this user
            foreach($user_projects as $key => $val)
            {
                $permissions = $usr->getPermission($val);
                if(isset($permissions) && !in_array('show expense', $permissions))
                {
                    unset($user_projects[$key]);
                }
            }
            // end

            $expenses = Expense::whereIn('project_id', $user_projects);
            $total    = number_format($expenses->sum('amount')) . '/' . number_format($usr->projects->sum('budget'));
            $expenses = $expenses->get();

            return view('expenses.list', compact('expenses', 'total'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
}
