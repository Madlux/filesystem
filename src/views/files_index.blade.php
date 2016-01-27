@extends('layouts.main')

@section('head')
	@if(!$config['customieze_input_file'])
	<link href="{{ asset('/users/bootstrap-fileuploader/css/fileinput.min.css') }}" rel="stylesheet" type="text/css" />
	<script type='text/javascript' src="{{ asset('/users/bootstrap-fileuploader/js/fileinput.min.js') }}"></script>
	@endif
@endsection

@section('content')
	<div class='col-md-12' id='files'>
		@if(Request::has('errors'))
			@include('partials.errors',['errors' => Request::get('errors')])
		@endif
		<div class=''>
			@if(!$config['is_ajax'])
				<form method='post' action="{{ url('files/save') }}" enctype="multipart/form-data">
					<label class="control-label">Select File</label>
					<input id="input" name="input[]" type="file" class="file file-loading" multiple data-show-caption="true">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
				</form>
			@else
				<input id="input" name="input[]" type="file" class="file file-loading" multiple data-show-caption="true">
			@endif
		</div>
		<a class='btn btn-info col-md-2 glyphicon glyphicon-plus create_folder'>CREATE FOLDER</a>
		<div class='col-md-12'>
			<div class="col-md-5">
				@foreach ($files as $file)
					<div class='list_files col-md-12 vertical-alighn'>
						<a  href="{{ url('files') }}?f={{ $file['file_name'] }}" 
							class="display-cell alert @if($file['type']!=='folder') alert-success @else alert-info @endif col-md-10" 
							role="alert">
							{{ $file['file_name'] }}
						</a>
						@if($config['is_ajax'])
							<button file="{{ $file['id'] }}" id='delete_file' 
									class='btn btn-danger col-md-1 glyphicon glyphicon-remove'></button>
						@else
							<button  type='submit' 
								href="{{ url('files/delete') }}?id={{ $file['id'] }}"
								class='btn btn-danger col-md-1 glyphicon glyphicon-remove'></button>
						@endif
					</div>
				@endforeach
			</div>
		</div>
		<!--
		<table class='table'>
			<thead>
				<tr>
					<td>Имя файла</td>
					<td>Ссылка на файл</td>
					<td>Размер файла</td>
				</tr>
			</thead>
			<tbody>
				@foreach ($files as $file)
					<tr file="{{ $file['id'] }}">
						<td>{{ $file['file_name'] }}</td>
						<td>{{ $file['href'] }}/{{ $file['file_name'] }}</td>
						<td>{{ $file['filesize'] }}</td>
						@if($config['is_ajax'])
							<td id='delete_file' style='cursor: pointer' class='btn btn-danger'>Удалить</td>
						@else
							<td id='delete_file' style='cursor: pointer'>
								<a  type='submit' 
									href="{{ url('users/files/delete') }}?id={{ $file['id'] }}"
									class='btn btn-danger'>Удалить</a>
							</td>
						@endif
					</tr>
				@endforeach
			</tbody>
		</table>
		-->
		<div class='shadow'></div>
		<div class='conteiner'></div>
	</div>
	<style>
		#files .create_folder{
			margin-top: 10px;
			margin-bottom: 10px;
		}
		
		.vertical-alighn{
			display: flex;
			align-items: center;
		}
		
		#files .alert{
			margin-top: 10px;
			margin-bottom: 10px;
		}
		
		#files .list_files button{
			margin-left: 15px;
		}
		
		#files .shadow{
			position: fixed;
			top: 0px;
			left: 0px;
			height: 100%;
			width: 100%;
			z-index: 10;
			background-color: grey;
			opacity: 0.3;
			display: none;
		}
		
		#files .conteiner{
			position: fixed;
			z-index: 11;
			display: none;
			background-color: white;
			border-radius: 5px;
		}
		
		#files .conteiner a{
			margin-top: 10px;
			margin-right: 10px;
		}
	</style>
@endsection


@section('javascript')
	@if($config['customieze_input_file'])
		@include($config['customieze_input_file'])
	<script type='text/javascript'>
	@else
	<script type='text/javascript'>
		$("#files input[type=file]").fileinput({
			@if($config['is_ajax'])
				
			uploadUrl: "{{ url('files/save') }}", // server upload action
			uploadAsync: true,
			ajaxSettings:{
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			},
			uploadExtraData : {
				f : '{{ $folder }}'
			},
			showPreview: false
			
			@endif
		});
	@endif
	
		$(document).ready(function(){
			@if($config['is_ajax'])
				$("#files").on('click','#delete_file',function(){
					if(confirm("Удалить файл?")){
						var obj=$(this);
						var id=$(obj).attr('file')
						$.ajax({
							url:"{{ url('files/delete') }}",
							headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
							},
							data:{
								id : id,
							},
							success:function(e){
								if(e=='false'){
									$('#files button[file='+id+']').parent().remove();
								}
							}
						})
					}
				})
			@endif
			
			$('#files .shadow').click(function(){
				if($(this).css('display')=='none'){
					$(this).css('display','block');
					$('#files .conteiner').html("");
					$('#files .conteiner').css('display','block');
				}else{
					$(this).css('display','none');
					$('#files .conteiner').css('display','none');
				}
			})
			
			$('.create_folder').click(function(){
				$('#files .shadow').click()
				$('#files .conteiner').css('padding','20px');
				$('#files .conteiner').css('width','60%')
				$('#files .conteiner').css('top','20%');
				$('#files .conteiner').css('left','20%');
				$('#files .conteiner').html('<input type="text" class="form-control" placeholder="Folder name" aria-describedby="basic-addon1">');
				$('#files .conteiner input').after('<a class="btn btn-info">Create</a>')
				$('#files .conteiner a').after('<a class="btn btn-danger">Close</a>')
			})
			
			$('#files .conteiner').on('click','.btn-info',function(){
				$.ajax({
					url: "{{ url('files/folder/create') }}",
					data:{
						foldername : $('#files .conteiner input').val(),
						f : '{{ $folder }}'
					},
					success: function(e){
						
						$('#files .shadow').click()
					}
				})
			})
		})
	</script>
@endsection