@php
    $edit = !is_null($dataTypeContent->getKey());
    $add  = is_null($dataTypeContent->getKey());
    $categories = \App\Models\ProductCategory::with('children')->select('id','name','parent_id','order')->get();
@endphp

@extends('voyager::master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/5.3.45/css/materialdesignicons.min.css">
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
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-4">
                
                <div class="category-body-tree-list">
                    <div class="tree-list" >
                        <h1 class="title-tree-list">List Categories</h1>
                        <hr>
                        <div class="tree-list-data" x-data="fileTree()">
                            <ul style="list-style: none;">
                                <template x-for="(level,i) in levels">
                                    <li x-html="renderLevel(level,i)"></li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>
                       
            </div>
            <div class="col-md-8">

                <div class="panel panel-bordered">
                    <!-- form start -->
                    <form role="form"
                            class="form-edit-add"
                            action="{{ $edit ? route('voyager.'.$dataType->slug.'.update', $dataTypeContent->getKey()) : route('voyager.'.$dataType->slug.'.store') }}"
                            method="POST" enctype="multipart/form-data">
                        <!-- PUT Method if we are editing -->
                        @if($edit)
                            {{ method_field("PUT") }}
                        @endif

                        <!-- CSRF TOKEN -->
                        {{ csrf_field() }}

                        <div class="panel-body">

                            @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Adding / Editing -->
                            @php
                                $dataTypeRows = $dataType->{($edit ? 'editRows' : 'addRows' )};
                            @endphp

                            @foreach($dataTypeRows as $row)
                                <!-- GET THE DISPLAY OPTIONS -->
                                @php
                                    $display_options = $row->details->display ?? NULL;
                                    if ($dataTypeContent->{$row->field.'_'.($edit ? 'edit' : 'add')}) {
                                        $dataTypeContent->{$row->field} = $dataTypeContent->{$row->field.'_'.($edit ? 'edit' : 'add')};
                                    }
                                @endphp
                                @if (isset($row->details->legend) && isset($row->details->legend->text))
                                    <legend class="text-{{ $row->details->legend->align ?? 'center' }}" style="background-color: {{ $row->details->legend->bgcolor ?? '#f0f0f0' }};padding: 5px;">{{ $row->details->legend->text }}</legend>
                                @endif

                                <div class="form-group @if($row->type == 'hidden') hidden @endif col-md-{{ $display_options->width ?? 12 }} {{ $errors->has($row->field) ? 'has-error' : '' }}" @if(isset($display_options->id)){{ "id=$display_options->id" }}@endif>
                                    {{ $row->slugify }}
                                    <label class="control-label" for="name">{{ $row->getTranslatedAttribute('display_name') }}</label>
                                    @include('voyager::multilingual.input-hidden-bread-edit-add')
                                    @if ($add && isset($row->details->view_add))
                                        @include($row->details->view_add, ['row' => $row, 'dataType' => $dataType, 'dataTypeContent' => $dataTypeContent, 'content' => $dataTypeContent->{$row->field}, 'view' => 'add', 'options' => $row->details])
                                    @elseif ($edit && isset($row->details->view_edit))
                                        @include($row->details->view_edit, ['row' => $row, 'dataType' => $dataType, 'dataTypeContent' => $dataTypeContent, 'content' => $dataTypeContent->{$row->field}, 'view' => 'edit', 'options' => $row->details])
                                    @elseif (isset($row->details->view))
                                        @include($row->details->view, ['row' => $row, 'dataType' => $dataType, 'dataTypeContent' => $dataTypeContent, 'content' => $dataTypeContent->{$row->field}, 'action' => ($edit ? 'edit' : 'add'), 'view' => ($edit ? 'edit' : 'add'), 'options' => $row->details])
                                    @elseif ($row->type == 'relationship')
                                        @include('voyager::formfields.relationship', ['options' => $row->details])
                                    @else
                                        {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
                                    @endif

                                    @foreach (app('voyager')->afterFormFields($row, $dataType, $dataTypeContent) as $after)
                                        {!! $after->handle($row, $dataType, $dataTypeContent) !!}
                                    @endforeach
                                    @if ($errors->has($row->field))
                                        @foreach ($errors->get($row->field) as $error)
                                            <span class="help-block">{{ $error }}</span>
                                        @endforeach
                                    @endif
                                </div>
                            @endforeach

                        </div><!-- panel-body -->

                        <div class="panel-footer">
                            @section('submit-buttons')
                                <button type="submit" class="btn btn-primary save">{{ __('voyager::generic.save') }}</button>
                            @stop
                            @yield('submit-buttons')
                        </div>
                    </form>

                    <div style="display:none">
                        <input type="hidden" id="upload_url" value="{{ route('voyager.upload') }}">
                        <input type="hidden" id="upload_type_slug" value="{{ $dataType->slug }}">
                    </div>
                </div>
            </div>
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
@stop

@section('javascript')
<script>
        let fileTree = function() {
        return {
            categories: `{!! $categories !!}`,
            levels: [
                {
                    title: 'AlphineJS',
                    children: [
                        {
                            title: 'LICENSE.md',
                        },
                        {
                            title: 'README.ja.md',
                        },
                        {
                            title: 'README.md',
                        },
                        {
                            title: 'README.ru.md',
                        },
                        {
                            title: 'README_zh-TW.md',
                        },
                        {
                            title: 'babel.config.js',
                        },
                        {
                            title: 'dist/',
                            children: [
                                {
                                    title: 'alpine-ie11.js',
                                },
                                {
                                    title: 'alpine.js',
                                },
                            ],
                        },
                        {
                            title: 'examples/',
                            children: [
                                {
                                    title: 'card-game.html',
                                },
                                {
                                    title: 'index.html',
                                },
                                {
                                    title: 'tags.html',
                                },
                            ],
                        },
                        {
                            title: 'jest.config.js',
                        },
                        {
                            title: 'package-lock.json',
                        },
                        {
                            title: 'package.json',
                        },
                        {
                            title: 'rollup-ie11.config.js',
                        },
                        {
                            title: 'rollup.config.js',
                        },
                        {
                            title: 'src/',
                            children: [
                                {
                                    title: 'component.js',
                                },
                                {
                                    title: 'directives/',
                                    children: [
                                        {
                                            title: 'bind.js',
                                        },
                                        {
                                            title: 'for.js',
                                        },
                                        {
                                            title: 'html.js',
                                        },
                                        {
                                            title: 'if.js',
                                        },
                                        {
                                            title: 'model.js',
                                        },
                                        {
                                            title: 'on.js',
                                        },
                                        {
                                            title: 'show.js',
                                        },
                                        {
                                            title: 'text.js',
                                        },
                                    ],
                                },
                                {
                                    title: 'index.js',
                                },
                                {
                                    title: 'observable.js',
                                },
                                {
                                    title: 'polyfills.js',
                                },
                                {
                                    title: 'utils.js',
                                },
                            ],
                        },
                        {
                            title: 'test/',
                            children: [
                                {
                                    title: 'bind.spec.js',
                                },
                                {
                                    title: 'cloak.spec.js',
                                },
                                {
                                    title: 'constructor.spec.js',
                                },
                                {
                                    title: 'custom-magic-properties.spec.js',
                                },
                                {
                                    title: 'data.spec.js',
                                },
                                {
                                    title: 'debounce.spec.js',
                                },
                                {
                                    title: 'dispatch.spec.js',
                                },
                                {
                                    title: 'el.spec.js',
                                },
                                {
                                    title: 'for.spec.js',
                                },
                                {
                                    title: 'html.spec.js',
                                },
                                {
                                    title: 'if.spec.js',
                                },
                                {
                                    title: 'lifecycle.spec.js',
                                },
                                {
                                    title: 'model.spec.js',
                                },
                                {
                                    title: 'mutations.spec.js',
                                },
                                {
                                    title: 'nesting.spec.js',
                                },
                                {
                                    title: 'next-tick.spec.js',
                                },
                                {
                                    title: 'on.spec.js',
                                },
                                {
                                    title: 'readonly.spec.js',
                                },
                                {
                                    title: 'ref.spec.js',
                                },
                                {
                                    title: 'show.spec.js',
                                },
                                {
                                    title: 'spread.spec.js',
                                },
                                {
                                    title: 'strict-mode.spec.js',
                                },
                                {
                                    title: 'text.spec.js',
                                },
                                {
                                    title: 'transition.spec.js',
                                },
                                {
                                    title: 'utils.spec.js',
                                },
                                {
                                    title: 'version.spec.js',
                                },
                                {
                                    title: 'watch.spec.js',
                                },
                            ],
                        },
                    ],
                },
            ],
            renderLevel: function(obj,i){
                let ref = 'l'+Math.random().toString(36).substring(7);
                let html = `<a href="#" class="tree-title" :class="{'has-children':level.children}" x-html="(level.children ? '<i class=\\'mdi mdi-folder-outline has-children\\' ></i>':'<i class=\\'mdi mdi-file-outline level-children\\'></i>')+' '+level.title" ${obj.children?`  @click.prevent="toggleLevel($refs.${ref})"`:''}></a>`;

                if(obj.children) {
                    html += `<ul style="display:block;" x-ref="${ref}" class="ul-level-children">
                            <template x-for='(level,i) in level.children'>
                                <li x-html="renderLevel(level,i)"></li>
                            </template>
                        </ul>`;
                }
                return html;
            },
            showLevel: function(el) {
                if (el.style.length === 1 && el.style.display === 'none') {
                    el.removeAttribute('style')
                } else {
                    el.style.removeProperty('display')
                }
                setTimeout(()=>{
                    el.previousElementSibling.querySelector('i.mdi').classList.add("mdi-folder-open-outline");
                    el.previousElementSibling.querySelector('i.mdi').classList.remove("mdi-folder-outline");
                    el.style.opacity = '1';
                },10)
            },
            hideLevel: function(el) {
                el.style.display = 'none';
                el.style.opacity = '0';
                el.previousElementSibling.querySelector('i.mdi').classList.remove("mdi-folder-open-outline");
                el.previousElementSibling.querySelector('i.mdi').classList.add("mdi-folder-outline");

                let refs = el.querySelectorAll('ul[x-ref]');
                for (var i = 0; i < refs.length; i++) {
                    this.hideLevel(refs[i]);
                }
            },
            toggleLevel: function(el) {
                if( el.style.length && el.style.display === 'none' ) {
                    this.showLevel(el);
                } else {
                    this.hideLevel(el);
                }
            }
        }
    }
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
            $('.toggleswitch').bootstrapToggle();

            //Init datepicker for date fields if data-datepicker attribute defined
            //or if browser does not handle date inputs
            $('.form-group input[type=date]').each(function (idx, elt) {
                if (elt.hasAttribute('data-datepicker')) {
                    elt.type = 'text';
                    $(elt).datetimepicker($(elt).data('datepicker'));
                } else if (elt.type != 'date') {
                    elt.type = 'text';
                    $(elt).datetimepicker({
                        format: 'L',
                        extraFormats: [ 'YYYY-MM-DD' ]
                    }).datetimepicker($(elt).data('datepicker'));
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
