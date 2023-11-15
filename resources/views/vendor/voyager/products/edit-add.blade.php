@php
    $edit = !is_null($dataTypeContent->getKey());
    $add = is_null($dataTypeContent->getKey());
    $product_collections = App\Models\ProductCollection::where('status','published')->get();
    $product_labels = App\Models\ProductLabel::where('status','published')->get();
    $product_taxes = App\Models\ProductTax::where('status','published')->get();
    $attributes =  App\Models\ProductAttribute::where('status','published')->get();
    $options = App\Models\ProductOption::get();
    // dd($options);
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
    <div class="page-content container-fluid" x-data="products">
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
                        @php
                            $dataTypeRows = $dataType->{($edit ? 'editRows' : 'addRows' )};
                        @endphp
                        
                        @foreach ($dataTypeRows as $row)
                            @if($row->type == 'text' && $row->field == 'name')
                                <div class="panel-heading">
                                    <h3 class="panel-title">
                                        {{ $row->getTranslatedAttribute('display_name') }}
                                    </h3>
                                </div>
                                <div class="panel-body">
                                    {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
                                </div>
                            @elseif($row->type == 'markdown_editor')
                                <div class="panel-heading">
                                    <h3 class="panel-title">
                                        {{ $row->getTranslatedAttribute('display_name') }}
                                    </h3>
                                </div>
                            
                                <div class="panel-body">
                                    {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
                                </div>
                            @elseif($row->type == 'media_picker')
                                <div class="panel-heading">
                                    <h3 class="panel-title">
                                        {{ trans('products.Images') }}
                                    </h3>
                                </div>
                                <div class="panel-body">
                                    {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
                                </div>
                            @endif
                            
                        @endforeach
                    </div>
                    <div class="panel">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                {{ trans('products.Overview') }}
                            </h3>
                        </div>
                        <div class="panel-body">
                            @foreach ($dataTypeRows as $row)
                            @php
                                $display_options = $row->details->display ?? NULL;
                                
                            @endphp
                                @if($row->type == 'number' 
                                ||  $row->type == 'text' && $row->field == 'sku' 
                                ||  $row->type == 'text' && $row->field == 'barcode'
                                ||  $row->type == 'select_dropdown' && $row->field == 'stock_status')
                                
                                        <div class="form-group @if($row->type == 'hidden') hidden @endif col-md-{{ $display_options->width ?? 12 }} {{ $errors->has($row->field) ? 'has-error' : '' }}" @if(isset($display_options->id)){{ "id=$display_options->id" }}@endif>
                                        <label class="control-label" for="name">{{ $row->getTranslatedAttribute('display_name') }}</label>
                                        {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
                                        </div>
                                    
                                @endif  
                            @endforeach
                        </div>
                    </div>
                    <div class="panel ">
                        <div class="panel-heading">
                            <h3 class="panel-title">{{ trans('products.Attributes') }}</h3>
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
                                    <template x-for="(row, index) in rowAttributes" :key="index">
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
                                                        style="width: max-content;
                                                        object-fit: cover;
                                                        margin-left: 15px;"
                                                        >
                                                </label>
                                                <input x-on:change.debounce="loadFile($event,index)" type="file"
                                                    x-bind:id="'uploadImage-' + index" style="display:none;" value=''>
                                                <input x-model="row.image" type="text" x-bind:id="'imageName-' + index"
                                                    style="display:none;">
                                            </td>
                                            <td class="text-center" style="padding-top: 15px;">
                                                <a @click="deleteRowAttribute(index)">
                                                    <svg class="delete-product-attribute" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;">
                                                        <path d="M6 7H5v13a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7H6zm4 12H8v-9h2v9zm6 0h-2v-9h2v9zm.618-15L15 2H9L7.382 4H3v2h18V4z"></path>
                                                    </svg>
                                                </a>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                            <div style="display: flex;justify-content: space-between;align-items: center;">
                                <a class="btn btn-primary" @click='addNewRowAttribute'>{{ trans('product-attributes.Add new row') }}</a>
                                <div style="display: flex;align-items: center;">
                                    <div class="">
                                        <select class="form-control" x-model="selected_available_attribute">
                                            <option value="">Attributes</option>
                                            @foreach ($attributes as $item)
                                                <option value="{{$item->attributes_list}}">{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <a style="margin-left: 10px" class="btn btn-primary" @click='SelectAvailableAttributes'>{{ trans('product-attributes.Select available Attributes') }}</a>
                                </div>
                               
                            </div>
                            <input type="hidden" name="product_attributes" x-model='JSON.stringify(rowAttributes)'>
                        </div>
                    </div>
                    <div class="panel ">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="icon wb-clipboard"></i> {{ trans('products.Options') }}</h3>
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
                                    <template x-for="(row, index) in rowOptions" :key="index">
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
                                            <a @click="deleteRowOption(index)">
                                                <svg class="delete-product-attribute" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;">
                                                    <path d="M6 7H5v13a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7H6zm4 12H8v-9h2v9zm6 0h-2v-9h2v9zm.618-15L15 2H9L7.382 4H3v2h18V4z"></path>
                                                </svg>
                                            </a>
                                            
                                        </td>
                                    </tr>
                                    </template>
                                </tbody>
                            </table>
                           
                            <div style="display: flex;justify-content: space-between;align-items: center;">
                                <a class="btn btn-primary" @click='addNewRowOption'>{{ trans('product-options.Add new row') }}</a>
                                <div style="display: flex;align-items: center;">
                                    <div class="">
                                        <select class="form-control" x-model="selected_available_option">
                                            <option value="">Option</option>
                                            @foreach ($options as $item)
                                                <option value="{{$item->option_value}}">{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <a style="margin-left: 10px" class="btn btn-primary" @click='SelectAvailableOptions'>{{ trans('product-options.Select available Options') }}</a>
                                </div>
                               
                            </div>
                            <input type="hidden" name="product_options" x-model='JSON.stringify(rowOptions)'>
                        </div>
                    </div>
                    
                </div>
                <div class="col-md-4">
                    <div class="panel">
                        <div class="panel-heading-botom">
                            <h3 class="panel-title"><i class="icon wb-clipboard"></i> {{ trans('products.Status') }}</h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <select class="form-control" name="status">
                                    <option value="published"@if (isset($dataTypeContent->status) && $dataTypeContent->status == 'published') selected="selected" @endif>
                                        Published</option>
                                    <option value="draft"@if (isset($dataTypeContent->status) && $dataTypeContent->status == 'draft') selected="selected" @endif>Draft
                                    </option>
                                    <option value="pending"@if (isset($dataTypeContent->status) && $dataTypeContent->status == 'pending') selected="selected" @endif>
                                        Pending</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="panel ">
                        <div class="panel-heading-botom">
                            <h3 class="panel-title"><i class="icon wb-clipboard"></i> {{ trans('products.Is featured?') }}
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <input type="checkbox" name="is_featured" @if (isset($dataTypeContent->is_featured) && $dataTypeContent->is_featured == 'on') checked @endif
                                    class="toggleswitch">
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-heading-botom">
                            <h3 class="panel-title"><i class="icon wb-clipboard"></i> {{ trans('products.Brands') }}</h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <select class="form-control" name="brand_id">
                                    @foreach ($brands as $item)
                                        <option value="{{$item->id}}"@if (isset($dataTypeContent->brand_id) && $dataTypeContent->brand_id == $item->id) selected="selected" @endif>
                                            {{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-heading-botom">
                            <h3 class="panel-title"><i class="icon wb-clipboard"></i> {{ trans('products.Featured image') }}</h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                @if (isset($dataTypeContent->featured_image))
                                    <img src="{{ filter_var($dataTypeContent->featured_image, FILTER_VALIDATE_URL) ? $dataTypeContent->featured_image : Voyager::image($dataTypeContent->featured_image) }}"
                                        style="width:200px; height:auto; clear:both; display:block; padding:2px; border:1px solid #ddd; margin-bottom:10px;" />
                                @endif
                                <input type="file" name="featured_image">
                            </div>
                        </div>
                    </div>
                    <div class="panel ">
                        <div class="panel-heading-botom">
                            <h3 class="panel-title"><i class="icon wb-clipboard"></i>
                                {{ trans('product-attributes.Category') }}</h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <input type="hidden" x-model="selectedCategoryIds" name="categories">
                                <ul class="attribute-categories">
                                    <template x-for="(level,i) in categories" :key="i">
                                        <li x-html="renderLevel(level,i)"></li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-heading-botom">
                            <h3 class="panel-title"><i class="icon wb-clipboard"></i> {{ trans('products.Collections') }}</h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <ul class="attribute-categories">
                                    <input type="hidden" x-model="selectedCollectionIds" name="product_collections">
                                    <template x-for="(collection,i) in collections" :key="i">
                                        <li>
                                            <input x-on:change="selectionCollections(collection.id)" x-bind:checked="selectedCollectionIds.includes(collection.id)" type="checkbox">
                                            <span x-text="collection.name"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-heading-botom">
                            <h3 class="panel-title"><i class="icon wb-clipboard"></i> {{ trans('products.Labels') }}</h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <ul class="attribute-categories">
                                    <input type="hidden" x-model="selectedLabelIds" name="product_labels">
                                    <template x-for="(label,i) in labels" :key="i">
                                        <li>
                                            <input x-on:change="selectionLabels(label.id)" x-bind:checked="selectedLabelIds.includes(label.id)" type="checkbox">
                                            <span x-text="label.name"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-heading-botom">
                            <h3 class="panel-title"><i class="icon wb-clipboard"></i> {{ trans('products.Taxes') }}</h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <ul class="attribute-categories">
                                    <input type="hidden" x-model="selectedTaxIds" name="product_taxes">
                                    <template x-for="(tax,i) in taxes" :key="i">
                                        <li>
                                            <input x-on:change="selectionTaxes(tax.id)" x-bind:checked="selectedTaxIds.includes(tax.id)" type="checkbox">
                                            <span x-text="tax.name"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-heading-botom">
                            <h3 class="panel-title"><i class="icon wb-clipboard"></i> {{ trans('products.Tag') }}</h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                @foreach ($dataTypeRows as $row)
                                    @if($row->type == 'relationship')
                                        @include('voyager::formfields.relationship', ['options' => $row->details])
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @section('submit-buttons')
            <button type="submit" class="btn btn-primary pull-right">
                @if ($edit)
                    {{ trans('products.Update products') }}
                @else
                    <i class="icon wb-plus-circle"></i> {{ trans('products.Create new products') }}
                @endif
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
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="voyager-warning"></i> {{ __('voyager::generic.are_you_sure') }}</h4>
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
{{-- @dd($dataTypeContent) --}}
@stop

@section('javascript')

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('products', () => ({
            priceTypes: [
                'fixed',
                'percent'
            ],
            categories: {!! $categoryTreeJson !!},
            selectedCategoryIds: [],
            rowAttributes: [],
            rowOptions: [],
            collections: {!! $product_collections !!},
            selectedCollectionIds: [],
            labels: {!! $product_labels !!},
            selectedLabelIds:[],
            taxes: {!! $product_taxes !!},
            selectedTaxIds:[],
            selected_available_attribute: '',
            selected_available_option:'',
            init() {
                let idCategories = `{!! $dataTypeContent->categories !!}`;
                let attributes_list = `{!! $dataTypeContent->product_attributes !!}`;
                let optionValue = `{!! $dataTypeContent->product_options !!}`;

                let id_product_collection = `{!! $dataTypeContent->product_collections !!}`;
                let id_product_label = `{!! $dataTypeContent->product_labels !!}`;
                let id_product_tax = `{!! $dataTypeContent->product_taxes !!}`;
                this.rowOptions = optionValue ? JSON.parse(optionValue) : [{ label: '', price: '', priceType: 'fixed' }];
                this.rowAttributes = attributes_list ? JSON.parse(attributes_list) : [{is_default: '', name: '',slug: '',color: '',image: ''}];
                this.selectedCategoryIds = idCategories ? idCategories.split(',').map((num) => {
                    return parseInt(num)
                }) : [];
                this.selectedCollectionIds = id_product_collection ? id_product_collection.split(',').map((num) => {
                    return parseInt(num)
                }) : [];
                this.selectedLabelIds = id_product_label ? id_product_label.split(',').map((num) => {
                    return parseInt(num)
                }) : [];
                this.selectedTaxIds = id_product_tax ? id_product_tax.split(',').map((num) => {
                    return parseInt(num)
                }) : [];
            },
            // Category
            renderLevel(level, index) {
                let html = `<div class="tree-categories" style="margin-bottom: 5px;">
                                    <input x-on:change="selectionCategories(level.id)" x-bind:checked="selectedCategoryIds.includes(level.id)" type="checkbox">
                                    <span x-text="level.name"></span>
                                </div>`;
                if (level.children) {
                    html += `<ul class="attribute-categories">
                                    <template x-for='(level,i) in level.children'>
                                        <li x-html="renderLevel(level,i)"></li>
                                    </template>
                                </ul>`;
                }
                return html;
            },
            selectionCategories(id) {
                const index = this.selectedCategoryIds.indexOf(id);
                if (index === -1) {
                    this.selectedCategoryIds.push(id);
                } else {
                    this.selectedCategoryIds.splice(index, 1);
                }
                console.log(this.selectedCategoryIds, index)

            },
            //Attribute
            addNewRowAttribute() {
                this.rowAttributes.push({
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
                        this.rowAttributes[index].image = data.path;
                        
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
            deleteRowAttribute(index) {
                if(this.rowAttributes[index].image){
                    const formData = new FormData();
                    formData.append('files', this.rowAttributes[index].image);
                    fetch('{{ route('delete_image_media') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        this.rowAttributes[index].image = data.path;
                    })
                    .catch(error => {
                        console.error('Lỗi khi tải lên:', error);
                    });
                }
                
                this.rowAttributes = this.rowAttributes.filter((item, i) => i !== index);
            },
            SelectAvailableAttributes(){
              
                this.rowAttributes = this.selected_available_attribute 
                    ? JSON.parse(this.selected_available_attribute) 
                    : [{is_default: '', name: '',slug: '',color: '',image: ''}];
               
            },
            //Option
            addNewRowOption(){
                this.rowOptions.push({label:'',price:'',priceType: 'fixed'})
            },
            deleteRowOption(index) {
                this.rowOptions = this.rowOptions.filter((item, i) => i !== index);
            },
            SelectAvailableOptions(){
                this.rowOptions = this.selected_available_option
                    ? JSON.parse(this.selected_available_option) 
                    : [{ label: '', price: '', priceType: 'fixed' }];
            },
            //Collection 
            selectionCollections(id) {
                const index = this.selectedCollectionIds.indexOf(id);
                if (index === -1) {
                    this.selectedCollectionIds.push(id);
                } else {
                    this.selectedCollectionIds.splice(index, 1);
                }
                console.log(this.selectedCollectionIds, index)

            },
            //Label 
            selectionLabels(id) {
                const index = this.selectedLabelIds.indexOf(id);
                if (index === -1) {
                    this.selectedLabelIds.push(id);
                } else {
                    this.selectedLabelIds.splice(index, 1);
                }
                console.log(this.selectedLabelIds, index)
            },
             //Taxes 
             selectionTaxes(id) {
                const index = this.selectedTaxIds.indexOf(id);
                if (index === -1) {
                    this.selectedTaxIds.push(id);
                } else {
                    this.selectedTaxIds.splice(index, 1);
                }
                console.log(this.selectedTaxIds, index)
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
