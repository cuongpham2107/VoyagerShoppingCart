@php
    $edit = !is_null($dataTypeContent->getKey());
    $add = is_null($dataTypeContent->getKey());
@endphp

@extends('voyager::master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">

@stop

@section('page_title', __('voyager::generic.' . ($edit ? 'edit' : 'add')) . ' ' .
    $dataType->getTranslatedAttribute('display_name_singular'))

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i>
        {{ __('voyager::generic.' . ($edit ? 'edit' : 'add')) . ' ' . $dataType->getTranslatedAttribute('display_name_singular') }}
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')
    <div class="page-content container-fluid" x-data="product_attributes">
        <form role="form" class="form-edit-add"
            action="{{ $edit ? route('voyager.' . $dataType->slug . '.update', $dataTypeContent->getKey()) : route('voyager.' . $dataType->slug . '.store') }}"
            method="POST" enctype="multipart/form-data">
            <!-- PUT Method if we are editing -->
            @if ($edit)
                {{ method_field('PUT') }}
            @endif
            {{ csrf_field() }}

            <div class="row">
                <div class="col-md-8">
                    <!-- ### TITLE ### -->
                    <div class="panel">
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
                                {{ trans('product-attributes.Name') }}
                            </h3>
                        </div>
                        <div class="panel-body">
                            <input x-model="name_attribute" @input="generateSlug" type="text" class="form-control"
                                id="title" name="name"
                                placeholder=" {{ trans('product-attributes.Placeholder Name') }} "
                                >
                        </div>

                        <div class="panel-heading">
                            <h3 class="panel-title">
                                {{ trans('product-attributes.Slug') }}
                            </h3>
                        </div>
                        <div class="panel-body">
                            <input x-model="slug_attribute" type="text" class="form-control" id="title"
                                name="slug" readonly value="{{ $dataTypeContent->slug ?? '' }}">
                        </div>
                    </div>

                    <!-- ### CONTENT ### -->
                    <div class="panel">
                        <div class="panel-heading">
                            <h3 class="panel-title">{{ trans('product-attributes.Option value') }}</h3>
                        </div>

                        <div class="panel-body">
                            <table class="table table-bordered primary table-attributes">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col" style="width:10%">{{ trans('product-attributes.Is default?') }}</th>
                                        <th scope="col">{{ trans('product-attributes.TITLE') }}</th>
                                        <th scope="col">{{ trans('product-attributes.SLUG') }}</th>
                                        <th scope="col">{{ trans('product-attributes.COLOR') }}</th>
                                        <th scope="col">{{ trans('product-attributes.IMAGE') }}</th>
                                        <th scope="col">{{ trans('product-attributes.REMOVE') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(row, index) in rows" :key="index">
                                        <tr>
                                            <th class="text-center" style="padding-top: 15px;">
                                                <p x-text="index">#</p>
                                            </th>
                                            <td>
                                                <input x-model="row.is_default" class="input-table-product-attribute"
                                                    type="checkbox" style="margin-top: 13px;">
                                            </td>
                                            <td>
                                                <input x-model="row.name" class="input-table-product-attribute"
                                                    type="text">
                                            </td>
                                            <td>
                                                <input x-model="row.slug" class="input-table-product-attribute"
                                                    type="text">
                                            </td>
                                            <td>
                                                <div id="color-picker">
                                                    <span class="picker">
                                                        <input class="colorPick" x-model="row.color" type="color"
                                                            value="#FFFFFF">
                                                    </span>
                                                </div>

                                            </td>
                                            <td>
                                                <label x-bind:for="'uploadImage-' + index">
                                                    <img x-bind:id="'output-image-' + index"
                                                        x-bind:src="row.image ? `{{ Voyager::image('${row.image}') }}` : 'https://edutalk.edu.vn/_nuxt/assets/images/default.jpg'"
                                                        src="https://edutalk.edu.vn/_nuxt/assets/images/default.jpg"
                                                        alt="" width="35" height="35"
                                                        style="margin-left: 15px;"
                                                        >
                                                </label>
                                                <input x-on:change.debounce="loadFile($event,index)" type="file"
                                                    x-bind:id="'uploadImage-' + index" style="display:none;" value=''>
                                                <input x-model="row.image" type="text" x-bind:id="'imageName-' + index"
                                                    style="display:none;">
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
                            <a class="btn btn-primary"
                                @click='addNewRowAttribute'>{{ trans('product-attributes.Add new row') }}</a>
                            <input type="hidden" name="attributes_list" x-model='JSON.stringify(rows)'>
                        </div>
                    </div><!-- .panel -->
                </div>
                
                <div class="col-md-4">
                    <!-- ### DETAILS ### -->
                    <div class="panel">
                        <div class="panel-heading-botom">
                            <h3 class="panel-title"><i class="icon wb-clipboard"></i> {{ trans('product-attributes.Status') }}</h3>
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
                    <div class="panel ">
                        <div class="panel-heading-botom">
                            <h3 class="panel-title"><i class="icon wb-clipboard"></i> {{ trans('product-attributes.Display Layout') }}</h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <select class="form-control" name="display_layout">
                                    <option value="dropdown"@if(isset($dataTypeContent->display_layout) && $dataTypeContent->display_layout == 'dropdown') selected="selected"@endif>Dropdown Swatch</option>
                                    <option value="visual"@if(isset($dataTypeContent->display_layout) && $dataTypeContent->display_layout == 'visual') selected="selected"@endif>Visual Swatch</option>
                                    <option value="text"@if(isset($dataTypeContent->display_layout) && $dataTypeContent->display_layout == 'text') selected="selected"@endif>Text Swatch</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="panel ">
                        <div class="panel-heading-botom">
                            <h3 class="panel-title"><i class="icon wb-clipboard"></i> {{ trans('product-attributes.Order') }}</h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                              <input class="form-control" type="number" name="order" id="" value="{{ $dataTypeContent->order ?? '' }}">
                            </div>
                        </div>
                    </div>
                    <div class="panel ">
                        <div class="panel-heading-botom">
                            <h3 class="panel-title"><i class="icon wb-clipboard"></i> {{ trans('product-attributes.Category') }}</h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <input type="hidden" x-model="selectedIds" name="category_id">
                               <ul class="attribute-categories">
                                    <template x-for="(level,i) in categories" :key="i">
                                        <li x-html="renderLevel(level,i)"></li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @section('submit-buttons')
                <button type="submit" class="btn btn-primary pull-right">
                    @if ($edit)
                        {{ trans('product-attributes.Update attribute') }}
                    @else
                        <i class="icon wb-plus-circle"></i> {{ trans('product-attributes.Create new attribute') }}
                    @endif
                </button>
            @stop
            @yield('submit-buttons')
        </form>

        <div style="display:none">
            <input type="hidden" id="upload_url" value="{{ route('voyager.upload') }}">
            <input type="hidden" id="upload_type_slug" value="{{ $dataType->slug }}">
        </div>
        @include('components.alert')
    </div>



<div class="modal fade modal-danger" id="confirm_delete_modal">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="voyager-warning"></i> {{ __('voyager::generic.are_you_sure') }}
                </h4>
            </div>

            <div class="modal-body">
                <h4>{{ __('voyager::generic.are_you_sure_delete') }} '<span class="confirm_delete_name"></span>'</h4>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                    data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                <button type="button" class="btn btn-danger"
                    id="confirm_delete">{{ __('voyager::generic.delete_confirm') }}</button>
            </div>
        </div>
    </div>
</div>
<!-- End Delete File Modal -->
@stop

@section('javascript')

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('product_attributes', () => ({
            name_attribute: '{{ $dataTypeContent->name ?? '' }}',
            slug_attribute: '{{ $dataTypeContent->slug ?? '' }}',
            rows: [],
            categories: {!! $categoryTreeJson !!},
            selectedIds:[],
            alertMessage: '',
            alert: false,
            init() {
                let idCategories = `{!! $dataTypeContent->category_id !!}`;
                let attributes_list = `{!! $dataTypeContent->attributes_list !!}`;
                this.rows = attributes_list ? JSON.parse(attributes_list) : [{is_default: '', name: '',slug: '',color: '',image: ''}]
                this.selectedIds = idCategories ? idCategories.split(',').map((num)=>{ 
                        return parseInt(num) 
                    }) : []
            },

            // category
            renderLevel(level,index){
                let html = `<div class="tree-categories" style="margin-bottom: 5px;">
                                <input x-on:change="toggleSelection(level.id)" x-bind:checked="selectedIds.includes(level.id)" type="checkbox">
                                <span x-text="level.name"></span>
                            </div>`;
                if(level.children) {
                    html += `<ul class="attribute-categories">
                                <template x-for='(level,i) in level.children'>
                                    <li x-html="renderLevel(level,i)"></li>
                                </template>
                            </ul>`;
                }
                return html;
            },
            toggleSelection(id) {
                const index = this.selectedIds.indexOf(id);
                if (index === -1) {
                    this.selectedIds.push(id);
                } else {
                    this.selectedIds.splice(index, 1);
                }
                // console.log(this.selectedIds)
            },
            // end category
            addNewRowAttribute() {
                this.rows.push({
                    is_default: '',
                    name: '',
                    slug: '',
                    color: '',
                    image: ''
                })
            },
            loadFile(event, index) {
                this.alert = false
                let output = document.getElementById(`output-image-${index}`);
                const image = event.target.files[0];
                if (image) {
                    this.uploadImage(image, index);
                }
                output.src = URL.createObjectURL(event.target.files[0]);
                output.onload = function() {
                    URL.revokeObjectURL(output.src);
                }
            },
            uploadImage(image, index) {
                const formData = new FormData();
                formData.append('file', image);
                formData.append("upload_path", 'product-attributes');
                formData.append("filename", this.renameImage(image.name));
                formData.append("details", '');
                fetch('{{ route('voyager.media.upload') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success == false){
                            this.alertMessage = data.message
                            this.alert = true
                        }
                        this.rows[index].image = data.path;
                        
                    })
                    .catch(error => {
                        console.error('Lỗi khi tải lên:', error);
                    });
            },
            renameImage(fileName) {
                return fileName
                    .toLowerCase()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .replace(/\.[^.]+$/, '')
            },

            deleteRow(index) {
                if(this.rows[index].image){
                    const formData = new FormData();
                    formData.append('files', this.rows[index].image);
                    fetch('{{ route('delete_image_media') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        this.rows[index].image = data.path;
                    })
                    .catch(error => {
                        console.error('Lỗi khi tải lên:', error);
                    });
                }
                
                this.rows = this.rows.filter((item, i) => i !== index);
            },
            // name and slug
            generateSlug() {
                const name = this.name_attribute || '';
                const slug = this.removeAccents(name)
                    .toLowerCase()
                    .replace(/ /g, '-')
                    .replace(/[^\w-]+/g, '');
                this.slug_attribute = slug;
            },
            removeAccents(str) {
                return str.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
            },
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
                slug: '{{ $dataType->slug }}',
                filename: $file.data('file-name'),
                id: $file.data('id'),
                field: $file.parent().data('field-name'),
                multi: isMulti,
                _token: '{{ csrf_token() }}'
            }

            $('.confirm_delete_name').text(params.filename);
            $('#confirm_delete_modal').modal('show');
        };
    }

    $('document').ready(function() {
        $('#slug').slugify();

        $('.toggleswitch').bootstrapToggle();

        //Init datepicker for date fields if data-datepicker attribute defined
        //or if browser does not handle date inputs
        $('.form-group input[type=date]').each(function(idx, elt) {
            if (elt.type != 'date' || elt.hasAttribute('data-datepicker')) {
                elt.type = 'text';
                $(elt).datetimepicker($(elt).data('datepicker'));
            }
        });

        @if ($isModelTranslatable)
            $('.side-body').multilingual({
                "editing": true
            });
        @endif

        $('.side-body input[data-slug-origin]').each(function(i, el) {
            $(el).slugify();
        });

        $('.form-group').on('click', '.remove-multi-image', deleteHandler('img', true));
        $('.form-group').on('click', '.remove-single-image', deleteHandler('img', false));
        $('.form-group').on('click', '.remove-multi-file', deleteHandler('a', true));
        $('.form-group').on('click', '.remove-single-file', deleteHandler('a', false));

        $('#confirm_delete').on('click', function() {
            $.post('{{ route('voyager.' . $dataType->slug . '.media.remove') }}', params, function(
                response) {
                if (response &&
                    response.data &&
                    response.data.status &&
                    response.data.status == 200) {

                    toastr.success(response.data.message);
                    $file.parent().fadeOut(300, function() {
                        $(this).remove();
                    })
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
