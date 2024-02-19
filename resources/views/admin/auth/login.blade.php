@extends('admin.layout.auth')

@section('content')

<div class="kt-login__signin">
    <form class="kt-form" action="{{url('login')}}" method="post">
        {{csrf_field()}}
		@if ($errors->any())
            <div class="alert alert-danger fade show">
            	<div class="alert-text">{{ $errors->first() }}</div>
				<div class="alert-close">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="la la-close"></i></span></button>
				</div>
            </div>
        @endif
        <div class="input-group">
            <input class="form-control" type="text" placeholder="User Name" name="user_name" autocomplete="off">
        </div>
		<div class="input-group">
			<input class="form-control" type="password" placeholder="Password" name="password">
		</div>
        <!--
		<div class="row kt-login__extra">
			<div class="col">
				<label class="kt-checkbox">
					<input type="checkbox" name="remember"> Remember me
					<span></span>
				</label>
			</div>
			<div class="col kt-align-right">
				<a href="javascript:;" id="kt_login_forgot" class="kt-link kt-login__link">Forget Password ?</a>
			</div>
		</div>
        -->
		<div class="kt-login__actions">
			<button type="submit" class="btn btn-pill kt-login__btn-primary">Sign In</button>
		</div>
	</form>
</div>
<div class="kt-login__forgot">
    <div class="kt-login__head">
	    <h3 class="kt-login__title">Forget Password ?</h3>
		<div class="kt-login__desc">Enter your user name to reset your password:</div>
	</div>
	<form class="kt-form" action="{{url('')}}" method="post">
        {{csrf_field()}}
		<div class="input-group">
		    <input class="form-control" type="text" placeholder="User Name" name="user_name" autocomplete="off">
		</div>
		<div class="kt-login__actions">
			<button id="kt_login_forgot_submit" class="btn btn-pill kt-login__btn-primary">Send</button>&nbsp;&nbsp;
			<button id="kt_login_forgot_cancel" class="btn btn-pill kt-login__btn-secondary">Cancel</button>
		</div>
	</form>
</div>
@endsection
