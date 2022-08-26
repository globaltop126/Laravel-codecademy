@foreach($project->users as $user)
    <tr>
        <th scope="row">
            <div class="media align-items-center">
                <div>
                    <img {{ $user->img_avatar }} class="avatar rounded-circle avatar-sm">
                </div>
                <div class="media-body ml-3">
                    <a class="name mb-0 h6 text-sm">{{ $user->name }}</a>
                    @if(\Auth::user()->checkProject($project->id) == 'Owner' && Auth::user()->id != $user->id)
                        <span class="badge badge-xs badge-{{($user->pivot->permission == 'user' ? 'primary' : 'warning')}}">{{__(ucfirst($user->pivot->permission))}}</span>
                    @endif
                    <br>
                    <a class="text-sm text-muted">{{ $user->email }}</a>
                </div>
            </div>
        </th>
        @if(\Auth::user()->checkProject($project->id) == 'Owner' && Auth::user()->id != $user->id)
            <td>
                <span class="text-sm ml-3 text-primary" data-toggle="tooltip" data-original-title="{{__('Permission')}}" data-url="{{ route('projects.user.permission',[$project->id,$user->id]) }}" data-ajax-popup="true" data-size="lg" data-title="{{__('Edit Permission')}}">
                    <i class="fas fa-lock"></i>
                </span>
                <span class="text-sm ml-3 text-danger" data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?')}}|{{__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('remove-project-user-{{$user->pivot->id}}').submit();">
                    <i class="fas fa-trash-alt"></i>
                </span>
                {!! Form::open(['method' => 'PATCH', 'route' => ['remove.user.from.project', $project->id, $user->id],'id' => 'remove-project-user-'.$user->pivot->id]) !!}
                {!! Form::close() !!}
            </td>
        @else
            <td></td>
        @endif
    </tr>
@endforeach
