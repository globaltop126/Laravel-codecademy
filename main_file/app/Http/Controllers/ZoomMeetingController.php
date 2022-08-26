<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\User;
use App\Models\UserContact;
use App\Models\ZoomMeeting;
use App\Traits\ZoomMeetingTrait;
use Illuminate\Http\Request;

class ZoomMeetingController extends Controller
{
    use ZoomMeetingTrait;
    const MEETING_TYPE_INSTANT = 1;
    const MEETING_TYPE_SCHEDULE = 2;
    const MEETING_TYPE_RECURRING = 3;
    const MEETING_TYPE_FIXED_RECURRING_FIXED = 8;
    const MEETING_URL = "https://api.zoom.us/v2/";

    public function index()
    {

        $client = UserContact::where('role', 'client')->where('user_id', \Auth::user()->id)->first();

        $user = UserContact::where('role', 'user')->where('user_id', \Auth::user()->id)->first();

        if ($user) {
            $meetings = ZoomMeeting::whereRaw("FIND_IN_SET(?, user_id)", [\Auth::user()->id])->get();
        } else if ($client) {
            $meetings = ZoomMeeting::whereRaw("FIND_IN_SET(?, client_id)", [\Auth::user()->id])->get();
        } else {
            $meetings = ZoomMeeting::where('created_by', \Auth::user()->id)->get();
        }
        $this->statusUpdate();

        return view('zoommeeting.index', compact('meetings'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $project = Project::pluck('name', 'id');
        return view('zoommeeting.create', compact('project'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        if (\Auth::user()->type == 'owner') 
        {
            $validator = \Validator::make(
                $request->all(), [
                    'title' => 'required',
                    'project_id' => 'required',
                    'start_date' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->route('zoommeeting.index')->with('error', $messages->first());
            }

            $data['title'] = $request->title;
            $data['start_time'] = date('y:m:d H:i:s', strtotime($request->start_date));
            $data['duration'] = (int) $request->duration;
            $data['password'] = $request->password;
            $data['host_video'] = 0;
            $data['participant_video'] = 0;

            try {
                $meeting_create = $this->createmitting($data);
                \Log::info('Meeting');
                \Log::info((array) $meeting_create);
                if (isset($meeting_create['success']) && $meeting_create['success'] == true) {
                    $meeting_id = isset($meeting_create['data']['id']) ? $meeting_create['data']['id'] : 0;
                    $start_url = isset($meeting_create['data']['start_url']) ? $meeting_create['data']['start_url'] : '';
                    $join_url = isset($meeting_create['data']['join_url']) ? $meeting_create['data']['join_url'] : '';
                    $status = isset($meeting_create['data']['status']) ? $meeting_create['data']['status'] : '';

                    $client = ProjectUser::where('permission', 'client')->first();

                    $zoommeeting = new ZoomMeeting();
                    $zoommeeting->title = $request->title;
                    $zoommeeting->meeting_id = $meeting_id;
                    $zoommeeting->project_id = $request->project_id;
                    $zoommeeting->user_id = implode(',', $request->employee);
                    $zoommeeting->start_date = date('y:m:d H:i:s', strtotime($request->start_date));
                    $zoommeeting->duration = $request->duration;
                    $zoommeeting->start_url = $start_url;
                    $zoommeeting->client_id = $request->client_id;
                    if (!empty($zoommeeting->client_id)) {
                        $zoommeeting->client_id = implode(',', $zoommeeting->client_id);
                    } else {
                        $zoommeeting->client_id = 0;
                    }
                    $zoommeeting->join_url = $join_url;
                    $zoommeeting->status = $status;
                    $zoommeeting->password = $request->password;
                    $zoommeeting->created_by = \Auth::user()->creatorId();

                    $zoommeeting->save();

                    return redirect()->route('zoommeeting.index')->with('success', __('Zoom Meeting successfully created.'));
                } else {
                    return redirect()->back()->with('error', __('Meeting not created.'));
                }

            } catch (\Exception $e) {
                return redirect()->back()->with('error', __("Invalid Api key and Secret key."));
            }

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ZoomMeeting  $zoomMeeting
     * @return \Illuminate\Http\Response
     */
    public function show(ZoomMeeting $zoommeeting)
    {

        $client = UserContact::where('role', 'client')->where('user_id', \Auth::user()->id)->first();

        $user = UserContact::where('role', 'user')->where('user_id', \Auth::user()->id)->first();

        if ($user) {
            $meeting = ZoomMeeting::whereRaw("FIND_IN_SET(?, user_id)", [\Auth::user()->id])->first();
        } else if ($client) {
            $meeting = ZoomMeeting::whereRaw("FIND_IN_SET(?, client_id)", [\Auth::user()->id])->first();
        } else {
            $meeting = ZoomMeeting::where('created_by', \Auth::user()->id)->first();
        }
        return view('zoommeeting.show', compact('meeting'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ZoomMeeting  $zoomMeeting
     * @return \Illuminate\Http\Response
     */
    public function edit(ZoomMeeting $zoomMeeting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ZoomMeeting  $zoomMeeting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ZoomMeeting $zoomMeeting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ZoomMeeting  $zoomMeeting
     * @return \Illuminate\Http\Response
     */
    public function destroy(ZoomMeeting $zoommeeting)
    {

        $zoommeeting->delete();
        return redirect()->route('zoommeeting.index')->with('success', __('Meeting successfully deleted.'));
    }

    public function projectwiseclient($id)
    {

        $project_user = ProjectUser::select('user_id')->where('project_id', $id)->where('permission', 'user')->get();
        $project_client = ProjectUser::select('user_id')->where('project_id', $id)->where('permission', 'client')->get();

        $users = [];
        foreach ($project_user as $key => $value) {
            $user = User::select('id', 'name')->where('id', $value->user_id)->first();
            $users1['id'] = $user->id;
            $users1['name'] = $user->name;
            $users[] = $users1;
        }

        $clients = [];
        foreach ($project_client as $key => $value) {
            $client = User::select('id', 'name')->where('id', $value->user_id)->first();
            $client1['id'] = $client->id;
            $client1['name'] = $client->name;
            $clients[] = $client1;
        }

        $member['users'] = $users;
        $member['clients'] = $clients;

        return \Response::json($member);
    }

    public function statusUpdate()
    {
        $meetings = ZoomMeeting::where('created_by', \Auth::user()->id)->pluck('meeting_id');
        // dd(\Auth::user()->id);
        try
        {
            foreach ($meetings as $meeting) {
                $data = $this->get($meeting);
                if (isset($data['data']) && !empty($data['data'])) {
                    $meeting = ZoomMeeting::where('meeting_id', $meeting)->update(['status' => $data['data']['status']]);
                }

            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __("Invalide Token."));
        }

    }

    //calendar view
    public function calendar(Request $request)
    {
        $client = UserContact::where('role', 'client')->where('user_id', \Auth::user()->id)->first();

        $user = UserContact::where('role', 'user')->where('user_id', \Auth::user()->id)->first();

        if ($user) {
            $meetings = ZoomMeeting::whereRaw("FIND_IN_SET(?, user_id)", [\Auth::user()->id])->get();
        } else if ($client) {
            $meetings = ZoomMeeting::whereRaw("FIND_IN_SET(?, client_id)", [\Auth::user()->id])->get();
        } else {
            $meetings = ZoomMeeting::where('created_by', \Auth::user()->id)->get();
        }
        $arrMeeting = [];
        foreach ($meetings as $meeting) {

            $arr['id'] = $meeting['id'];
            $arr['title'] = $meeting['title'];
            $arr['meeting_id'] = $meeting['meeting_id'];
            $arr['start'] = $meeting['start_date'];
            $arr['duration'] = $meeting['duration'];
            $arr['start_url'] = $meeting['start_url'];
            $arr['className'] = 'bg-red';
            $arr['url'] = route('zoommeeting.show', $meeting['id']);
            $arrMeeting[] = $arr;

        }
        $arrMeeting = str_replace('"[', '[', str_replace(']"', ']', json_encode($arrMeeting)));

        return view('zoommeeting.calendar', compact('arrMeeting'));

    }

}
