<form class="pl-3 pr-3" method="post" action="{{ route('plans.store') }}">
    @csrf
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="form-group">
                <label class="form-control-label" for="name">{{ __('Name') }}</label>
                <input type="text" class="form-control" id="name" name="name" required/>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="form-group">
                <div class="custom-control custom-switch mt-5 ml-5">
                    <input type="checkbox" class="custom-control-input" name="status" id="status" value="1">
                    <label class="custom-control-label form-control-label" for="status">{{__('Status')}}</label>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="form-group">
                <label class="form-control-label" for="monthly_price">{{ __('Monthly Price') }}</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">{{(env('CURRENCY') ? env('CURRENCY') : '$')}}</span>
                    </div>
                    <input type="number" min="0" class="form-control" id="monthly_price" name="monthly_price" required/>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="form-group">
                <label class="form-control-label" for="annual_price">{{ __('Annual Price') }}</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">{{(env('CURRENCY') ? env('CURRENCY') : '$')}}</span>
                    </div>
                    <input type="number" min="0" class="form-control" id="annual_price" name="annual_price" required/>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
            <div class="form-group">
                <label class="form-control-label" for="trial_days">{{ __('Trial Days') }}</label>
                <input type="number" class="form-control" id="trial_days" name="trial_days" required/>
                <span><small>{{__('Note: "-1" for Unlimited')}}</small></span>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
            <div class="form-group">
                <label class="form-control-label" for="max_users">{{ __('Maximum Users') }}</label>
                <input type="number" class="form-control" id="max_users" name="max_users" required/>
                <span><small>{{__('Note: "-1" for Unlimited')}}</small></span>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
            <div class="form-group">
                <label class="form-control-label" for="max_projects">{{ __('Maximum Projects') }}</label>
                <input type="number" class="form-control" id="max_projects" name="max_projects" required/>
                <span><small>{{__('Note: "-1" for Unlimited')}}</small></span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="form-group">
                <label class="form-control-label" for="description">{{ __('Description') }}</label>
                <textarea class="form-control" data-toggle="autosize" rows="3" id="description" name="description"></textarea>
            </div>
        </div>
    </div>
    <div class="text-right">
        <button class="btn btn-sm btn-primary rounded-pill" type="submit">{{ __('Create Plan') }}</button>
    </div>
</form>
