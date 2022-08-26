@extends('layouts.admin')

@section('title')
    {{$contract->subject }}
@endsection


@push('css')
    <link rel="stylesheet" href="{{ asset('assets/libs/summernote/summernote-bs4.css') }}">
@endpush

@push('theme-script')
    <script src="{{ asset('assets/libs/summernote/summernote-bs4.js') }}"></script>
@endpush


@section('action-button')
    <div class="float-end">

        @if (Auth::user()->type == 'owner')
            <a href="{{ route('send.mail.contract', $contract->id) }}" data-url=""
                class="btn btn-sm btn-white btn-icon-only rounded-circle ml-0" data-toggle="tooltip"
                data-original-title="{{ __('Email') }}">
                <span class="btn-inner--icon"><i class="fas fa-email"></i></span>
            </a>
        @endif

        <a href="{{ route('contract.download.pdf', \Crypt::encrypt($contract->id)) }}"
            class="btn btn-sm btn-white btn-icon-only rounded-circle ml-0 cp_link" data-toggle="tooltip"
            data-original-title="{{ __('Download') }}">
            <span class="btn-inner--icon"><i class="fas fa-download"></i></span>
        </a>


        <a href="{{ route('get.contract', $contract->id) }}" class="btn btn-sm btn-white btn-icon-only rounded-circle ml-0"
            data-url="" target="_blank" data-toggle="tooltip" data-original-title="{{ __('Preview') }}">
            <span class="btn-inner--icon"><i class="fas fa-eye"></i></span>
        </a>


        @if ((\Auth::user()->type == 'owner' && $contract->owner_signature == null) ||
            ($contract->client == Auth::user()->id && $contract->client_signature == null))
            <a href="" class="btn btn-sm btn-white btn-icon-only rounded-circle ml-0 "
                data-url="{{ route('signature', $contract->id) }}" data-ajax-popup="true"
                data-title="{{ __('Signature') }}" data-toggle="tooltip" data-size="md"
                data-original-title="{{ __('Signature') }}">
                <span class="btn-inner--icon"><i class="fas fa-sign"></i></span>
            </a>
        @endif

        {{-- <a href="{{ route('contracts') }}" class="btn btn-sm btn-white rounded-circle btn-icon-only ml-2"
            data-url="" data-toggle="tooltip" data-original-title="{{ __('Preview') }}">
            <span class="btn-inner--icon"><i class="fas fa-eye"></i></span>
        </a> --}}

        <a href="{{ route('contractclient.index', $contract->id) }}"
            class="btn btn-sm btn-white rounded-circle btn-icon-only ml-0">
            <span class="btn-inner--icon"><i class="fas fa-arrow-left"></i></span>
        </a>


    </div>
@endsection


@section('content')
    <div class="row">

        <div class="col-lg-4 order-lg-2">
            <div class="card">
                <div class="list-group list-group-flush" id="tabs">
                    <div data-href="#tabs-1" class="list-group-item text-primary">
                        <div class="media">
                            <i class="fas fa-cog pt-1"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1">{{ __('General') }}</a>
                                <p class="mb-0 text-sm">{{ __('Detail of your Contract.') }}</p>
                            </div>
                        </div>
                    </div>
                    <div data-href="#tabs-2" class="list-group-item">
                        <div class="media">
                            <i class="fas fa-money-check-alt pt-1"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1">{{ __('Attachment') }}</a>
                                <p class="mb-0 text-sm">{{ __('You can manage your attachment here') }}</p>
                            </div>
                        </div>
                    </div>

                    <div data-href="#tabs-3" class="list-group-item">
                        <div class="media">
                            <i class="fas fa-file pt-1"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1">{{ __('Comment') }}</a>
                                <p class="mb-0 text-sm">{{ __('You can manage your comment here.') }}</p>
                            </div>
                        </div>
                    </div>
                    <div data-href="#tabs-4" class="list-group-item">
                        <div class="media">
                            <i class="fas fa-percent pt-1"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1">{{ __('Notes') }}</a>
                                <p class="mb-0 text-sm">{{ __('You can manage your notes rate here.') }}</p>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>

        <div class="col-lg-8 order-lg-1">
            {{-- <div class="col-xl-12"> --}}
            <div id="tabs-1" class="tabs-card">

                <div class="row">

                    <div class="col-xl-4 col-md-6">
                        <div class="card card-stats">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h6 class="text-muted mb-1">{{ __('Total Attachment') }}</h6>
                                        <span class="h3 font-weight-bold mb-0 ">{{ count($contract->taskFiles) }}</span>
                                        <br>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6">
                        <div class="card card-stats">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h6 class="text-muted mb-1">{{ __('Total Comment') }}</h6>
                                        <span class="h3 font-weight-bold mb-0 ">{{ count($contract->comment) }}</span>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6">
                        <div class="card card-stats">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h6 class="text-muted mb-1">{{ __('Total Notes') }}</h6>
                                        <span class="h3 font-weight-bold mb-0 ">{{ count($contract->note) }}</span>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card report_card total_amount_card">

                            <div class="card-body pt-0">
                                <address class="mb-0 text-sm ml-5">
                                    <dl class="row mt-4 align-items-center">
                                        <dt class="col-sm-6 h6 text-sm">{{ __('Name') }}</dt>
                                        <dd class="col-sm-6 text-sm">
                                            {{ !empty($contract->getprojectname) ? $contract->getprojectname->name : '-' }}
                                        </dd>

                                        <dt class="col-sm-6 h6 text-sm">{{ __('Project') }}</dt>
                                        <dd class="col-sm-6 text-sm">
                                            {{ !empty($contract->clientdetail) ? $contract->clientdetail->name : '' }}
                                        </dd>

                                        <dt class="col-sm-6 h6 text-sm">{{ __('Value') }}</dt>
                                        <dd class="col-sm-6 text-sm">
                                            {{ Auth::user()->priceFormat($contract->value) }}</dd>

                                        <dt class="col-sm-6 h6 text-sm">{{ __('Contract Type') }}</dt>
                                        <dd class="col-sm-6 text-sm">
                                            {{ !empty($contract->ContractType) ? $contract->ContractType->name : '-' }}
                                        </dd>

                                        <dt class="col-sm-6 h6 text-sm">{{ __('Start Date') }}</dt>
                                        <dd class="col-sm-6 text-sm">
                                            {{ Auth::user()->dateFormat($contract->start_date) }}</dd>

                                        <dt class="col-sm-6 h6 text-sm">{{ __('End Date') }}</dt>
                                        <dd class="col-sm-6 text-sm">
                                            {{ Auth::user()->dateFormat($contract->end_date) }}</dd>
                                    </dl>
                                </address>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Description ') }}</h5>
                    </div>
                    <div class="card-body p-3">
                        {{ Form::open(['route' => ['contract.contract_description.store', $contract->id]]) }}
                        <div class="col-md-12">
                            <div class="form-group mt-3">
                                <textarea class="tox-target pc-tinymce-2 summernote-simple" name="contract_description" rows="8">{!! $contract->contract_description !!}</textarea>
                            </div>
                        </div>
                        @if ($contract->client == Auth::user()->id)
                            @if ($contract->status == 'start')
                                <div class="col-md-12 text-end">
                                    <div class="form-group mt-3 me-3 text-right">
                                        {{ Form::submit(__('Save Changes'), ['class' => 'btn  btn-primary']) }}
                                    </div>
                                </div>
                            @else
                            @endif
                        @else
                            <div class="col-md-12 text-end">
                                <div class="form-group mt-3 me-3 text-right">
                                    {{ Form::submit(__('Save Changes'), ['class' => 'btn  btn-primary']) }}
                                </div>
                            </div>
                        @endif
                        {{ Form::close() }}
                    </div>
                </div>
            </div>

            <div id="tabs-2" class="tabs-card d-none">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ __('Upload File') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class=" height-450">

                            @if ($contract->client == Auth::user()->id)
                                @if ($contract->status == 'start')
                                    <div class="row mb-4 align-items-center">
                                        <div class="col-6">
                                            <label class="form-control-label mb-0">{{ __('Attachments') }}</label>
                                        </div>
                                        <div class="col-6 text-right">
                                            <a href="#" class="btn btn-xs btn-secondary btn-icon rounded-pill"
                                                data-toggle="collapse" data-target="#add_file">
                                                <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                                <span class="btn-inner--text">{{ __('Add item') }}</span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card mb-3 border shadow-none collapse" id="add_file">
                                        <div class="card border-0 shadow-none mb-0">
                                            <div class="px-3 py-2 row align-items-center">
                                                <div class="col-10">
                                                    <input type="file" name="task_attachment" id="task_attachment"
                                                        required class="custom-input-file" />
                                                    <label for="task_attachment">
                                                        <i class="fa fa-upload"></i>
                                                        <span class="attachment_text">{{ __('Choose a file…') }}</span>
                                                    </label>
                                                </div>
                                                <div class="col-2 card-meta d-inline-flex align-items-center">
                                                    <button class="btn btn-primary btn-xs" type="submit"
                                                        id="file_submit"
                                                        data-action="{{ route('contracts.file.upload', [$contract->id]) }}">
                                                        <i class="fas fa-plus "></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                @endif
                            @else
                                <div class="row mb-4 align-items-center">
                                    <div class="col-6">
                                        <label class="form-control-label mb-0">{{ __('Attachments') }}</label>
                                    </div>
                                    <div class="col-6 text-right">
                                        <a href="#" class="btn btn-xs btn-secondary btn-icon rounded-pill"
                                            data-toggle="collapse" data-target="#add_file">
                                            <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                            <span class="btn-inner--text">{{ __('Add item') }}</span>
                                        </a>
                                    </div>
                                </div>
                                <div class="card mb-3 border shadow-none collapse" id="add_file">
                                    <div class="card border-0 shadow-none mb-0">
                                        <div class="px-3 py-2 row align-items-center">
                                            <div class="col-10">
                                                <input type="file" name="task_attachment" id="task_attachment"
                                                    required class="custom-input-file" />
                                                <label for="task_attachment">
                                                    <i class="fa fa-upload"></i>
                                                    <span class="attachment_text">{{ __('Choose a file…') }}</span>
                                                </label>
                                            </div>
                                            <div class="col-2 card-meta d-inline-flex align-items-center">
                                                <button class="btn btn-primary btn-xs" type="submit" id="file_submit"
                                                    data-action="{{ route('contracts.file.upload', [$contract->id]) }}">
                                                    <i class="fas fa-plus "></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif


                            <div id="comments-file">
                                @foreach ($contract->taskFiles as $file)
                                    <div class="card mb-3 border shadow-none task-file">
                                        <div class="px-3 py-3">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <img src="{{ asset('assets/img/icons/files/' . $file->extension . '.png') }}"
                                                        class="img-fluid" style="width: 40px;">
                                                </div>
                                                <div class="col ml-n2">
                                                    <h6 class="text-sm mb-0">
                                                        <a href="#" target="">{{ $file->name }} </a>
                                                    </h6>
                                                    <p class="card-text small text-muted">{{ $file->file_size }}
                                                    </p>
                                                </div>
                                                <div class="col-auto actions">

                                                    <a href="{{ asset(Storage::url('contract_attechment/' . $file->files)) }}"
                                                        target="_blank" role="button">
                                                        <i class="fas fa-eye "></i>
                                                    </a>

                                                    <a href="{{ asset(Storage::url('contract_attechment/' . $file->files)) }}"
                                                        download class="action-item" role="button">
                                                        <i class="fas fa-download"></i>
                                                    </a>

                                                    @if ($contract->client == Auth::user()->id)
                                                        @if (Auth::user()->id == $file->created_by)
                                                            @auth('web')
                                                                <a href="#" class="action-item text-danger px-2"
                                                                    data-toggle="tooltip"
                                                                    data-original-title="{{ __('Delete') }}"
                                                                    data-confirm="{{ __('Are You Sure?') }}|{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                    data-confirm-yes="document.getElementById('delete-Attachments-{{ $file->id }}').submit();">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </a>

                                                                {!! Form::open([
                                                                    'method' => 'GET',
                                                                    'route' => ['contracts.file.delete', $file->id],
                                                                    'id' => 'delete-Attachments-' . $file->id,
                                                                ]) !!}
                                                                {!! Form::close() !!}
                                                            @endauth
                                                        @else
                                                        @endif
                                                    @else
                                                        @auth('web')
                                                            <a href="#" class="action-item text-danger px-2"
                                                                data-toggle="tooltip"
                                                                data-original-title="{{ __('Delete') }}"
                                                                data-confirm="{{ __('Are You Sure?') }}|{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                data-confirm-yes="document.getElementById('delete-Attachments-{{ $file->id }}').submit();">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </a>

                                                            {!! Form::open([
                                                                'method' => 'GET',
                                                                'route' => ['contracts.file.delete', $file->id],
                                                                'id' => 'delete-Attachments-' . $file->id,
                                                            ]) !!}
                                                            {!! Form::close() !!}
                                                        @endauth
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>



                        </div>
                    </div>
                </div>
            </div>

            <div id="tabs-3" class="tabs-card d-none">
                <div class="row pt-2">
                    <div class="col-12">
                        <div id="comment">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('Comments') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="col-12">


                                        @if ($contract->client == Auth::user()->id)
                                            @if ($contract->status == 'start')
                                                <div class="form-group mb-0 form-send w-100 text-right ">
                                                    <form method="POST" class=" pills-comments-form comment-box"
                                                        id="form-comment"
                                                        action="{{ route('comment_store.store', [$contract->id]) }}">
                                                        @csrf

                                                        <button type="submit" id="comment_submit"
                                                            class="btn btn-send btn btn-primary mr-0 commanent-buttion"><i
                                                                class="fas fa-plus"></i>
                                                        </button>


                                                        <textarea rows="1" class="form-control comments-shadow" name="comment" data-toggle="autosize"
                                                            placeholder="Add a comment..." spellcheck="false"></textarea>

                                                        <grammarly-extension data-grammarly-shadow-root="true"
                                                            style="position: absolute; top: 0px; left: 0px; pointer-events: none; z-index: 1;"
                                                            class="cGcvT"></grammarly-extension>

                                                        <grammarly-extension data-grammarly-shadow-root="true"
                                                            style="mix-blend-mode: darken; position: absolute; top: 0px; left: 0px; pointer-events: none; z-index: 1;"
                                                            class="cGcvT"></grammarly-extension>

                                                    </form>
                                                </div>
                                            @else
                                            @endif
                                        @else
                                            <div class="form-group mb-0 form-send w-100 text-right ">
                                                <form method="POST" class=" pills-comments-form comment-box"
                                                    id="form-comment"
                                                    action="{{ route('comment_store.store', [$contract->id]) }}">
                                                    @csrf

                                                    <button type="submit" id="comment_submit"
                                                        class="btn btn-send btn btn-primary mr-0 commanent-buttion"><i
                                                            class="fas fa-plus"></i>
                                                    </button>


                                                    <textarea rows="1" class="form-control comments-shadow" name="comment" data-toggle="autosize"
                                                        placeholder="Add a comment..." spellcheck="false"></textarea>

                                                    <grammarly-extension data-grammarly-shadow-root="true"
                                                        style="position: absolute; top: 0px; left: 0px; pointer-events: none; z-index: 1;"
                                                        class="cGcvT"></grammarly-extension>

                                                    <grammarly-extension data-grammarly-shadow-root="true"
                                                        style="mix-blend-mode: darken; position: absolute; top: 0px; left: 0px; pointer-events: none; z-index: 1;"
                                                        class="cGcvT"></grammarly-extension>

                                                </form>
                                            </div>
                                        @endif


                                    </div>
                                    <div class="">
                                        <div class="list-group list-group-flush mt-3" id="comments">
                                            @foreach ($contract->comment as $comment)
                                                <div class="list-group-item px-0">
                                                    <div class="row align-items-center">
                                                        <div class="col-auto">

                                                            {{-- @dd($comment); --}}
                                                            @php
                                                                $user = App\Models\user::find($comment->user_id);
                                                            @endphp



                                                            <a href="#"
                                                                class="img-fluid rounded-circle card-avatar">
                                                                <a href="{{ asset('/storage/' . $user->avatar) }}"
                                                                    target="_blank"
                                                                    class="avatar avatar-sm rounded-circle">
                                                                    <img src="{{ !empty($user->avatar) ? asset('/storage/' . $user->avatar) : asset(Storage::url('avatars/avatar.png')) }}"
                                                                        alt="">
                                                                </a>
                                                            </a>
                                                        </div>
                                                        <div class="col ml-n2">
                                                            <p
                                                                class="d-block h6 text-sm font-weight-light mb-0 text-break">
                                                                {{ $comment->comment }}</p>
                                                        </div>
                                                        @if ($contract->client == Auth::user()->id)
                                                            @if (Auth::user()->id == $comment->created_by)
                                                                <div class="col-auto">
                                                                    <a href="#" class="action-item text-danger px-2"
                                                                        data-toggle="tooltip"
                                                                        data-original-title="{{ __('Delete') }}"
                                                                        data-confirm="{{ __('Are You Sure?') }}|{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                        data-confirm-yes="document.getElementById('delete-comment-{{ $comment->id }}').submit();">
                                                                        <i class="fas fa-trash-alt"></i>
                                                                    </a>


                                                                    {!! Form::open([
                                                                        'method' => 'GET',
                                                                        'route' => ['comment_store.destroy', $comment->id],
                                                                        'id' => 'delete-comment-' . $comment->id,
                                                                    ]) !!}
                                                                    {!! Form::close() !!}
                                                                </div>
                                                            @else
                                                            @endif
                                                        @else
                                                            <div class="col-auto">
                                                                <a href="#" class="action-item text-danger px-2"
                                                                    data-toggle="tooltip"
                                                                    data-original-title="{{ __('Delete') }}"
                                                                    data-confirm="{{ __('Are You Sure?') }}|{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                    data-confirm-yes="document.getElementById('delete-comment-{{ $comment->id }}').submit();">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </a>

                                                                {!! Form::open([
                                                                    'method' => 'GET',
                                                                    'route' => ['comment_store.destroy', $comment->id],
                                                                    'id' => 'delete-comment-' . $comment->id,
                                                                ]) !!}
                                                                {!! Form::close() !!}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="tabs-4" class="tabs-card d-none">
                <div class="row pt-2">
                    <div class="col-12">
                        <div id="notes">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('Notes') }}</h5>
                                </div>
                                <div class="card-body">

                                    {{-- {{ dd(Auth::user()->type) }} --}}
                                    {{-- @if (Auth::user()->user_contacts->role == 'client' && $contract->status == 'on') --}}




                                    @if ($contract->client == Auth::user()->id)
                                        @if ($contract->status == 'start')
                                            {{ Form::open(['route' => ['note_store.store', $contract->id]]) }}
                                            <div class="form-group mb-1">
                                                <textarea class="tox-target pc-tinymce form-control " placeholder="Add a notes..." style="width:100%; height:114px;"
                                                    name="note" id="summernote"></textarea>
                                            </div>

                                            <div class="row d-flex ">
                                                <div class="col-md-12 notes text-right mt-3">
                                                    {{ Form::submit(__('Add'), ['class' => 'btn  btn-primary ']) }}
                                                </div>
                                            </div>
                                            {{ Form::close() }}
                                        @else
                                            {{ Form::open(['route' => ['note_store.store', $contract->id]]) }}

                                            {{ Form::close() }}
                                        @endif
                                    @else
                                        {{ Form::open(['route' => ['note_store.store', $contract->id]]) }}
                                        <div class="form-group mb-1">
                                            <textarea class="tox-target pc-tinymce form-control " placeholder="Add a notes..." style="width:100%; height:114px;"
                                                name="note" id="summernote"></textarea>
                                        </div>

                                        <div class="row d-flex ">
                                            <div class="col-md-12 notes text-right mt-3">
                                                {{ Form::submit(__('Add'), ['class' => 'btn  btn-primary ']) }}
                                            </div>
                                        </div>
                                        {{ Form::close() }}
                                    @endif






                                    <div class="">
                                        <div class="list-group list-group-flush mt-3" id="comments">
                                            {{-- {{ dd($contract->notes) }} --}}
                                            @foreach ($contract->note as $note)
                                                <div class="list-group-item ">
                                                    <div class="row align-items-center">

                                                        @php
                                                            $user = App\Models\user::find($note->user_id);
                                                        @endphp

                                                        <div class="col-auto">
                                                            <a href="{{ asset('/storage/' . $user->avatar) }}"
                                                                target="_blank" class="avatar avatar-sm rounded-circle">
                                                                <img src="{{ !empty($user->avatar) ? asset('/storage/' . $user->avatar) : asset(Storage::url('avatars/avatar.png')) }}"
                                                                    alt="">
                                                            </a>
                                                        </div>
                                                        <div class="col ml-n2">
                                                            <p
                                                                class="d-block h6 text-sm font-weight-light mb-0 text-break">
                                                                {{ $note->note }}</p>
                                                            <small
                                                                class="d-block">{{ $note->created_at->diffForHumans() }}</small>
                                                        </div>


                                                        @if ($contract->client == Auth::user()->id)
                                                            @if (Auth::user()->id == $note->created_by)
                                                                <a href="#" class="action-item text-danger px-2"
                                                                    data-toggle="tooltip"
                                                                    data-original-title="{{ __('Delete') }}"
                                                                    data-confirm="{{ __('Are You Sure?') }}|{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                    data-confirm-yes="document.getElementById('delete-note-{{ $note->id }}').submit();">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </a>

                                                                {!! Form::open([
                                                                    'method' => 'GET',
                                                                    'route' => ['note_store.destroy', $note->id],
                                                                    'id' => 'delete-note-' . $note->id,
                                                                ]) !!}
                                                                {!! Form::close() !!}
                                                            @endif
                                                        @else
                                                            <a href="#" class="action-item text-danger px-2"
                                                                data-toggle="tooltip"
                                                                data-original-title="{{ __('Delete') }}"
                                                                data-confirm="{{ __('Are You Sure?') }}|{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                data-confirm-yes="document.getElementById('delete-note-{{ $note->id }}').submit();">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </a>

                                                            {!! Form::open([
                                                                'method' => 'GET',
                                                                'route' => ['note_store.destroy', $note->id],
                                                                'id' => 'delete-note-' . $note->id,
                                                            ]) !!}
                                                            {!! Form::close() !!}
                                                        @endif


                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- </div> --}}
        </div>

    </div>
@endsection


@push('script')
    <script src="{{ asset('css/summernote/summernote-bs4.js') }}"></script>


    <script>
        /*For Task Attachment*/
        $(document).on('click', '#file_submit', function() {

            var file_data = $("#task_attachment").prop("files")[0];
            console.log(file_data);
            if (file_data != '' && file_data != undefined) {
                var formData = new FormData();
                formData.append('file', file_data);
                // console.log(formData);

                $.ajax({
                    url: $("#file_submit").data('action'),
                    type: 'POST',
                    data: formData,
                    cache: false,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        $('#task_attachment').val('');
                        $('.attachment_text').html('{{ __('Choose a file…') }}');

                        // console.log(data);
                        if (data.status == 'success') {
                            show_toastr('Success', data.msg, 'success');
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        }
                        // show_toastr('{{ __('Success') }}', '{{ __('File Added Successfully!') }}',
                        //     'Success');

                        // var delLink = '';
                        // if (data.deleteUrl.length > 0) {
                        //     delLink =
                        //         '<a href="#" class="action-item delete-comment-file" role="button" data-url="' +
                        //         data.deleteUrl + '">' +
                        //         '                                        <i class="fas fa-trash"></i>' +
                        //         '                                    </a>';
                        // }

                        // var html = '<div class="card mb-3 border shadow-none task-file">' +
                        //     '                    <div class="px-3 py-3">' +
                        //     '                        <div class="row align-items-center">' +
                        //     '                            <div class="col-auto">' +
                        //     '                                <img src="{{ asset('assets/img/icons/files') }}/' +
                        //     data.extension + '.png" class="img-fluid" style="width: 40px;">' +
                        //     '                            </div>' +
                        //     '                            <div class="col ml-n2">' +
                        //     '                                <h6 class="text-sm mb-0">' +
                        //     '                                    <a href="#">' + data.name + '</a>' +
                        //     '                                </h6>' +
                        //     '                                <p class="card-text small text-muted">' +
                        //     data.file_size + '</p>' +
                        //     '                            </div>' +
                        //     '                            <div class="col-auto actions">' +
                        //     '                                <a href="{{ asset(Storage::url('tasks')) }}/' +
                        //     data.file + '" download class="action-item" role="button">' +
                        //     '                                    <i class="fas fa-download"></i>' +
                        //     '                                </a>' +
                        //     delLink +
                        //     '                            </div>' +
                        //     '                        </div>' +
                        //     '                    </div>' +
                        //     '                </div>'

                        // $("#comments-file").prepend(html);
                    },
                    error: function(data) {
                        data = data.responseJSON;
                        if (data.message) {
                            show_toastr('{{ __('Error') }}', data.errors.file[0], 'error');
                            $('#file-error').text(data.errors.file[0]).show();
                        } else {
                            show_toastr('{{ __('Error') }}', '{{ __('Some Thing Is Wrong!') }}',
                                'error');
                        }
                    }
                });
            } else {
                show_toastr('{{ __('Error') }}', '{{ __('Please select file!') }}', 'error');
            }
        });


        // $(document).on('click', '#comment_submit', function(e) 
        // {

        //     var curr = $(this);

        //     var comment = $.trim($("#form-comment textarea[name='comment']").val());

        //     if (comment != '') {
        //         $.ajax({
        //             url: $("#form-comment").data('action'),
        //             data: {
        //                 comment: comment,
        //                 "_token": "{{ csrf_token() }}",
        //             },
        //             type: 'POST',
        //             success: function(data) {

        //                 data = JSON.parse(data);

        //                 var html = "<div class='list-group-item px-0'>" +
        //                     "                    <div class='row align-items-center'>" +
        //                     "                        <div class='col-auto'>" +
        //                     "                            <a href='#' class='avatar avatar-sm rounded-circle ms-2'>" +
        //                     "                                <img src=" + data.default_img +
        //                     " alt='' class='avatar-sm rounded-circle'>" +
        //                     "                            </a>" +
        //                     "                        </div>" +
        //                     "                        <div class='col ml-n2'>" +
        //                     "                            <p class='d-block h6 text-sm font-weight-light mb-0 text-break'>" +
        //                     data.comment + "</p>" +
        //                     "                            <small class='d-block'>" + data.current_time +
        //                     "</small>" +
        //                     "                        </div>" +
        //                     "                        <div class='action-btn bg-danger me-4'><div class='col-auto'><a href='#' class='mx-3 btn btn-sm  align-items-center delete-comment' data-url='" +
        //                     data.deleteUrl +
        //                     "'><i class='ti ti-trash text-white'></i></a></div></div>" +
        //                     "                    </div>" +
        //                     "                </div>";

        //                 $("#comments").prepend(html);
        //                 $("#form-comment textarea[name='comment']").val('');
        //                 load_task(curr.closest('.task-id').attr('id'));

        //                 if(data.status=='success')
        //                 {
        //                 show_toastr('success', 'Comment Added Successfully!');
        //                 }
        //             },
        //             error: function(data) {
        //                 show_toastr('error', 'Some Thing Is Wrong!');
        //             }
        //         });
        //     } else {
        //         show_toastr('error', 'Please write comment!');
        //     }
        // });

        $(document).on("click", ".delete-comment", function() {
            var btn = $(this);
            $.ajax({
                url: $(this).attr('data-url'),
                type: 'DELETE',
                dataType: 'JSON',
                data: {
                    comment: comment,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    load_task(btn.closest('.task-id').attr('id'));
                    show_toastr('success', 'Comment Deleted Successfully!');
                    btn.closest('.list-group-item').remove();
                },
                error: function(data) {
                    data = data.responseJSON;
                    if (data.message) {
                        show_toastr('error', data.message);
                    } else {
                        show_toastr('error', 'Some Thing Is Wrong!');
                    }
                }
            });
        });
    </script>



    <script src="{{ asset('assets/libs/signature_pad.min.js') }}"></script>

    <script>
        var signature = {
            canvas: null,
            clearButton: null,

            init: function init() {

                this.canvas = document.querySelector(".signature-pad");
                this.clearButton = document.getElementById('clearSig');
                this.saveButton = document.getElementById('addSig');
                signaturePad = new SignaturePad(this.canvas);


                this.clearButton.addEventListener('click', function(event) {

                    signaturePad.clear();
                });

                this.saveButton.addEventListener('click', function(event) {
                    var data = signaturePad.toDataURL('image/png');
                    $('#SignupImage1').val(data);



                    $.ajax({
                        url: '{{ route('signaturestore') }}',
                        type: 'POST',
                        data: $("form").serialize(),
                        success: function(data) {
                            show_toastr('success', data.message);
                            $('#commonModal').modal('hide');
                        },
                        error: function(data) {

                        }
                    });

                });

            }
        };

        signature.init();
    </script>


    <script>
        // For Sidebar Tabs
        $(document).ready(function() {
            $('#tabs .list-group-item').on('click', function() {
                var href = $(this).attr('data-href');
                $('.tabs-card').addClass('d-none');
                $(href).removeClass('d-none');
                $('#tabs .list-group-item').removeClass('text-primary');
                $(this).addClass('text-primary');
            });
        });

        $(document).on('click', '.custom-control-input', function() {
            var pp = $(this).parents('.tabs-card').removeClass('d-none');
        });
    </script>
@endpush
