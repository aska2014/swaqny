@extends('master')

@section('content')
<section id="main" class="clearfix">
	<div id="main-header" class="page-header">
    	<ul class="breadcrumb">
        	<li>
            	<i class="icon-home"></i>Control panel
                <span class="divider">&raquo;</span>
            </li>
            <li>
            	<a href="#">Galleries</a>
                <span class="divider">&raquo;</span>
            </li>
            <li>
            	<a href="#">Image Gallery</a>
            </li>
        </ul>
        
        <h1 id="main-heading">
        	{{ ucfirst($gallery->name) }} Gallery <span>You can choose from galleries to display all included images.</span>
        </h1>
    </div>
    
    <div id="main-content">

        <div class="row-fluid">
            <div class="span12">
                <div class="widget">
                    <div class="widget-header">
                        <span class="title">Galleries</span>
                    </div>
                    <div class="widget-content form-container">
                        <form class="form-horizontal">
                            <div class="control-group">
                                <label class="control-label" for="input17">Select gallery</label>
                                <div class="controls">
                                    <select id="gallery_name" class="select2-select-00 span12">

                                        @foreach($galleries as $a_gallery)
                                        
                                            @if($a_gallery->name == $gallery->name)
                                            <option value="{{ $a_gallery->name }}" selected>{{ $a_gallery->name }}</option>
                                            @else
                                            <option value="{{ $a_gallery->name }}">{{ $a_gallery->name }}</option>
                                            @endif

                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row-fluid">
            <div class="span12">
                <div class="widget collapsed">
                    <div class="widget-header">
                        <span class="title"><i class="icon-upload"></i> Upload images to {{ ucfirst($gallery->name) }} Gallery</span>
                        <div class="toolbar">
                            <span class="btn" data-toggle="widget"><i class="icon-sort"></i></span>
                        </div>
                    </div>
                    <div class="widget-content form-container">
                        <form class="form-horizontal" enctype="multipart/form-data" method="POST" action="{{ URL::to('image/gallery/' . $gallery->name) }}">
                            <fieldset id="input_cloning" class="sheepit-form">
                                <legend>
                                    Images
                                    <span id="input_cloning_controls" class="pull-right">
                                        <span class="btn btn-mini" id="input_cloning_add"><i class="icon-plus"></i></span>
                                    </span>
                                </legend>
                                <div id="input_cloning_template" class="control-group">
                                    <label for="input_cloning_#index#_input" class="control-label">Image <span id="input_cloning_label"></span></label>
                                    <div class="controls">
                                        <input type="file" id="input_cloning_#index#_input" name="Image[#index#]">
                                    </div>
                                    <span class="close" id="input_cloning_remove_current">&times;</span>
                                </div>
                                <div id="input_cloning_noforms_template" class="control-group">
                                    <p class="help-block">Add a new input by clicking the (+) button above</p>
                                </div>
                            </fieldset>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Upload images</button>
                                <button class="btn" type="reset">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        @if(!empty($gallery->images))
        <div class="gallery">
            <ul>
                @foreach($gallery->getImagesByPage($page, $number) as $image)
                <li>
                    <span class="thumbnail">
                        <img src="{{ $image->getSource() }}">
                    </span>
                    <span class="actions">
                        <a href="{{ $image->getSource() }}" rel="prettyPhoto[nature]"><i class="icon-search"></i></a>
                        <a href="{{ $image->getDeleteURL() }}" id="image_remove"><i class="icon-remove"></i></a> 
                    </span>
                </li>
                @endforeach
            </ul>
        </div>
        <div class="pagination pagination-centered">
            {{ $paginationHtml }}
        </div>
        @endif
    </div>
</section>
@stop

@section('scripts')

<script type="text/javascript">

$(document).ready(function()
{
    $("#gallery_name").change(function()
    {
        window.location = "{{ URL::to('image/gallery') }}" + "/" + $("#gallery_name").val();
    });
});


;(function( $, window, document, undefined ) {
            
    $(document).ready(function() {
        if( $.fn.sheepIt ) {
            $('#input_cloning').sheepIt({
                separator: '', 
                iniFormsCount: 1, 
                minFormsCount: 1, 
                maxFormsCount: 5
            });
        }
    });
    
    
}) (jQuery, window, document);

</script>

@stop