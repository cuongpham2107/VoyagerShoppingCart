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
    <div class="page-content container-fluid" x-data="product_options">
        {{-- <form class="form-edit-add" role="form" action="@if($edit){{ route('voyager.product_options.update', $dataTypeContent->id) }}@else{{ route('voyager.product_options.store') }}@endif" method="POST" enctype="multipart/form-data"> --}}
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
                                {{ trans('product-options.Name') }}
                            </h3>
                        </div>
                        <div class="panel-body">
                            <input type="text" class="form-control" id="title" name="name" placeholder=" {{ trans('product-options.Placeholder Name') }} " value="{{ $dataTypeContent->name ?? '' }}">
                        </div>
                    </div>

                    <!-- ### CONTENT ### -->
                    <div class="panel panel panel-bordered panel-primary">
                        {{-- <div class="panel-heading">
                            <h3 class="panel-title">{{ trans('product-options.Option value') }}</h3>
                        </div> --}}
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="icon wb-clipboard"></i> {{ trans('product-options.Option value') }}</h3>
                        </div>
                        <div class="panel-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                      <th scope="col">#</th>
                                      <th scope="col">{{ trans('product-options.Label') }}</th>
                                      <th scope="col">{{ trans('product-options.Price') }}</th>
                                      <th scope="col">{{ trans('product-options.Price Type') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(row, index) in rows" :key="index">
                                    <tr>
                                        <th class="text-center" style="padding-top: 15px;">
                                            <span x-text="index+1"></span>
                                        </th>
                                        <td>
                                            <input x-model="row.label" class="input-table-product-attribute" type="text"  id="" placeholder="{{ trans('product-options.Placeholder Label') }}">
                                        </td>
                                        <td>
                                            <input x-model="row.price" class="input-table-product-attribute" type="number"  id="" placeholder="{{ trans('product-options.Placeholder Price') }}">
                                        </td>
                                        <td>
                                            <select x-model="row.priceType" class="select-table-product-attribute" id="">
                                                <template x-for="type in priceTypes">
                                                    <option x-bind:value="type" x-text="type.charAt(0).toUpperCase() + type.slice(1)"></option>
                                                </template>
                                            </select>    
                                        </td>
                                        <td class="text-center" style="padding-top: 15px;">
                                            <a @click="deleteRow(index)">
                                                <svg class="delete-product-attribute" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;">
                                                    <path d="M6 7H5v13a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7H6zm4 12H8v-9h2v9zm6 0h-2v-9h2v9zm.618-15L15 2H9L7.382 4H3v2h18V4z"></path>
                                                </svg>
                                            </a>
                                            
                                        </td>
                                    </tr>
                                    </template>
                                </tbody>
                            </table>
                            <a class="btn btn-primary" @click='addNewRowAttribute'>{{ trans('product-options.Add new row') }}</a>
                            <input type="hidden" name="option_value" x-model='JSON.stringify(rows)'>
                        </div>
                    </div><!-- .panel -->
                </div>
                <div class="col-md-4">
                    <!-- ### DETAILS ### -->
                    <div class="panel">
                        <div class="panel-heading-botom">
                            <h3 class="panel-title"><i class="icon wb-clipboard"></i> {{ trans('product-options.Type') }}</h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <select class="form-control" name="type">
                                    <option value="Dropdowm"@if(isset($dataTypeContent->type) && $dataTypeContent->type == 'Dropdowm') selected="selected"@endif>Dropdowm</option>
                                    <option value="Checkbox"@if(isset($dataTypeContent->type) && $dataTypeContent->type == 'Checkbox') selected="selected"@endif>Checkbox</option>
                                    <option value="RadioButton"@if(isset($dataTypeContent->type) && $dataTypeContent->type == 'RadioButton') selected="selected"@endif>RadioButton</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="panel ">
                        <div class="panel-heading-botom">
                            <h3 class="panel-title"><i class="icon wb-clipboard"></i> {{ trans('product-options.Is required?') }}</h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <input type="checkbox" name="is_required" @if(isset($dataTypeContent->is_required) && $dataTypeContent->is_required == 'on') checked @endif  class="toggleswitch">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @section('submit-buttons')
                <button type="submit" class="btn btn-primary pull-right">
                     @if($edit){{ trans('product-options.Update option') }}@else <i class="icon wb-plus-circle"></i> {{ trans('product-options.Create new option') }}@endif
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
            Alpine.data('product_options', () => ({
                priceTypes: [
                    'fixed',
                    'percent'
                ],
                rows: [],
                init(){
                    let optionValue = `{!! $dataTypeContent->option_value !!}`;
                    this.rows = optionValue ? JSON.parse(optionValue) : [{ label: '', price: '', priceType: 'fixed' }]
                },
                addNewRowAttribute(){
                    this.rows.push({label:'',price:'',priceType: 'fixed'})
                },
                deleteRow(index) {
                    this.rows = this.rows.filter((item, i) => i !== index);
                }
              
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
