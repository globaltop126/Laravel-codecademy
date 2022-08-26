<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use App\Models\EmailTemplateLang;
use App\Models\ProjectEmailTemplate;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index($id, $lang = 'en')
    {
        $usr = \Auth::user();
        if(\Auth::user()->type == 'admin')
        {
            $EmailTemplates    = EmailTemplate::all();
            $emailTemplate     = EmailTemplate::first();
            $languages         = Utility::languages();

            $currEmailTempLang = EmailTemplateLang::where('parent_id', '=', $emailTemplate->id)->where('lang', $lang)->first();

            if(!isset($currEmailTempLang) || empty($currEmailTempLang))
            {
                $currEmailTempLang       = EmailTemplateLang::where('parent_id', '=', $id)->where('lang', 'en')->first();
                $currEmailTempLang->lang = $lang;
            }
       

            return view('email_templates.show', compact('EmailTemplates','languages','emailTemplate','currEmailTempLang'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        \App::setLocale(\Auth::user()->lang);

        return redirect()->back()->with('error', __('Permission Denied.'));

        if(\Auth::user()->type == 'admin')
        {
            return view('email_templates.create');
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
        \App::setLocale(\Auth::user()->lang);

        $usr = \Auth::user();

        if($usr->type == 'admin')
        {
            $validator = \Validator::make($request->all(), [
                'name' => 'required',
                'keyword' => 'required',
            ]);

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $EmailTemplate             = new EmailTemplate();
            $EmailTemplate->name       = $request->name;
            $EmailTemplate->from       = env('APP_NAME');
            $EmailTemplate->keyword    = $request->keyword;
            $EmailTemplate->created_by = $usr->id;
            $EmailTemplate->save();

            return redirect()->route('email_template.index')->with('success', __('Email Template successfully created.'));
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
     * @param \App\EmailTemplate $emailTemplate
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $template_id)
    {
        \App::setLocale(\Auth::user()->lang);

        $validator = \Validator::make($request->all(), [
            'from' => 'required',
        ]);

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $emailTemplate       = EmailTemplate::find($template_id);
        $emailTemplate->from = $request->from;
        $emailTemplate->save();

        return redirect()->route('manage.email.language', [
            $emailTemplate->id,
            $request->lang,
        ])->with('success', __('Email Template successfully updated.'));
    }

    // Used For View Email Template Language Wise
    public function manageEmailLang($id, $lang = 'en')
    {
        $EmailTemplates    = EmailTemplate::all();
        if(\Auth::user()->type == 'admin')
        {
            $languages         = Utility::languages();
            $emailTemplate     = EmailTemplate::where('id', '=', $id)->first();
            $currEmailTempLang = EmailTemplateLang::where('parent_id', '=', $id)->where('lang', $lang)->first();

            if(!isset($currEmailTempLang) || empty($currEmailTempLang))
            {
                $currEmailTempLang       = EmailTemplateLang::where('parent_id', '=', $id)->where('lang', 'en')->first();
                $currEmailTempLang->lang = $lang;
            }
         
            return view('email_templates.show', compact('EmailTemplates','emailTemplate', 'languages', 'currEmailTempLang'));
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    // Used For Store Email Template Language Wise
    public function storeEmailLang(Request $request, $id)
    {
        \App::setLocale(\Auth::user()->lang);

        if(Auth::user()->type == 'admin')
        {
            $validator = \Validator::make($request->all(), [
                'subject' => 'required',
                'content' => 'required',
            ]);

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $emailLangTemplate = EmailTemplateLang::where('parent_id', '=', $id)->where('lang', '=', $request->lang)->first();

            // if record not found then create new record else update it.
            if(empty($emailLangTemplate))
            {
                $emailLangTemplate            = new EmailTemplateLang();
                $emailLangTemplate->parent_id = $id;
                $emailLangTemplate->lang      = $request['lang'];
                $emailLangTemplate->subject   = $request['subject'];
                $emailLangTemplate->content   = $request['content'];
                $emailLangTemplate->from      = $request['from'];
                $emailLangTemplate->save();
            }
            else
            {
                $emailLangTemplate->subject = $request['subject'];
                $emailLangTemplate->content = $request['content'];
                $emailLangTemplate->from    = $request['from'];
                $emailLangTemplate->save();
            }

            return redirect()->route('manage.email.language', [
                $id,
                $request->lang,
            ])->with('success', __('Email Template Detail successfully updated.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied'));
        }
    }

    // Used For Update Status Project Wise.
    public function updateStatus(Request $request, $id, $project_id)
    {
        \App::setLocale(\Auth::user()->lang);

        if(\Auth::user()->type != 'admin')
        {
            $user_email = ProjectEmailTemplate::where('id', '=', $id)->where('project_id', '=', $project_id)->first();
            if(!empty($user_email))
            {
                if($request->status == 1)
                {
                    $user_email->is_active = 0;
                }
                else
                {
                    $user_email->is_active = 1;
                }

                $user_email->save();

                return response()->json(
                    [
                        'is_success' => true,
                        'success' => __('Status successfully updated!'),
                    ], 200
                );
            }
            else
            {
                return response()->json(
                    [
                        'is_success' => false,
                        'error' => __('Permission Denied.'),
                    ], 401
                );
            }
        }
        else
        {
            return response()->json(
                [
                    'is_success' => false,
                    'error' => __('Permission Denied.'),
                ], 401
            );
        }
    }
}
