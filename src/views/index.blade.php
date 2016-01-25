@extends('layouts.main')

@section('content')
	<div class='col-md-4'>
		<div class="input-group">
			<span class="input-group-addon" id="basic-addon1">First name</span>
			<input value='{{ $user['first_name'] }}' type="text" class="form-control" placeholder="Username" aria-describedby="basic-addon1">
		</div>
		
		<div class="input-group">
			<span class="input-group-addon" id="basic-addon1">Last name</span>
			<input value='{{ $user['last_name'] }}' type="text" class="form-control" placeholder="Username" aria-describedby="basic-addon1">
		</div>
		
		<div class="input-group">
			<span class="input-group-addon" id="basic-addon1">Login</span>
			<input value='{{ $user['username'] }}' type="text" class="form-control" placeholder="Username" aria-describedby="basic-addon1">
		</div>
		
		<div class="input-group">
			<span class="input-group-addon" id="basic-addon1">Email</span>
			<input value='{{ $user['email'] }}' type="text" class="form-control" placeholder="Username" aria-describedby="basic-addon1">
		</div>
	</div>
@endsection