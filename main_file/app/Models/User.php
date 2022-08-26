<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class User extends Authenticatable
{
    use Notifiable,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'avatar',
        'created_by',
        'phone',
        'dob',
        'gender',
        'skills',
        'is_active',
        'lang',
        'facebook',
        'whatsapp',
        'instagram',
        'likedin',
        'mode',
        'is_trial_done',
        'interested_plan_id',
        'is_register_trial',
        'plan',
        'plan_expire_date',
        'details',
        'requested_plan',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Change image while fetching
    protected $appends = [''];

    public function getImgAvatarAttribute()
    {
        if(\Storage::exists($this->avatar) && !empty($this->avatar))
        {
            return $this->attributes['img_avatar'] = 'src=' . asset(\Storage::url($this->avatar));
        }
        else
        {
            return $this->attributes['img_avatar'] = 'avatar=' . $this->name;
        }
    }

    public function getCreatedBy()
    {
        if($this->type == 'owner')
        {
            return $this->id;
        }
        else
        {
            return $this->created_by;
        }
    }

    public function creatorId()
    {
        if($this->type == 'owner' || $this->type == 'super admin')
        {
            return $this->id;
        }
        else
        {
            return $this->created_by;
        }
    }

    public function projects()
    {
        return $this->belongsToMany('App\Models\Project', 'project_users', 'user_id', 'project_id')->withPivot('id', 'permission')->withTimestamps();
    }

    public function todo()
    {
        return $this->hasMany('App\Models\UserToDo', 'user_id', 'id');
    }

    // Get Open Task and Last Seven Days Timesheet Logged Hours
    public function usrCommonData()
    {
        $user_projects = $this->projects()->pluck('project_id')->toArray();

        // get Open Task
        $open_task = ProjectTask::whereIn('project_id', $user_projects)->where('is_complete', '=', 0)->whereRaw("find_in_set('" . $this->id . "',assign_to)")->count();

        // Get Last Seven Days Timesheet with date
        $seven_days = Utility::getLastSevenDays();
        $chart_data = [];
        foreach($seven_days as $date => $day)
        {
            $time             = Timesheet::where('created_by', '=', $this->id)->where('date', 'LIKE', $date)->pluck('time')->toArray();
            $chart_data[$day] = str_replace(':', '.', Utility::calculateTimesheetHours($time));
        }

        return [
            'open_task' => $open_task,
            'timesheet' => $chart_data,
        ];
    }

    // Get task users
    public function tasks()
    {
        return ProjectTask::whereRaw("find_in_set('" . $this->id . "',assign_to)")->get();
    }

    // Get User's Contact
    public function contacts()
    {
        return $this->hasMany('App\Models\UserContact', 'parent_id', 'id');
    }

    // For Email template Module
    public function defaultEmail()
    {
        // Email Template
        $emailTemplate = [
            'User Invite' => 'Email : {email},Password : {password}',
            'Invite Project' => 'Project Name : {project_name},Project Status : {project_status},Project Budget : {project_budget},Project Hours : {project_hours}',
            'Task Assign' => 'Task Name : {task_name},Task Priority : {task_priority},Task Project : {task_project},Task Stage : {task_stage}',
            'Create Timesheet' => 'Timesheet Project : {timesheet_project},Timesheet Task : {timesheet_task},Timesheet Time : {timesheet_time},Timesheet Date : {timesheet_date}',
            'Contract Assign' =>'Client Name : {client_name},Contract Name : {contract_name},Contract Type : {contract_type}, Contract Value : {contract_value}, Start Date : {start_date}, End Date : {end_date}',
        ];

        foreach($emailTemplate as $eTemp => $keyword)
        {
            EmailTemplate::create([
                                      'name' => $eTemp,
                                      'from' => (env('APP_NAME'))?env('APP_NAME'):"Taskgo SaaS",
                                      'keyword' => $keyword,
                                      'created_by' => $this->id,
                                  ]);
        }

        // Make content for email template language
        $defaultTemplate = [
            'User Invite' => [
                'subject' => 'Login Detail',
                'lang' => [
                    'ar' => '<p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;">مرحبا،&nbsp;<br>مرحبا بك في {app_name}.</p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;"><span style="font-weight: bolder;">البريد الإلكتروني&nbsp;</span>: {email}<br><span style="font-weight: bolder;">كلمه السر</span>&nbsp;: {password}</p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;">{app_url}</p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;">شكر،<br>{app_name}</p>',
                    'da' => '<p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;">Hej,&nbsp;<br>Velkommen til {app_name}.</p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;"><span style="font-weight: bolder;">E-mail&nbsp;</span>: {email}<br><span style="font-weight: bolder;">Adgangskode</span>&nbsp;: {password}</p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;">{app_url}</p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;">Tak,<br>{app_name}</p>',
                    'de' => '<p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;">Hallo,&nbsp;<br>Willkommen zu {app_name}.</p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;"><span style="font-weight: bolder;">Email&nbsp;</span>: {email}<br><span style="font-weight: bolder;">Passwort</span>&nbsp;: {password}</p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;">{app_url}</p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;">Vielen Dank,<br>{app_name}</p>',
                    'en' => '<p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;">Hello,&nbsp;<br>Welcome to {app_name}.</p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;"><span style="font-weight: bolder;">Email&nbsp;</span>: {email}<br><span style="font-weight: bolder;">Password</span>&nbsp;: {password}</p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;">{app_url}</p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;">Thanks,<br>{app_name}</p>',
                    'es' => '<p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;">Hola,&nbsp;<br>Bienvenido a {app_name}.</p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;"><span style="font-weight: bolder;">Email&nbsp;</span>: {email}<br><span style="font-weight: bolder;">Contraseña</span>&nbsp;: {password}</p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;">{app_url}</p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;">Gracias,<br>{app_name}</p>',
                    'fr' => '<p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;">Bonjour,&nbsp;<br>Bienvenue à {app_name}.</p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;"><span style="font-weight: bolder;">Email&nbsp;</span>: {email}<br><span style="font-weight: bolder;">Mot de passe</span>&nbsp;: {password}</p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;">{app_url}</p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;">Merci,<br>{app_name}</p>',
                    'it' => '<p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;">Ciao,&nbsp;<br>Benvenuto a {app_name}.</p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;"><span style="font-weight: bolder;">E-mail&nbsp;</span>: {email}<br><span style="font-weight: bolder;">Parola d\'ordine</span>&nbsp;: {password}</p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;">{app_url}</p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;">Grazie,<br>{app_name}</p>',
                    'ja' => '<p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;">こんにちは、&nbsp;<br>へようこそ {app_name}.</p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;"><span style="font-weight: bolder;">Eメール&nbsp;</span>: {email}<br><span style="font-weight: bolder;">パスワード</span>&nbsp;: {password}</p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;">{app_url}</p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;">おかげで、<br>{app_name}</p>',
                    'nl' => '<p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;">Hallo,&nbsp;<br>Welkom bij {app_name}.</p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;"><span style="font-weight: bolder;">E-mail&nbsp;</span>: {email}<br><span style="font-weight: bolder;">Wachtwoord</span>&nbsp;: {password}</p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;">{app_url}</p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;">Bedankt,<br>{app_name}</p>',
                    'pl' => '<p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;">Dzień dobry,&nbsp;<br>Witamy w {app_name}.</p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;"><span style="font-weight: bolder;">E-mail&nbsp;</span>: {email}<br><span style="font-weight: bolder;">Hasło</span>&nbsp;: {password}</p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;">{app_url}</p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;">Dzięki,<br>{app_name}</p>',
                    'ru' => '<p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;">Привет,&nbsp;<br>Добро пожаловать в {app_name}.</p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;"><span style="font-weight: bolder;">Эл. адрес&nbsp;</span>: {email}<br><span style="font-weight: bolder;">пароль</span>&nbsp;: {password}</p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;">{app_url}</p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;">Спасибо,<br>{app_name}</p>',
                    'pt' => '<p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;">Olá   ,&nbsp;<br>Bem-vindo ao {app_name}.</p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;"><span style="font-weight: bolder;">E-mail&nbsp;</span>: {email}<br><span style="font-weight: bolder;">Senha</span>&nbsp;: {password}</p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;">{app_url}</p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;">Obrigada,<br>{app_name}</p>',
                ],
            ],
            'Invite Project' => [
                'subject' => 'New Project Assign',
                'lang' => [
                    'ar' => '<p>مرحبا،</p><p>تم تعيين مشروع جديد لك.</p><p><b>اسم المشروع </b>: {project_name}<br><b>حالة المشروع </b>:<b>&nbsp;</b>{project_status}<br><b>ميزانية المشروع </b>:<b> </b>{project_budget}<br><b>ساعات المشروع </b>:<b> </b>{project_hours}</p>',
                    'da' => '<p>Hej,</p><p>Der er tildelt nyt projekt til dig.</p><p><b>Projekt navn </b>: {project_name}<br><b>Projektstatus </b>:<b>&nbsp;</b>{project_status}<br><b>Projektbudget </b>:<b> </b>{project_budget}<br><b>Projekt timer </b>:<b> </b>{project_hours}</p>',
                    'de' => '<p>Hallo,</p><p>Ihnen wurde ein neues Projekt zugewiesen.</p><p><b>Projektname </b>: {project_name}<br><b>Projekt-Status </b>:<b>&nbsp;</b>{project_status}<br><b>Projektbudget </b>:<b> </b>{project_budget}<br><b>Projektstunden </b>:<b> </b>{project_hours}</p>',
                    'en' => '<p>Hello,</p><p>New Project has been assigned to you.</p><p><b>Project Name </b>: {project_name}<br><b>Project Status </b>:<b>&nbsp;</b>{project_status}<br><b>Project Budget </b>:<b> </b>{project_budget}<br><b>Project Hours </b>:<b> </b>{project_hours}</p>',
                    'es' => '<p>Hola,</p><p>Se le ha asignado un nuevo proyecto.</p><p><b>Nombre del proyecto </b>: {project_name}<br><b>Estado del proyecto </b>:<b>&nbsp;</b>{project_status}<br><b>Presupuesto del proyecto </b>:<b> </b>{project_budget}<br><b>Horas del proyecto </b>:<b> </b>{project_hours}</p>',
                    'fr' => '<p>Bonjour,</p><p>Un nouveau projet vous a été attribué.</p><p><b>nom du projet </b>: {project_name}<br><b>L\'état du projet </b>:<b>&nbsp;</b>{project_status}<br><b>Budget du projet </b>:<b> </b>{project_budget}<br><b>Heures du projet </b>:<b> </b>{project_hours}</p>',
                    'it' => '<p>Ciao,</p><p>Nuovo progetto ti è stato assegnato.</p><p><b>Nome del progetto </b>: {project_name}<br><b>Stato del progetto </b>:<b>&nbsp;</b>{project_status}<br><b>Budget del progetto </b>:<b> </b>{project_budget}<br><b>Ore del progetto </b>:<b> </b>{project_hours}</p>',
                    'ja' => '<p>こんにちは、</p><p>新しいプロジェクトが割り当てられました。</p><p><b>プロジェクト名 </b>: {project_name}<br><b>プロジェクトの状況 </b>:<b>&nbsp;</b>{project_status}<br><b>プロジェクト予算 </b>:<b> </b>{project_budget}<br><b>プロジェクト時間 </b>:<b> </b>{project_hours}</p>',
                    'nl' => '<p>Hallo,</p><p>Nieuw project is aan u toegewezen.</p><p><b>Naam van het project </b>: {project_name}<br><b>Project status </b>:<b>&nbsp;</b>{project_status}<br><b>Project budget </b>:<b> </b>{project_budget}<br><b>Projecturen </b>:<b> </b>{project_hours}</p>',
                    'pl' => '<p>Dzień dobry,</p><p>Nowy projekt został Ci przypisany.</p><p><b>Nazwa Projektu </b>: {project_name}<br><b>Stan projektu </b>:<b>&nbsp;</b>{project_status}<br><b>Budżet projektu </b>:<b> </b>{project_budget}<br><b>Godziny projektu </b>:<b> </b>{project_hours}</p>',
                    'ru' => '<p>Привет,</p><p>Новый проект был назначен вам.</p><p><b>название проекта </b>: {project_name}<br><b>Статус проекта </b>:<b>&nbsp;</b>{project_status}<br><b>Бюджет проекта </b>:<b> </b>{project_budget}<br><b>Часы работы проекта </b>:<b> </b>{project_hours}</p>',
                    'pt' => '<p>Olá,</p><p>Novo Projeto foi atribuído a você.</p><p><b>Nome do Projeto </b>: {project_name}<br><b>Status do Projeto </b>:<b>&nbsp;</b>{project_status}<br><b>Orçamento do Projeto </b>:<b> </b>{project_budget}<br><b>Projeto Horas </b>:<b> </b>{project_hours}</p>',
                ],
            ],
            'Task Assign' => [
                'subject' => 'New Task Assign',
                'lang' => [
                    'ar' => '<p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;"><span style="font-family: sans-serif;">مرحبا،</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">تم تعيين مهمة جديدة لك.</span></p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;"><span style="font-family: sans-serif;"><b>مهمة </b><span style="font-weight: bolder;">اسم</span>&nbsp;: {task_name}<br><span style="font-weight: bolder;">أولوية المهمة</span>&nbsp;: {task_priority}<br><b>مشروع المهمة </b>: {task_project}<b>&nbsp;<br>مرحلة المهمة </b>: {task_stage}</span></p>',
                    'da' => '<p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;"><span style="font-family: sans-serif;">Hej,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Ny opgave er blevet tildelt til dig.</span></p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;"><span style="font-family: sans-serif;"><b>Opgave </b><span style="font-weight: bolder;">Navn</span>&nbsp;: {task_name}<br><span style="font-weight: bolder;">Opgaveprioritet</span>&nbsp;: {task_priority}<br><b>Opgaveprojekt </b>: {task_project}<b>&nbsp;<br>Opgavefase </b>: {task_stage}</span></p>',
                    'de' => '<p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;"><span style="font-family: sans-serif;">Hallo,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Neue Aufgabe wurde Ihnen zugewiesen.</span></p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;"><span style="font-family: sans-serif;"><b>Aufgabe </b><span style="font-weight: bolder;">Name</span>&nbsp;: {task_name}<br><span style="font-weight: bolder;">Aufgabenpriorität</span>&nbsp;: {task_priority}<br><b>Aufgabenprojekt </b>: {task_project}<b>&nbsp;<br>Aufgabenphase </b>: {task_stage}</span></p>',
                    'en' => '<p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;"><span style="font-family: sans-serif;">Hello,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">New Task has been Assign to you.</span></p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;"><span style="font-family: sans-serif;"><b>Task </b><span style="font-weight: bolder;">Name</span>&nbsp;: {task_name}<br><span style="font-weight: bolder;">Task Priority</span>&nbsp;: {task_priority}<br><b>Task Project </b>: {task_project}<b>&nbsp;<br>Task Stage </b>: {task_stage}</span></p>',
                    'es' => '<p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;"><span style="font-family: sans-serif;">Hola,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Nueva tarea ha sido asignada a usted.</span></p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;"><span style="font-family: sans-serif;"><b>Tarea </b><span style="font-weight: bolder;">Nombre</span>&nbsp;: {task_name}<br><span style="font-weight: bolder;">Prioridad de tarea</span>&nbsp;: {task_priority}<br><b>Proyecto de tarea </b>: {task_project}<b>&nbsp;<br>Etapa de tarea </b>: {task_stage}</span></p>',
                    'fr' => '<p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;"><span style="font-family: sans-serif;">Bonjour,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Une nouvelle tâche vous a été assignée.</span></p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;"><span style="font-family: sans-serif;"><b>Tâche </b><span style="font-weight: bolder;">Nom</span>&nbsp;: {task_name}<br><span style="font-weight: bolder;">Priorité des tâches</span>&nbsp;: {task_priority}<br><b>Projet de tâche </b>: {task_project}<b>&nbsp;<br>Étape de la tâche </b>: {task_stage}</span></p>',
                    'it' => '<p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;"><span style="font-family: sans-serif;">Ciao,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">La nuova attività è stata assegnata a te.</span></p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;"><span style="font-family: sans-serif;"><b>Compito </b><span style="font-weight: bolder;">Nome</span>&nbsp;: {task_name}<br><span style="font-weight: bolder;">Priorità dell\'attività</span>&nbsp;: {task_priority}<br><b>Progetto di attività </b>: {task_project}<b>&nbsp;<br>Fase delle attività </b>: {task_stage}</span></p>',
                    'ja' => '<p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;"><span style="font-family: sans-serif;">こんにちは、</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">新しいタスクが割り当てられました。</span></p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;"><span style="font-family: sans-serif;"><b>仕事 </b><span style="font-weight: bolder;">名前</span>&nbsp;: {task_name}<br><span style="font-weight: bolder;">タスクの優先度</span>&nbsp;: {task_priority}<br><b>タスクプロジェクト </b>: {task_project}<b>&nbsp;<br>タスクステージ </b>: {task_stage}</span></p>',
                    'nl' => '<p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;"><span style="font-family: sans-serif;">Hallo,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Nieuwe taak is aan u toegewezen.</span></p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;"><span style="font-family: sans-serif;"><b>Taak </b><span style="font-weight: bolder;">Naam</span>&nbsp;: {task_name}<br><span style="font-weight: bolder;">Taakprioriteit</span>&nbsp;: {task_priority}<br><b>Taakproject </b>: {task_project}<b>&nbsp;<br>Taakfase </b>: {task_stage}</span></p>',
                    'pl' => '<p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;"><span style="font-family: sans-serif;">Dzień dobry,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Nowe zadanie zostało Ci przypisane.</span></p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;"><span style="font-family: sans-serif;"><b>Zadanie </b><span style="font-weight: bolder;">Imię</span>&nbsp;: {task_name}<br><span style="font-weight: bolder;">Priorytet zadania</span>&nbsp;: {task_priority}<br><b>Projekt zadania </b>: {task_project}<b>&nbsp;<br>Etap zadania </b>: {task_stage}</span></p>',
                    'ru' => '<p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;"><span style="font-family: sans-serif;">Привет,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Новая задача была назначена вам.</span></p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;"><span style="font-family: sans-serif;"><b>задача </b><span style="font-weight: bolder;">название</span>&nbsp;: {task_name}<br><span style="font-weight: bolder;">Приоритет задачи</span>&nbsp;: {task_priority}<br><b>Задача Проект </b>: {task_project}<b>&nbsp;<br>Этап задачи </b>: {task_stage}</span></p>',
                    'pt' => '<p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;"><span style="font-family: sans-serif;">Olá,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Nova Tarefa tem sido Assign para você.</span></p><p style="line-height: 28px; font-family: Nunito, &quot;Segoe UI&quot;, arial; font-size: 14px;"><span style="font-family: sans-serif;"><b>Tarefa </b><span style="font-weight: bolder;">Nome da </span>&nbsp;: {task_name}<br><span style="font-weight: bolder;">Prioridade da Tarefa </span>&nbsp;: {task_priority}<br><b>Projeto de tarefa </b>: {task_project}<b>&nbsp;<br>Estágio da tarefa </b>: {task_stage}</span></p>',
                ],
            ],
            'Create Timesheet' => [
                'subject' => 'New Timesheet Assign',
                'lang' => [
                    'ar' => '<p><span style="font-size: 14px; font-family: sans-serif;">مرحبا،</span><br style="font-size: 14px; font-family: sans-serif;"><span style="font-size: 14px; font-family: sans-serif;">تم تعيين جدول زمني جديد لك.</span></p><p><span style="font-size: 14px; font-family: sans-serif;"><b>مشروع الجدول الزمني</b> : {timesheet_project}<br><b>مهمة الجدول الزمني</b> : {timesheet_task}<br><b>وقت الجدول الزمني</b> : {timesheet_time}<br><b>تاريخ الجدول الزمني</b> : {timesheet_date}</span></p><p><br></p>',
                    'da' => '<p><span style="font-size: 14px; font-family: sans-serif;">Hej,</span><br style="font-size: 14px; font-family: sans-serif;"><span style="font-size: 14px; font-family: sans-serif;">Nyt timesheet er blevet tildelt til dig.</span></p><p><span style="font-size: 14px; font-family: sans-serif;"><b>Timesheet-projekt</b> : {timesheet_project}<br><b>Timesheet-opgave</b> : {timesheet_task}<br><b>Tidsskema Tid</b> : {timesheet_time}<br><b>Tidspunkt Dato</b> : {timesheet_date}</span></p><p><br></p>',
                    'de' => '<p><span style="font-size: 14px; font-family: sans-serif;">Hallo,</span><br style="font-size: 14px; font-family: sans-serif;"><span style="font-size: 14px; font-family: sans-serif;">Neue Arbeitszeittabelle wurde Ihnen zugewiesen.</span></p><p><span style="font-size: 14px; font-family: sans-serif;"><b>Arbeitszeittabellenprojekt</b> : {timesheet_project}<br><b>Arbeitszeittabellenaufgabe</b> : {timesheet_task}<br><b>Arbeitszeittabelle Zeit</b> : {timesheet_time}<br><b>Arbeitszeittabelle Datum</b> : {timesheet_date}</span></p><p><br></p>',
                    'en' => '<p><span style="font-size: 14px; font-family: sans-serif;">Hello,</span><br style="font-size: 14px; font-family: sans-serif;"><span style="font-size: 14px; font-family: sans-serif;">New Timesheet has been Assign to you.</span></p><p><span style="font-size: 14px; font-family: sans-serif;"><b>Timesheet Project</b> : {timesheet_project}<br><b>Timesheet Task</b> : {timesheet_task}<br><b>Timesheet Time</b> : {timesheet_time}<br><b>Timesheet Date</b> : {timesheet_date}</span></p><p><br></p>',
                    'es' => '<p><span style="font-size: 14px; font-family: sans-serif;">Hola,</span><br style="font-size: 14px; font-family: sans-serif;"><span style="font-size: 14px; font-family: sans-serif;">Se le ha asignado una nueva hoja de tiempo.</span></p><p><span style="font-size: 14px; font-family: sans-serif;"><b>Proyecto de parte de horas</b> : {timesheet_project}<br><b>Tarea de parte de horas</b> : {timesheet_task}<br><b>Tiempo de parte de horas</b> : {timesheet_time}<br><b>Fecha de parte de horas</b> : {timesheet_date}</span></p><p><br></p>',
                    'fr' => '<p><span style="font-size: 14px; font-family: sans-serif;">Bonjour,</span><br style="font-size: 14px; font-family: sans-serif;"><span style="font-size: 14px; font-family: sans-serif;">Une nouvelle feuille de temps vous a été attribuée.</span></p><p><span style="font-size: 14px; font-family: sans-serif;"><b>Projet de feuille de temps</b> : {timesheet_project}<br><b>Tâche de feuille de temps</b> : {timesheet_task}<br><b>Temps de feuille de temps</b> : {timesheet_time}<br><b>Date de la feuille de temps</b> : {timesheet_date}</span></p><p><br></p>',
                    'it' => '<p><span style="font-size: 14px; font-family: sans-serif;">Ciao,</span><br style="font-size: 14px; font-family: sans-serif;"><span style="font-size: 14px; font-family: sans-serif;">La nuova scheda attività è stata assegnata a te.</span></p><p><span style="font-size: 14px; font-family: sans-serif;"><b>Progetto scheda attività</b> : {timesheet_project}<br><b>Attività scheda attività</b> : {timesheet_task}<br><b>Timesheet Time</b> : {timesheet_time}<br><b>Data scheda attività</b> : {timesheet_date}</span></p><p><br></p>',
                    'ja' => '<p><span style="font-size: 14px; font-family: sans-serif;">こんにちは、</span><br style="font-size: 14px; font-family: sans-serif;"><span style="font-size: 14px; font-family: sans-serif;">新しいタイムシートが割り当てられました。</span></p><p><span style="font-size: 14px; font-family: sans-serif;"><b>タイムシートプロジェクト</b> : {timesheet_project}<br><b>タイムシートタスク</b> : {timesheet_task}<br><b>タイムシート時間</b> : {timesheet_time}<br><b>タイムシートの日付</b> : {timesheet_date}</span></p><p><br></p>',
                    'nl' => '<p><span style="font-size: 14px; font-family: sans-serif;">Hallo,</span><br style="font-size: 14px; font-family: sans-serif;"><span style="font-size: 14px; font-family: sans-serif;">New Timesheet is aan u toewijzen.</span></p><p><span style="font-size: 14px; font-family: sans-serif;"><b>Timesheet Project</b> : {timesheet_project}<br><b>Timesheet-taak</b> : {timesheet_task}<br><b>Timesheet Time</b> : {timesheet_time}<br><b>Datum rooster</b> : {timesheet_date}</span></p><p><br></p>',
                    'pl' => '<p><span style="font-size: 14px; font-family: sans-serif;">Dzień dobry,</span><br style="font-size: 14px; font-family: sans-serif;"><span style="font-size: 14px; font-family: sans-serif;">Nowy grafik został przypisany do Ciebie.</span></p><p><span style="font-size: 14px; font-family: sans-serif;"><b>Projekt grafiku</b> : {timesheet_project}<br><b>Zadanie grafiku</b> : {timesheet_task}<br><b>Czas pracy</b> : {timesheet_time}<br><b>Data grafiku</b> : {timesheet_date}</span></p><p><br></p>',
                    'ru' => '<p><span style="font-size: 14px; font-family: sans-serif;">Привет,</span><br style="font-size: 14px; font-family: sans-serif;"><span style="font-size: 14px; font-family: sans-serif;">Новое расписание было назначено вам.</span></p><p><span style="font-size: 14px; font-family: sans-serif;"><b>Проект расписания</b> : {timesheet_project}<br><b>Задача расписания</b> : {timesheet_task}<br><b>Расписание</b> : {timesheet_time}<br><b>Дата расписания</b> : {timesheet_date}</span></p><p><br></p>',
                    'pt' => '<p><span style="font-size: 14px; font-family: sans-serif;">Olá,</span><br style="font-size: 14px; font-family: sans-serif;"><span style="font-size: 14px; font-family: sans-serif;">New Timesheet tem sido Assign para você.</span></p><p><span style="font-size: 14px; font-family: sans-serif;"><b>Projeto de Témesheet</b> : {timesheet_project}<br><b>Tarefa do Timesheet</b> : {timesheet_task}<br><b>Tempo de cronômetro</b> : {timesheet_time}<br><b>Data do cronômetro</b> : {timesheet_date}</span></p><p><br></p>',
                ],
            ],
            'Contract Assign' => [
                'subject' => 'New Contract Assign',
                'lang' => [ 'ar' => '<p><span style="font-size: 14px; font-family: sans-serif;">مرحبًا,</span>
                            <br style="font-size: 14px; font-family: sans-serif;">
                            <span style="font-size: 14px; font-family: sans-serif;">تم تعيين عقد جديد لك.</span>
                            </p><p><span style="font-size: 14px; font-family: sans-serif;">
                            <b>اسم العميل</b>  : {client_name}<br>
                            <b>اسم العقد</b> :   {contract_name}<br>
                            <b>نوع العقد</b> : {contract_type}<br>
                            <b>قيمة العقد</b> : {contract_value}<br>
                            <b>تاريخ البدء</b> : {start_date}<br>
                            <b>تاريخ الانتهاء</b> : {end_date}</span></p><p><br></p>',

                         

                    'da' => '<p><span style="font-size: 14px; font-family: sans-serif;">Hej,</span>
                            <br style="font-size: 14px; font-family: sans-serif;">
                            <span style="font-size: 14px; font-family: sans-serif;">Ny kontrakt er blevet tildelt dig.</span>
                            </p><p><span style="font-size: 14px; font-family: sans-serif;">
                            <b>Kundenavn</b>  : {client_name}<br>
                            <b>Kontrakt navn</b> :   {contract_name}<br>
                            <b>Kontrakttype</b> : {contract_type}<br>
                            <b>Kontraktværdi</b> : {contract_value}<br>
                            <b>Start dato</b> : {start_date}<br>
                            <b>Slutdato</b> : {end_date}</span></p><p><br></p>',

                           

                    'de' => '<p><span style="font-size: 14px; font-family: sans-serif;">Hallo,</span>
                            <br style="font-size: 14px; font-family: sans-serif;">
                            <span style="font-size: 14px; font-family: sans-serif;">Ihnen wurde ein neuer Vertrag zugewiesen.</span>
                            </p><p><span style="font-size: 14px; font-family: sans-serif;">
                            <b>Kundenname</b>  : {client_name}<br>
                            <b>Vertragsname</b> :   {contract_name}<br>
                            <b>Vertragstyp</b> : {contract_type}<br>
                            <b>Vertragswert</b> : {contract_value}<br>
                            <b>Anfangsdatum</b> : {start_date}<br>
                            <b>Endtermin</b> : {end_date}</span></p><p><br></p>',

                        
                    'en' => '<p><span style="font-size: 14px; font-family: sans-serif;">Hello,</span>
                            <br style="font-size: 14px; font-family: sans-serif;">
                            <span style="font-size: 14px; font-family: sans-serif;">New Contract has been Assign to you.</span>
                            </p><p><span style="font-size: 14px; font-family: sans-serif;">
                            <b>Client Name</b>  : {client_name}<br>
                            <b>Contract Name</b> :   {contract_name}<br>
                            <b>Contract Type</b> : {contract_type}<br>
                            <b>Contract Value</b> : {contract_value}<br>
                            <b>Start date</b> : {start_date}<br>
                            <b>End date</b> : {end_date}</span></p><p><br></p>',

                    'es' => '<p><span style="font-size: 14px; font-family: sans-serif;">Hola,</span>
                            <br style="font-size: 14px; font-family: sans-serif;">
                            <span style="font-size: 14px; font-family: sans-serif;">Se le ha asignado un nuevo contrato.</span>
                            </p><p><span style="font-size: 14px; font-family: sans-serif;">
                            <b>nombre del cliente</b>  : {client_name}<br>
                            <b>Nombre del contrato</b> :   {contract_name}<br>
                            <b>tipo de contrato</b> : {contract_type}<br>
                            <b>Valor del contrato</b> : {contract_value}<br>
                            <b>Fecha de inicio</b> : {start_date}<br>
                            <b>Fecha final</b> : {end_date}</span></p><p><br></p>',


                    'fr' => '<p><span style="font-size: 14px; font-family: sans-serif;">Bonjour,</span>
                            <br style="font-size: 14px; font-family: sans-serif;">
                            <span style="font-size: 14px; font-family: sans-serif;">Un nouveau contrat vous a été attribué.</span>
                            </p><p><span style="font-size: 14px; font-family: sans-serif;">
                            <b>Nom du client</b>  : {client_name}<br>
                            <b>Nom du contrat</b> :   {contract_name}<br>
                            <b>Type de contrat</b> : {contract_type}<br>
                            <b>Valeur du contrat</b> : {contract_value}<br>
                            <b>Date de début</b> : {start_date}<br>
                            <b>Date de fin</b> : {end_date}</span></p><p><br></p>',

                          

                    'it' => '<p><span style="font-size: 14px; font-family: sans-serif;">Ciao,</span>
                            <br style="font-size: 14px; font-family: sans-serif;">
                            <span style="font-size: 14px; font-family: sans-serif;">Ti è stato assegnato un nuovo contratto.</span>
                            </p><p><span style="font-size: 14px; font-family: sans-serif;">
                            <b>nome del cliente</b>  : {client_name}<br>
                            <b>Nome del contratto</b> :   {contract_name}<br>
                            <b>tipo di contratto</b> : {contract_type}<br>
                            <b>Valore del contratto</b> : {contract_value}<br>
                            <b>Data d\'inizio</b> : {start_date}<br>
                            <b>Data di fine</b> : {end_date}</span></p><p><br></p>',


                    'ja' => '<p><span style="font-size: 14px; font-family: sans-serif;">こんにちは,</span>
                            <br style="font-size: 14px; font-family: sans-serif;">
                            <span style="font-size: 14px; font-family: sans-serif;">新しい契約があなたに割り当てられました.</span>
                            </p><p><span style="font-size: 14px; font-family: sans-serif;">
                            <b>クライアント名</b>  : {client_name}<br>
                            <b>契約名</b> :   {contract_name}<br>
                            <b>契約の種類</b> : {contract_type}<br>
                            <b>契約金額</b> : {contract_value}<br>
                            <b>開始日</b> : {start_date}<br>
                            <b>終了日</b> : {end_date}</span></p><p><br></p>',

                    'nl' => '<p><span style="font-size: 14px; font-family: sans-serif;">Hallo,</span>
                            <br style="font-size: 14px; font-family: sans-serif;">
                            <span style="font-size: 14px; font-family: sans-serif;">Nieuw contract is aan u toegewezen.</span>
                            </p><p><span style="font-size: 14px; font-family: sans-serif;">
                            <b>klantnaam</b>  : {client_name}<br>
                            <b>Contractnaam:</b> :   {contract_name}<br>
                            <b>Contract type</b> : {contract_type}<br>
                            <b>Contract waarde</b> : {contract_value}<br>
                            <b>Startdatum</b> : {start_date}<br>
                            <b>Einddatum</b> : {end_date}</span></p><p><br></p>',

                          

                    'pl' => '<p><span style="font-size: 14px; font-family: sans-serif;">Witam,</span>
                            <br style="font-size: 14px; font-family: sans-serif;">
                            <span style="font-size: 14px; font-family: sans-serif;">Nowa umowa została Ci przypisana.</span>
                            </p><p><span style="font-size: 14px; font-family: sans-serif;">
                            <b>Nazwa klienta</b>  : {client_name}<br>
                            <b>Nazwa kontraktu</b> :   {contract_name}<br>
                            <b>Typ kontraktu</b> : {contract_type}<br>
                            <b>Wartość kontraktu</b> : {contract_value}<br>
                            <b>Data rozpoczęcia</b> : {start_date}<br>
                            <b>Data zakonczenia</b> : {end_date}</span></p><p><br></p>',

                        

                    'ru' => '<p><span style="font-size: 14px; font-family: sans-serif;">Привет,</span>
                            <br style="font-size: 14px; font-family: sans-serif;">
                            <span style="font-size: 14px; font-family: sans-serif;">Вам назначен новый контракт.</span>
                            </p><p><span style="font-size: 14px; font-family: sans-serif;">
                            <b>имя клиента</b>  : {client_name}<br>
                            <b>Название контракта</b> :   {contract_name}<br>
                            <b>Форма контракта</b> : {contract_type}<br>
                            <b>Стоимость контракта</b> : {contract_value}<br>
                            <b>Дата начала</b> : {start_date}<br>
                            <b>Дата окончания</b> : {end_date}</span></p><p><br></p>',

                          

                    'pt' => '<p><span style="font-size: 14px; font-family: sans-serif;">Olá,</span>
                            <br style="font-size: 14px; font-family: sans-serif;">
                            <span style="font-size: 14px; font-family: sans-serif;">Novo contrato foi atribuído a você.</span>
                            </p><p><span style="font-size: 14px; font-family: sans-serif;">
                            <b>Nome do cliente</b>  : {client_name}<br>
                            <b>Nome do contrato</b> :   {contract_name}<br>
                            <b>tipo de contrato</b> : {contract_type}<br>
                            <b>Valor do contrato</b> : {contract_value}<br>
                            <b>Data de início</b> : {start_date}<br>
                            <b>Data final</b> : {end_date}</span></p><p><br></p>',


                ],
            ],
        ];

        $email = EmailTemplate::all();

        // Make entry in email_template_lang tbl
        foreach($email as $e)
        {
            foreach($defaultTemplate[$e->name]['lang'] as $lang => $content)
            {
                EmailTemplateLang::create([
                                              'parent_id' => $e->id,
                                              'lang' => $lang,
                                              'subject' => $defaultTemplate[$e->name]['subject'],
                                              'content' => $content,
                                              'from' => (env('APP_NAME'))?env('APP_NAME'):"Taskgo SaaS",
                                          ]);
            }
        }
    }

    // Get All permission
    public function getAllPermission()
    {
        return [
            "create milestone",
            "edit milestone",
            "delete milestone",
            "create task",
            "edit task",
            "delete task",
            "show task",
            "move task",
            "create timesheet",
            "show as admin timesheet",
            "create expense",
            "show expense",
            "show activity",
            "project setting",
        ];
    }

    // Get project wise permission
    public function getPermission($project_id)
    {
        $data = ProjectUser::where('project_id', '=', $project_id)->where('user_id', '=', $this->id)->first();

        return json_decode($data->user_permission, true);
    }

    // check project is shared or not
    public function checkProject($project_id)
    {
        $user_projects = $this->projects()->pluck('permission', 'project_id')->toArray();
        if(array_key_exists($project_id, $user_projects))
        {
            $projectstatus = $user_projects[$project_id] == 'owner' ? 'Owner' : 'Shared';
        }

        return $projectstatus;
    }

    // Check Plan
    public function getPlan()
    {
        $user = \App\Models\User::find($this->id);

        return Plan::find($user->plan);
    }

    // for Assign plan
    public function assignPlan($planID, $frequency = '')
    {
        $usr  = $this;
        $plan = Plan::find($planID);

        if($plan)
        {
            if(\Auth::check())
            {
                $usr_contact = $usr->contacts->pluck('user_id')->toArray();

                if(count($usr_contact) > 0)
                {
                    $users     = User::whereIn('id', $usr_contact)->get();
                    $userCount = 0;

                    foreach($users as $user)
                    {
                        $userCount++;
                        $user->is_active = ($plan->max_users == -1 || $userCount <= $plan->max_users) ? 1 : 0;
                        $user->save();
                    }
                }

                $user_project = $usr->projects()->pluck('project_id')->toArray();

                if(count($user_project) > 0)
                {
                    $projects     = Project::whereIn('id', $user_project)->get();
                    $projectCount = 0;

                    foreach($projects as $project)
                    {
                        $projectCount++;
                        $project->is_active = ($plan->max_projects == -1 || $projectCount <= $plan->max_projects) ? 1 : 0;
                        $project->save();
                    }
                }
            }

            $this->plan = $plan->id;
            if($frequency == 'weekly')
            {
                $this->plan_expire_date = Carbon::now()->addWeeks(1)->isoFormat('YYYY-MM-DD');
            }
            elseif($frequency == 'monthly')
            {
                $this->plan_expire_date = Carbon::now()->addMonths(1)->isoFormat('YYYY-MM-DD');
            }
            elseif($frequency == 'annual')
            {
                $this->plan_expire_date = Carbon::now()->addYears(1)->isoFormat('YYYY-MM-DD');
            }
            else
            {
                $this->plan_expire_date = null;
            }
            $this->save();

            return ['is_success' => true];
        }
        else
        {
            return [
                'is_success' => false,
                'error' => __('Plan is deleted.'),
            ];
        }
    }

    // for get user role
    public function usrRole()
    {
        $usr = \Auth::user();
        if($usr->id != $this->id)
        {
            $role      = UserContact::where('parent_id', '=', $usr->id)->where('user_id', '=', $this->id)->first();
            $arrReturn = [
                'color' => ($role->role == 'user') ? 'primary' : 'warning',
                'role' => ucfirst($role->role),
            ];
        }
        else
        {
            $arrReturn = [
                'color' => 'success',
                'role' => 'User',
            ];
        }


        return $arrReturn;
    } 

    public static function GetusrRole()
    {
        $usr = \Auth::user();
        // dd($usr);

        $role = UserContact::where('user_id', '=', $usr->id)->first();

        if ($role != null) {
            $arrReturn = [
                'color' => ($role->role == 'user') ? 'primary' : 'warning',
                'role' => $role->role,
            ];

        } else {
            $arrReturn = [
                'color' => '',
                'role' => '',
            ];
        }

        return $arrReturn;
    }

    public function user_contacts()
    {
        return $this->hasOne('App\Models\UserContact', 'user_id', 'id');
    }   

    // get user's created taxes
    public function taxes()
    {
        return $this->hasMany('App\Models\Tax', 'created_by', 'id');
    }     

    public function contracttype()
    {
        return $this->hasMany('App\Models\ContractType', 'created_by', 'id');
    }

    // get decoded details
    public function decodeDetails($user_id = '')
    {
        $arr = [
            'light_logo' => '',
            'dark_logo' => '',
            'address' => '',
            'city' => '',
            'state' => '',
            'zipcode' => '',
            'country' => '',
            'telephone' => '',
            'invoice_template' => 'template1',
            'invoice_color' => 'ffffff',
            'invoice_logo' => '',
            'invoice_footer_title' => '',
            'invoice_footer_note' => '',
            'interval_time'=>''
        ];

        if(empty($user_id))
        {
            $data = json_decode($this->details, true);
        }
        else
        {
            $usr  = User::find($user_id);
            $data = json_decode($usr->details, true);
        }

        if(!empty($data))
        {
            foreach($arr as $key => $val)
            {
                $arr[$key] = (!empty($data[$key])) ? $data[$key] : $arr[$key];
            }
        }

        $arr['light_logo']   = empty($arr['light_logo']) ? 'logo/logo.png' : $arr['light_logo'];
        $arr['dark_logo']    = empty($arr['dark_logo']) ? 'logo/logo.png' : $arr['dark_logo'];
        $arr['invoice_logo'] = empty($arr['invoice_logo']) ? 'logo/logo.png' : $arr['invoice_logo'];


        return $arr;
    }

    public function cancel_subscription($user_id = false)
    {
        $user = User::find($user_id);

        if(!$user_id && !$user && $user->payment_subscription_id != '' && $user->payment_subscription_id != null)
        {
            return true;
        }

        $data            = explode('###', $user->payment_subscription_id);
        $type            = strtolower($data[0]);
        $subscription_id = $data[1];

        $paymentSetting = Utility::getPaymentSetting();

        switch($type)
        {
            case 'stripe':

                /* Initiate Stripe */ \Stripe\Stripe::setApiKey($paymentSetting['stripe_secret']);

                /* Cancel the Stripe Subscription */
                $subscription = \Stripe\Subscription::retrieve($subscription_id);
                $subscription->cancel();

                break;

            case 'paypal':

                /* Initiate paypal */ $paypal = new \PayPal\Rest\ApiContext(new \PayPal\Auth\OAuthTokenCredential($paymentSetting['paypal_client_id'], $paymentSetting['paypal_secret_key']));
                $paypal->setConfig(['mode' => $paymentSetting['paypal_mode']]);

                /* Create an Agreement State Descriptor, explaining the reason to suspend. */
                $agreement_state_descriptior = new \PayPal\Api\AgreementStateDescriptor();
                $agreement_state_descriptior->setNote('Suspending the agreement');

                /* Get details about the executed agreement */
                $agreement = \PayPal\Api\Agreement::get($subscription_id, $paypal);

                /* Suspend */
                $agreement->suspend($agreement_state_descriptior, $paypal);

                break;
        }

        $user->payment_subscription_id = '';
        $user->save();
    }

    public static function invoiceNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["invoice_prefix"] . sprintf("%05d", $number);
    }

    public static function dateFormat($date)
    {
        $settings = Utility::settings();

        return date($settings['site_date_format'], strtotime($date));
    }

    public static function priceFormat($price)
    {
        $settings = Utility::settings();

        return (($settings['site_currency_symbol_position'] == "pre") ? $settings['site_currency_symbol'] : '') . number_format($price, 2) . (($settings['site_currency_symbol_position'] == "post") ? $settings['site_currency_symbol'] : '');
    }
    public function projectsList(){
        $user_projects = $this->projects()->pluck('project_id')->toArray();
        $project = Project::select('projects.id','projects.name')->with('tasks')->whereIn('id',$user_projects)->get()->toArray();
        return $project;
    }

    public function timeFormat($time)
    {
        $settings = Utility::settings();

        return date($settings['site_time_format'], strtotime($time));
    }

   
}
