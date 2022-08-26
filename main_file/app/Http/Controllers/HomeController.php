<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\Timesheet;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    use \RachidLaasri\LaravelInstaller\Helpers\MigrationsHelper;

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function landingPage()
    {
        if(!file_exists(storage_path() . "/installed"))
        {
            header('location:install');
            die;
        }

        if(Utility::getValByName('enable_landing') == 'on')
        {
            $plans          = Plan::orderBy('id', 'asc')->take(4)->get();
            $paymentSetting = Utility::getPaymentSetting();

            return view('layouts.landing', compact('plans', 'paymentSetting'));
        }
        else
        {
            return redirect()->route('home');
        }
    }

    public function index()
    {
        $user = Auth::user();

        if($user->type == 'admin')
        {
            return view('admin.dashboard');
        }
        else
        {
            $home_data = [];

            $user_projects   = $user->projects()->pluck('project_id')->toArray();
            $project_tasks   = ProjectTask::whereIn('project_id', $user_projects)->get();
            $project_expense = Expense::whereIn('project_id', $user_projects)->get();
            $seven_days      = Utility::getLastSevenDays();

            // Total Projects
            $complete_project           = $user->projects()->where('status', 'LIKE', 'complete')->count();
            $home_data['total_project'] = [
                'total' => count($user_projects),
                'percentage' => Utility::getPercentage($complete_project, count($user_projects)),
            ];

            // Total Tasks
            $complete_task           = ProjectTask::where('is_complete', '=', 1)->whereRaw("find_in_set('" . $user->id . "',assign_to)")->whereIn('project_id', $user_projects)->count();
            $home_data['total_task'] = [
                'total' => $project_tasks->count(),
                'percentage' => Utility::getPercentage($complete_task, $project_tasks->count()),
            ];

            // Total Expense
            $total_expense        = 0;
            $total_project_amount = 0;
            foreach($user->projects as $pr)
            {
                $total_project_amount += $pr->budget;
            }
            foreach($project_expense as $expense)
            {
                $total_expense += $expense->amount;
            }
            $home_data['total_expense'] = [
                'total' => $project_expense->count(),
                'percentage' => Utility::getPercentage($total_expense, $total_project_amount),
            ];

            // Total Users
            // $home_data['total_user'] = User::where('created_by', '=', Auth::user()->id)->count();
            $home_data['total_user'] = Auth::user()->contacts->count();

            // Tasks Overview Chart & Timesheet Log Chart
            $task_overview    = [];
            $timesheet_logged = [];
            foreach($seven_days as $date => $day)
            {
                // Task
                $task_overview[__($day)] = ProjectTask::where('is_complete', '=', 1)->where('marked_at', 'LIKE', $date)->whereIn('project_id', $user_projects)->count();

                // Timesheet
                $time                       = Timesheet::whereIn('project_id', $user_projects)->where('date', 'LIKE', $date)->pluck('time')->toArray();
                $timesheet_logged[__($day)] = str_replace(':', '.', Utility::calculateTimesheetHours($time));
            }

            $home_data['task_overview']    = $task_overview;
            $home_data['timesheet_logged'] = $timesheet_logged;

            // Project Status
            $total_project  = count($user_projects);
            $project_status = [];
            foreach(Project::$status as $k => $v)
            {
                $project_status[$k]['total']      = $user->projects->where('status', 'LIKE', $k)->count();
                $project_status[$k]['percentage'] = Utility::getPercentage($project_status[$k]['total'], $total_project);
            }
            $home_data['project_status'] = $project_status;

            // Top Due Project
            $home_data['due_project'] = $user->projects()->orderBy('end_date', 'DESC')->limit(5)->get();

            // Top Due Tasks
            $usr = \Auth::user();
            $home_data['due_tasks'] = ProjectTask::where('is_complete', '=', 0)->whereRaw("find_in_set('" . $usr->id . "',assign_to)")->whereIn('project_id', $user_projects)->orderBy('end_date', 'DESC')->get();

            $home_data['last_tasks'] = ProjectTask::whereIn('project_id', $user_projects)->orderBy('end_date', 'DESC')->get();

            return view('admin.dashboard', compact('home_data'));
        }
    }

    public function check()
    {
        $user = \Auth::user();

        if($user->type != 'admin')
        {
            if($user->plan != 1 && $user->plan_expire_date < date('Y-m-d'))
            {
                $plan = Plan::find(1);
                $user->assignPlan(1);
                if(!empty($plan))
                {
                    $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                    Order::create([
                                      'order_id' => $orderID,
                                      'name' => null,
                                      'email' => null,
                                      'card_number' => null,
                                      'card_exp_month' => null,
                                      'card_exp_year' => null,
                                      'plan_name' => $plan->name,
                                      'plan_id' => $plan->id,
                                      'price' => 0,
                                      'price_currency' => !empty(env('CURRENCY_CODE')) ? env('CURRENCY_CODE') : 'usd',
                                      'txn_id' => '',
                                      'payment_type' => __('Zero Price'),
                                      'payment_status' => 'succeeded',
                                      'receipt' => null,
                                      'user_id' => $user->id,
                                  ]);
                }

                return redirect()->route('home')->with('success', __('Free Plan Activated Successfully.!'));
            }
            elseif($user->plan != 1 && !empty($user->plan_expire_date))
            {
                if($user->is_trial_done || empty($user->plan_expire_date) && $user->plan_expire_date < date('Y-m-d'))
                {
                    $plans = Plan::all();
                    $error = $user->is_trial_done ? __('Your Plan is expired.') : ($user->plan_expire_date < date('Y-m-d') ? __('Please upgrade your plan') : '');

                    return view('plans.subscription', compact('plans'))->with('error', $error);
                }
                else
                {
                    return redirect()->route('home');
                }
            }
            else
            {
                return redirect()->route('home');
            }
        }
        else
        {
            return redirect()->route('home');
        }
    }

    public function homeCheck()
    {
        $user = \Auth::user();
        if($user->type != 'admin')
        {
            if($user->plan != 1 && !empty($user->plan_expire_date))
            {
                $plans = Plan::all();
                $error = $user->is_trial_done ? __('Your Plan is expired.') : ($user->plan_expire_date < date('Y-m-d') ? __('Please upgrade your plan') : '');

                return view('plans.subscription', compact('plans'))->with('error', $error);
            }
            elseif($user->plan == 1 && empty($user->plan_expire_date))
            {
                return redirect()->route('home')->with('success', __('Free Plan Activated Successfully.!'));
            }
        }
        else
        {
            return redirect()->route('home');
        }
    }

    // Load Dashboard user's using ajax
    public function filterView(Request $request)
    {
        $usr   = Auth::user();
        $users = User::where('id', '!=', $usr->id);

        if($request->ajax())
        {
            if(!empty($request->keyword))
            {

                $users->where('name', 'LIKE', $request->keyword . '%')->orWhereRaw('FIND_IN_SET("' . $request->keyword . '",skills)');
            }

            $users      = $users->get();
            $returnHTML = view('admin.view', compact('users'))->render();

            return response()->json([
                                        'success' => true,
                                        'html' => $returnHTML,
                                    ]);
        }
    }

    public function changeUserPlan(Request $request, $plan_id)
    {
        $plan = Plan::find($plan_id);
        $user = Auth::user();

        if($plan && $user->type == 'owner')
        {

            $user->is_register_trial  = 0;
            $user->interested_plan_id = 0;
            $user->save();

            return redirect('/check');

        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

    }
}
