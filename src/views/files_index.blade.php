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
				@if($folder!=="")
					<div class='list_files col-md-12 vertical-alighn'>
						<a  href="{{ url('files') }}?f={{ preg_replace($pattern,'',$folder) }}" 
							class="display-cell alert alert-info col-md-10" 
							role="alert">..</a>
					</div>
				@endif
				@foreach ($files as $file)
					<div class='list_files col-md-12 vertical-alighn'>
						<a  href="{{ url('files') }}?f={{ $folder.$slash.$file['filename'] }}" 
							class="display-cell alert @if($file['type']!=='folder') alert-success @else alert-info @endif col-md-10" 
							file="{{ $file['id'] }}"
							role="alert">
							{{ $file['filename'] }}
						</a>
						@if($config['is_ajax'])
							<button file="{{ $file['id'] }}" id='update_name'
									class='btn btn-warning col-md-1 glyphicon glyphicon-pencil'></button>
							<button file="{{ $file['id'] }}" id='delete_file' 
									class='btn btn-danger col-md-1 glyphicon glyphicon-trash'></button>
						@else
							<button  type='submit' 
								href="{{ url('files/delete') }}?id={{ $file['id'] }}"
								class='btn btn-danger col-md-1 glyphicon glyphicon-remove'></button>
						@endif
					</div>
				@endforeach
			</div>
		</div>
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
				$('#files').on('fileuploaded','input[type=file]', function(event, data, previewId, index) {
					var form = data.form, files = data.files, extra = data.extra,
						response = data.response, reader = data.reader;
					console.log(response);
					$('#files .list_files .alert-info:last').parent().after(
						'<div class="list_files col-md-12 vertical-alighn">'+
							'<a  href="{{ url("files") }}?f={{ $folder.$slash }}'+response['0']['filename']+'"'+
								'class="display-cell alert alert-success col-md-10"'+
								'role="alert">'+
								response['0']['filename']+
							'</a>'+
							'<button file="'+response['0']['id']+'" id="delete_file"'+
										'class="btn btn-danger col-md-1 glyphicon glyphicon-remove"></button>'+
						'</div>'
					)
				});
				
				$("#files").on('click','#update_name',function(){
					var old_name=$(this).parent().find('a').html();
					var file_id=$(this).attr('file')
					
					old_name=old_name.replace(/^\s+|\s+$/g,'');
					
					$('#files .shadow').click()
					$('#files .conteiner').css('padding','20px');
					$('#files .conteiner').css('width','60%')
					$('#files .conteiner').css('top','20%');
					$('#files .conteiner').css('left','20%');
					$('#files .conteiner').html('<input type="text" value="'+old_name+'" class="form-control" aria-describedby="basic-addon1">');
					$('#files .conteiner input').after('<a class="btn btn-info" old_name="'+old_name+'" b="update" file="'+file_id+'">Update</a>')
					$('#files .conteiner a').after('<a class="btn btn-danger">Close</a>')
				})
				
				$('#files .conteiner').on('click','.btn-info[b=update]',function(){
					if(confirm('Переименовать?')){
						var file_id=$(this).attr('file');
						var old_name=$(this).attr('old_name');
						var new_name=$('#files .conteiner input').val();
						$.ajax({
							url: "{{ url('files/update') }}",
							data:{
								name : new_name,
								id : file_id
							},
							success: function(e){
								$('#files a[file='+file_id+']').html(new_name)
								
								var url = $('#files a[file='+file_id+']').attr('href')
								
								var pattern=new RegExp('\\?f=[a-zа-я0-9_\/\s]*' + old_name + '[a-zа-я0-9_\/\s]*$','i');
								
								e="?f="+JSON.parse(e).replace(/^\//,'');
								
								var url2= url.replace(pattern,e)
								
								$('#files a[file='+file_id+']').attr('href',url2)
								
								$('#files .shadow').click()
							}
						})
					}
				})
			
				$("#files").on('click','#delete_file',function(){
					if(confirm("Удалить?")){
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
				$('#files .conteiner input').after('<a class="btn btn-info" b="create">Create</a>')
				$('#files .conteiner a').after('<a class="btn btn-danger">Close</a>')
			})
			
			$('#files .conteiner').on('click','.btn-info[b=create]',function(){
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