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
		@if(!$config['is_ajax'])
			<form method='post' action="{{ url('users/files/save') }}" enctype="multipart/form-data">
				<label class="control-label">Select File</label>
				<input id="input" name="input[]" type="file" class="file file-loading" multiple data-show-caption="true">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
			</form>
		@else
			<input id="input" name="input[]" type="file" class="file file-loading" multiple data-show-caption="true">
		@endif
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
	</div>
@endsection

@section('javascript')
	@if($config['customieze_input_file'])
		@include($config['customieze_input_file'])
	<script type='text/javascript'>
	@else
	<script type='text/javascript'>
			$("#files input[type=file]").fileinput({
				@if($config['is_ajax'])
					
				uploadUrl: "{{ url('users/files/save') }}", // server upload action
				uploadAsync: true,
				ajaxSettings:{
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				}
				
				@endif
			});
	@endif
	
		$(document).ready(function(){
			@if($config['is_ajax'])
				$("#files").on('click','#delete_file',function(){
					if(confirm("Удалить файл?")){
						var obj=$(this);
						var id=$(obj).parent().attr('file')
						$.ajax({
							url:"{{ url('users/files/delete') }}",
							headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
							},
							data:{
								id : id,
							},
							success:function(e){
								if(e=='false'){
									$('#files tr[file='+id+']').remove();
								}
							}
						})
					}
				})
			@endif
		})
	</script>
@endsection