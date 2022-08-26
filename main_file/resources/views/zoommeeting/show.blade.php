<div class="form-body">
    <div class="row">
        <div class="col-md-6 ">
            <div class="form-group">
                <label><b>{{__('Zoom Meeting Title')}}</b></label>
                <p> {{$meeting->title}} </p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label><b>{{__('Zoom Meeting ID')}}</b></label>
                <p> {{$meeting->meeting_id}} </p>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-md-12 ">
            <div class="form-group">
                <label><b>{{__('Project Name')}}</b></label>
                <p> {{ !empty($meeting->projectName)?$meeting->projectName->name:'' }}</p>
                
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-md-6 ">
            <div class="form-group">
                <label><b>{{__('User Name')}}</b></label>
                <p> 
                    @foreach($meeting->users($meeting->user_id) as  $projectUser)
                        {{ $projectUser->name }}<br> 
                    @endforeach

                </p>
            </div>
        </div>
        <div class="col-md-6 ">
            <div class="form-group">
                <label><b>{{__('Client Name')}}</b></label>
                <p>   
                    @if($meeting->client_id != '0')
                        @foreach($meeting->clients($meeting->client_id) as $projectClient)
                                {{ $projectClient->name }} <br>
                        @endforeach
                    @endif
                </p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label><b>{{__('Date')}}</b></label>
                <p>{{\Auth::user()->dateFormat($meeting->start_date)}}</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label><b>{{__('Time')}}</b></label>
                <p>{{\Auth::user()->timeFormat($meeting->start_date)}}</p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label><b>{{__('Duration')}}</b></label>
                <p> {{$meeting->duration }} {{ __('Minutes') }}</p>
            </div>
        </div>
    </div>
</div>


