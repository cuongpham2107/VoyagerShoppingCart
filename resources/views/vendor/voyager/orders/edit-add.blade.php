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
    <div class="page-content container-fluid" x-data="orders">
        <form role="form"
            class="form-edit-add"
            action="{{ $edit ? route('voyager.'.$dataType->slug.'.update', $dataTypeContent->getKey()) : route('voyager.'.$dataType->slug.'.store') }}"
            method="POST" enctype="multipart/form-data">    
            <!-- PUT Method if we are editing -->
            @if($edit)
                {{ method_field("PUT") }}
            @endif
            {{ csrf_field() }}

            <div class="row" style="justify-content: center;display: flex;">
                <div class="col-md-6">
                    <!-- ### TITLE ### -->
                    <div class="panel panel panel-bordered panel-primary">
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
                                {{ trans('orders.Order information') }}
                            </h3>
                        </div>
                        <div style="margin: 10px">
                          <table class="table table-bordered table-order" >
                            <thead>
                              <tr>
                                <th scope="col"></th>
                                <th scope="col">{{ trans('orders.PRODUCT NAME') }}</th>
                                <th scope="col">{{ trans('orders.PRICE') }}</th>
                                <th scope="col">{{ trans('orders.QUANTITY') }}</th>
                                <th scope="col">{{ trans('orders.TOTAL') }}</th>
                                <th scope="col">{{ trans('orders.ACTION') }}</th>
                              </tr>
                            </thead>
                            <style>
                                .table .order-image-table {
                                    width: 50px;
                                    object-fit: cover;
                                }
                                .table .order-product-name-table{
                                    text-decoration: none !important;
                                    font-weight: 500;
                                }
                                .table .order-product-description-table{
                                    font-size: 12px !important;
                                }
                                .table-order td{
                                    width:10%
                                }
                                .table-order span{
                                    font-weight: 600;
                                    color:#444;
                                    margin: 0;
                                    padding-top: 15px;
                                }
                                .table-order>thead:first-child>tr:first-child>th{
                                    font-weight: 400;
                                    font-size: 12px !important;
                                    color:#959595;
                                    
                                }
                                .table-order input{
                                    border-radius: 5px;
                                    border: 0.5px solid rgb(183, 181, 181);
                                    padding: 5px 0px 5px 0px;
                                    width: 60px;
                                    margin-top: 7px;
                                    align-items: center;
                                    display: flex;

                                }
                                .table-order p{
                                    align-items: center;
                                    display: flex;
                                }
                            </style>
                            <tbody>
                                <template x-for="(test, index) in [1,2,3]" :key="index">
                                    <tr>
                                        <th style="width:10%">
                                            <img class="order-image-table" src="https://edutalk.edu.vn/_nuxt/assets/images/default.jpg" alt="" style="">
                                        </th>
                                        <th> 
                                            <a class="order-product-name-table" href=""> Dual Camera 20MP (Digital) (Global Store)</a>
                                            <p class="order-product-description-table">(Size: XL, Color: Black)</p>
                                        </th>
                                        <td> 
                                            <p><span>$100.00</span></p>
                                        </td>
                                        <td>
                                          <input type="number" name="quantity" id="" style="width: 70px;">
                                        </td>
                                        <td> 
                                            <p><span>$100.00</span></p>
                                        </td>
                                        <td>
                                          
                                            <p>  <span>X</span></p>
                                        </td>
                                      </tr>
                                </template>
                              
                              
                            </tbody>
                          </table>
                        </div>
                     
                    </div>
                    
                </div>
                <div class="col-md-3">
                    <!-- ### DETAILS ### -->
                    <div class="panel">
                        <div class="panel-heading-botom">
                            <h3 class="panel-title"><i class="icon wb-clipboard"></i> {{ trans('orders.Customer') }}</h3>
                            {{-- <p>x</p> --}}
                        </div>
                    </div>
                   
                </div>
            </div>

            @section('submit-buttons')
                <button type="submit" class="btn btn-primary pull-right">
                     @if($edit){{ trans('product-brands.Update brands') }}@else <i class="icon wb-plus-circle"></i> {{ trans('product-brands.Create new brands') }}@endif
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
    {{-- @dd($dataTypeContent) --}}
@stop

@section('javascript')

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('orders', () => ({
                
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
