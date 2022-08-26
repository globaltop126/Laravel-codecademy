<div class="scrollbar-inner">
    <div class="min-h-300 mh-300">
        <div class="container">
            @if($user_data['due_task']->count() > 0)
                @foreach($user_data['due_task'] as $due_task)
                    <div class="row mb-3">
                        <div class="col-9">
                            <div class="progress-wrapper">
                                <span class="progress-label text-muted text-sm" data-toggle="tooltip" data-original-title="{{$due_task->project->name}}"><a href="{{ route('projects.tasks.index',$due_task->project->id) }}" class="text-muted">{{ $due_task->name }}</a></span>
                                <div class="progress mt-1 mb-2 height-5">
                                    <div class="progress-bar bg-{{ $due_task->taskProgress()['color'] }}" role="progressbar" aria-valuenow="{{ $due_task->taskProgress()['percentage'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $due_task->taskProgress()['percentage'] }};"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3 align-self-end text-right">
                            <span class="h6 mb-0">{{ $due_task->taskProgress()['percentage'] }}</span>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="py-5">
                    <h6 class="h6 text-center">{{__('No Tasks Found.')}}</h6>
                </div>
            @endif
        </div>
    </div>
</div>
