<div class="scrollbar-inner">
    <div class="min-h-500 mh-500">
        <div class="list-group list-group-flush">
            @if(count($user_data['projects']) > 0)
                @foreach($user_data['projects'] as $project)
                    <a href="{{ route('projects.show',$project) }}" class="list-group-item list-group-item-action">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <img {{ $project->img_image }} class="avatar rounded-circle"/>
                            </div>
                            <div class="flex-fill pl-3 text-limit">
                                <div class="row">
                                    <div class="col-9">
                                        <h6 class="progress-text mb-1 text-sm d-block text-limit">{{ $project->name }}</h6>
                                    </div>
                                    <div class="col-3 text-right">
                                        <span class="badge badge-xs badge-{{ (Auth::user()->checkProject($project->id) == 'Owner') ? 'success' : 'warning'  }}">{{ __(\Auth::user()->checkProject($project->id)) }}</span>
                                    </div>
                                </div>
                                <div class="progress progress-xs mb-0">
                                    <div class="progress-bar bg-{{ $project->project_progress()['color'] }}" role="progressbar" style="width: {{ $project->project_progress()['percentage'] }};" aria-valuenow="{{ $project->project_progress()['percentage'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="d-flex justify-content-between text-xs text-muted text-right mt-1">
                                    <div>
                                        <span class="font-weight-bold text-{{ \App\Models\Project::$status_color[$project->status] }}">{{__(\App\Models\Project::$status[$project->status])}}</span>
                                    </div>
                                    <div>
                                        {{ $project->countTask($user->id) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            @else
                <div class="py-5">
                    <h6 class="h6 text-center">{{__('No Projects Found.')}}</h6>
                </div>
            @endif
        </div>
    </div>
</div>
