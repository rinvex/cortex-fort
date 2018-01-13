{{-- Master Layout --}}
@extends('cortex/foundation::adminarea.layouts.default')

{{-- Page Title --}}
@section('title')
    {{ config('app.name') }} » {{ trans('cortex/foundation::common.adminarea') }} » {{ trans('cortex/fort::common.abilities') }} » {{ $ability->exists ? $ability->name : trans('cortex/fort::common.create_ability') }}
@stop

@push('inline-scripts')
    {!! JsValidator::formRequest(Cortex\Fort\Http\Requests\Adminarea\AbilityFormRequest::class)->selector("#adminarea-abilities-create-form, #adminarea-abilities-{$ability->getKey()}-update-form") !!}
@endpush

{{-- Main Content --}}
@section('content')

    @if($ability->exists)
        @include('cortex/foundation::common.partials.confirm-deletion', ['type' => 'ability'])
    @endif

    <div class="content-wrapper">
        <section class="content-header">
            <h1>{{ Breadcrumbs::render() }}</h1>
        </section>

        <!-- Main content -->
        <section class="content">

            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#details-tab" data-toggle="tab">{{ trans('cortex/fort::common.details') }}</a></li>
                    @if($ability->exists) <li><a href="#logs-tab" data-toggle="tab">{{ trans('cortex/fort::common.logs') }}</a></li> @endif
                    @if($ability->exists && $currentUser->can('delete-abilities', $ability)) <li class="pull-right"><a href="#" data-toggle="modal" data-target="#delete-confirmation" data-item-href="{{ route('adminarea.abilities.delete', ['ability' => $ability]) }}" data-item-name="{{ $ability->slug }}"><i class="fa fa-trash text-danger"></i></a></li> @endif
                </ul>

                <div class="tab-content">

                    <div class="tab-pane active" id="details-tab">

                        @if ($ability->exists)
                            {{ Form::model($ability, ['url' => route('adminarea.abilities.update', ['ability' => $ability]), 'method' => 'put', 'id' => "adminarea-abilities-{$ability->getKey()}-update-form"]) }}
                        @else
                            {{ Form::model($ability, ['url' => route('adminarea.abilities.store'), 'id' => 'adminarea-abilities-create-form']) }}
                        @endif

                            <div class="row">
                                <div class="col-md-6">

                                    {{-- Name --}}
                                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                        {{ Form::label('name', trans('cortex/fort::common.name'), ['class' => 'control-label']) }}
                                        {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => trans('cortex/fort::common.name'), 'required' => 'required', 'autofocus' => 'autofocus']) }}

                                        @if ($errors->has('name'))
                                            <span class="help-block">{{ $errors->first('name') }}</span>
                                        @endif
                                    </div>

                                </div>
                                <div class="col-md-6">

                                    {{-- Policy --}}
                                    <div class="form-group{{ $errors->has('policy') ? ' has-error' : '' }}">
                                        {{ Form::label('policy', trans('cortex/fort::common.policy'), ['class' => 'control-label']) }}
                                        {{ Form::text('policy', null, ['class' => 'form-control', 'placeholder' => trans('cortex/fort::common.policy')]) }}

                                        @if ($errors->has('policy'))
                                            <span class="help-block">{{ $errors->first('policy') }}</span>
                                        @endif
                                    </div>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">

                                    {{-- Action --}}
                                    <div class="form-group{{ $errors->has('action') ? ' has-error' : '' }}">
                                        {{ Form::label('action', trans('cortex/fort::common.action'), ['class' => 'control-label']) }}
                                        {{ Form::text('action', null, ['class' => 'form-control', 'placeholder' => trans('cortex/fort::common.action'), 'required' => 'required']) }}

                                        @if ($errors->has('action'))
                                            <span class="help-block">{{ $errors->first('action') }}</span>
                                        @endif
                                    </div>

                                </div>
                                <div class="col-md-6">

                                    {{-- Resource --}}
                                    <div class="form-group{{ $errors->has('resource') ? ' has-error' : '' }}">
                                        {{ Form::label('resource', trans('cortex/fort::common.resource'), ['class' => 'control-label']) }}
                                        {{ Form::text('resource', null, ['class' => 'form-control', 'placeholder' => trans('cortex/fort::common.resource'), 'required' => 'required']) }}

                                        @if ($errors->has('resource'))
                                            <span class="help-block">{{ $errors->first('resource') }}</span>
                                        @endif
                                    </div>

                                </div>
                            </div>

                            <div class="row">

                                @can('grant-abilities')
                                    <div class="col-md-12">

                                        {{-- Roles --}}
                                        <div class="form-group{{ $errors->has('roles') ? ' has-error' : '' }}">
                                            {{ Form::label('roles[]', trans('cortex/fort::common.roles'), ['class' => 'control-label']) }}
                                            {{ Form::hidden('roles', '') }}
                                            {{ Form::select('roles[]', $roles, null, ['class' => 'form-control select2', 'placeholder' => trans('cortex/fort::common.select_roles'), 'multiple' => 'multiple', 'data-close-on-select' => 'false', 'data-width' => '100%']) }}

                                            @if ($errors->has('roles'))
                                                <span class="help-block">{{ $errors->first('roles') }}</span>
                                            @endif
                                        </div>

                                    </div>
                                @endcan

                            </div>

                            <div class="row">
                                <div class="col-md-12">

                                    {{-- Description --}}
                                    <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                        {{ Form::label('description', trans('cortex/fort::common.description'), ['class' => 'control-label']) }}
                                        {{ Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => trans('cortex/fort::common.description'), 'rows' => 3]) }}

                                        @if ($errors->has('description'))
                                            <span class="help-block">{{ $errors->first('description') }}</span>
                                        @endif
                                    </div>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">

                                    <div class="pull-right">
                                        {{ Form::button(trans('cortex/fort::common.submit'), ['class' => 'btn btn-primary btn-flat', 'type' => 'submit']) }}
                                    </div>

                                    @include('cortex/foundation::adminarea.partials.timestamps', ['model' => $ability])

                                </div>
                            </div>

                        {{ Form::close() }}

                    </div>

                    @if($ability->exists)

                        <div class="tab-pane" id="logs-tab">
                            {!! $logs->table(['class' => 'table table-striped table-hover responsive dataTableBuilder', 'id' => "adminarea-abilities-{$ability->getKey()}-logs-table"]) !!}
                        </div>

                    @endif

                </div>

            </div>

        </section>

    </div>

@endsection

@if($ability->exists)

    @push('styles')
        <link href="{{ mix('css/datatables.css', 'assets') }}" rel="stylesheet">
    @endpush

    @push('vendor-scripts')
        <script src="{{ mix('js/datatables.js', 'assets') }}" defer></script>
    @endpush

    @push('inline-scripts')
        {!! $logs->scripts() !!}
    @endpush

@endif
