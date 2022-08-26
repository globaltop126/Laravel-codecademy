<div class="col-12">
    <div class="card">
        <div class="table-responsive">
            <table class="table align-items-center">
                <thead>
                <tr>
                    <th scope="col" class="sort" data-sort="name">{{__('Project')}}</th>
                    <th scope="col" class="sort" data-sort="status">{{__('Status')}}</th>
                    <th scope="col">{{__('Users')}}</th>
                    <th scope="col" class="sort" data-sort="completion">{{__('Completion')}}</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody class="list">
                @if(isset($projects) && !empty($projects) && count($projects) > 0)
                    @foreach ($projects as $key => $project)
                        <tr>
                            <th scope="row">
                                <div class="media align-items-center">
                                    <div> 
                                        <img {{ $project->img_image }} class="avatar rounded-circle">
                                    </div>
                                    <div class="media-body ml-4">
                                        @if($project->is_active == 1)
                                            <a href="{{ route('projects.show',$project) }}" class="name mb-0 h6 text-sm">{{ $project->name }}</a>
                                        @else
                                            <a href="#" class="name mb-0 h6 text-sm">{{ $project->name }}</a>
                                        @endif
                                        <br>
                                        <span class="badge badge-xs badge-{{ (\Auth::user()->checkProject($project->id) == 'Owner') ? 'success' : 'warning'  }}" data-toggle="tooltip" data-placement="bottom" data-original-title="{{__('You are ') .__(ucfirst($project->permission()))}}">{{ __(\Auth::user()->checkProject($project->id)) }}</span>
                                    </div>
                                </div>
                            </th>
                            <td>
                                <span class="badge badge-dot mr-4">
                                      <i class="bg-{{\App\Models\Project::$status_color[$project->status]}}"></i>
                                      <span class="status">{{ __(\App\Models\Project::$status[$project->status]) }}</span>
                                </span>
                            </td>
                            <td>
                                <div class="avatar-group" id="project_{{ $project->id }}">
                                    @if(isset($project->users) && !empty($project->users) && count($project->users) > 0)
                                        @foreach($project->users as $key => $user)
                                            @if($key < 3)
                                                <a href="#" class="avatar rounded-circle avatar-sm">
                                                    <img {{ $user->img_avatar }} title="{{ $user->name }}">
                                                </a>
                                            @else
                                                @break
                                            @endif
                                        @endforeach
                                        @if(count($project->users) > 3)
                                            <a href="#" class="avatar rounded-circle avatar-sm">
                                                <img avatar="+ {{ count($project->users)-3 }}">
                                            </a>
                                        @endif
                                    @else
                                        {{ __('-') }}
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="completion mr-2">{{ $project->project_progress()['percentage'] }}</span>
                                    <div>
                                        <div class="progress" style="width: 100px;">
                                            <div class="progress-bar bg-{{ $project->project_progress()['color'] }}" role="progressbar" aria-valuenow="{{ $project->project_progress()['percentage'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $project->project_progress()['percentage'] }};"></div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-right w-15">
                                @if($project->is_active == 1)
                                    @if(\Auth::user()->checkProject($project->id) == 'Owner')
                                        <div class="actions">
                                            <a href="#" data-url="{{ route('invite.project.member.view', $project->id) }}" data-ajax-popup="true" data-size="lg" data-title="{{__('Invite Member')}}" class="action-item px-2" data-toggle="tooltip" data-original-title="{{__('Invite Member')}}">
                                                <i class="fas fa-paper-plane"></i>
                                            </a>
                                            <a href="{{ route('projects.edit',$project) }}" class="action-item px-2" data-toggle="tooltip" data-original-title="{{__('Edit')}}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="#" class="action-item text-danger px-2" data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?')}}|{{__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-project-{{$project->id}}').submit();">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </div>
                                        {!! Form::open(['method' => 'DELETE', 'route' => ['projects.destroy',$project->id],'id'=>'delete-project-'.$project->id]) !!}
                                        {!! Form::close() !!}
                                    @endif
                                @else
                                    <div class="actions text-center px-4">
                                        <a href="#" class="action-item" data-toggle="tooltip" data-original-title="{{__('Locked')}}">
                                            <i class="fas fa-lock"></i>
                                        </a>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <th scope="col" colspan="7"><h6 class="text-center">{{__('No Projects Found.')}}</h6></th>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
