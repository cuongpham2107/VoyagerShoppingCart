@php
    $edit = !is_null($dataTypeContent->getKey());
    $add  = is_null($dataTypeContent->getKey());
@endphp

@extends('voyager::master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('page_title', __('voyager::generic.'.($edit ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular'))

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i>
        {{ __('voyager::generic.'.($edit ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular') }}
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')
    <div class="page-content container-fluid" x-data="product_labels">
        <form role="form"
            class="form-edit-add"
            action="{{ $edit ? route('voyager.'.$dataType->slug.'.update', $dataTypeContent->getKey()) : route('voyager.'.$dataType->slug.'.store') }}"
            method="POST" enctype="multipart/form-data">    
            <!-- PUT Method if we are editing -->
            @if($edit)
                {{ method_field("PUT") }}
            @endif
            {{ csrf_field() }}

            <div class="row">
                <div class="col-md-8">
                    <!-- ### TITLE ### -->
                    <div class="panel ">
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="panel-heading">
                            <h3 class="panel-title">
                                {{ trans('product-labels.Name') }}
                            </h3>
                        </div>
                        <div class="panel-body">
                            <input type="text" class="form-control" id="title" name="name" placeholder="" value="{{ $dataTypeContent->name ?? '' }}" required> 
                        </div>
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                {{ trans('product-labels.Color') }}
                            </h3>
                        </div>
                        <div class="panel-body">
                            <input type="color" class="form-control" id="title" name="color" placeholder=" " value="{{ $dataTypeContent->color ?? '' }}">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    
                    <!-- ### DETAILS ### -->
                    <div class="panel">
                        <div class="panel-heading-botom">
                            <h3 class="panel-title"><i class="icon wb-clipboard"></i> {{ trans('product-labels.Status') }}</h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <select class="form-control" name="status">
                                    <option value="published"@if(isset($dataTypeContent->status) && $dataTypeContent->status == 'published') selected="selected"@endif>Published</option>
                                    <option value="draft"@if(isset($dataTypeContent->status) && $dataTypeContent->status == 'draft') selected="selected"@endif>Draft</option>
                                    <option value="pending"@if(isset($dataTypeContent->status) && $dataTypeContent->status == 'pending') selected="selected"@endif>Pending</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @section('submit-buttons')
                <button type="submit" class="btn btn-primary pull-right">
                     @if($edit){{ trans('product-labels.Update labels') }}@else <i class="icon wb-plus-circle"></i> {{ trans('product-labels.Create new labels') }}@endif
                </button>
            @stop
            @yield('submit-buttons')
        </form>

        <div style="display:none">
            <input type="hidden" id="upload_url" value="{{ route('voyager.upload') }}">
            <input type="hidden" id="upload_type_slug" value="{{ $dataType->slug }}">
        </div>
    </div>

    <div class="modal fade modal-danger" id="confirm_delete_modal">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><i class="voyager-warning"></i> {{ __('voyager::generic.are_you_sure') }}</h4>
                </div>

                <div class="modal-body">
                    <h4>{{ __('voyager::generic.are_you_sure_delete') }} '<span class="confirm_delete_name"></span>'</h4>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                    <button type="button" class="btn btn-danger" id="confirm_delete">{{ __('voyager::generic.delete_confirm') }}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Delete File Modal -->
    {{-- @dd() --}}
@stop

@section('javascript')

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('product_labels', () => ({
                
            }))
        })
    </script>
    <script>
        var params = {};
        var $file;

        function deleteHandler(tag, isMulti) {
          return function() {
            $file = $(this).siblings(tag);

            params = {
                slug:   '{{ $dataType->slug }}',
                filename:  $file.data('file-name'),
                id:     $file.data('id'),
                field:  $file.parent().data('field-name'),
                multi: isMulti,
                _token: '{{ csrf_token() }}'
            }

            $('.confirm_delete_name').text(params.filename);
            $('#confirm_delete_modal').modal('show');
          };
        }

        $('document').ready(function () {
            $('#slug').slugify();

            $('.toggleswitch').bootstrapToggle();

            //Init datepicker for date fields if data-datepicker attribute defined
            //or if browser does not handle date inputs
            $('.form-group input[type=date]').each(function (idx, elt) {
                if (elt.type != 'date' || elt.hasAttribute('data-datepicker')) {
                    elt.type = 'text';
                    $(elt).datetimepicker($(elt).data('datepicker'));
                }
            });

            @if ($isModelTranslatable)
                $('.side-body').multilingual({"editing": true});
            @endif

            $('.side-body input[data-slug-origin]').each(function(i, el) {
                $(el).slugify();
            });

            $('.form-group').on('click', '.remove-multi-image', deleteHandler('img', true));
            $('.form-group').on('click', '.remove-single-image', deleteHandler('img', false));
            $('.form-group').on('click', '.remove-multi-file', deleteHandler('a', true));
            $('.form-group').on('click', '.remove-single-file', deleteHandler('a', false));

            $('#confirm_delete').on('click', function(){
                $.post('{{ route('voyager.'.$dataType->slug.'.media.remove') }}', params, function (response) {
                    if ( response
                        && response.data
                        && response.data.status
                        && response.data.status == 200 ) {

                        toastr.success(response.data.message);
                        $file.parent().fadeOut(300, function() { $(this).remove(); })
                    } else {
                        toastr.error("Error removing file.");
                    }
                });

                $('#confirm_delete_modal').modal('hide');
            });
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@stop
