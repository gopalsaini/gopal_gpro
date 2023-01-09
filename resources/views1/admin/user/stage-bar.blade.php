<div class="col-sm-12 mb-3">
    <div class="btn-group stagebar">
        <a href="{{ route('admin.user.list.stage.all', [$type]) }}" class="btn btn-outline-primary @if ($stageno == 'all') active @endif">@lang('admin.all')</a>
        @if ($setting && $setting->StageSetting->stage_zero)
        <a href="{{ route('admin.user.list.stage.zero', [$type]) }}" class="btn btn-outline-primary @if ($stageno == '0') active @endif">@lang('admin.stage') 0</a>
        @endif
        @if ($setting && $setting->StageSetting->stage_one)
        <a href="{{ route('admin.user.list.stage.one', [$type]) }}" class="btn btn-outline-primary @if ($stageno == '1') active @endif">@lang('admin.stage') 1</a>
        @endif
        @if ($setting && $setting->StageSetting->stage_two)
        <a href="{{ route('admin.user.list.stage.two', [$type]) }}" class="btn btn-outline-primary @if ($stageno == '2') active @endif">@lang('admin.stage') 2</a>
        @endif
        @if ($setting && $setting->StageSetting->stage_three)
        <a href="{{ route('admin.user.list.stage.three', [$type]) }}" class="btn btn-outline-primary @if ($stageno == '3') active @endif">@lang('admin.stage') 3</a>
        @endif
        @if ($setting && $setting->StageSetting->stage_four)
        <a href="{{ route('admin.user.list.stage.four', [$type]) }}" class="btn btn-outline-primary @if ($stageno == '4') active @endif">@lang('admin.stage') 4</a>
        @endif
        @if ($setting && $setting->StageSetting->stage_five)
        <a href="{{ route('admin.user.list.stage.five', [$type]) }}" class="btn btn-outline-primary @if ($stageno == '5') active @endif">@lang('admin.stage') 5</a>
        @endif
    </div>
</div>