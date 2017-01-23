@extends('layouts.basic')

@section('view_scripts')
<!-- <script src="{{ asset("Pratt/assets/js/bootstrap.js") }}"></script> -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDiUuDlNj73HAMVk4KB2tswmVlLlOSAEjs" async defer></script>
<script src="/js/roadassist.js"></script>
@endsection

@section('content')
	<div class="row centered">
		<div class="col-lg-12">
			<h1>Welcome To <b>GoTire</b></h1>
			<h3>Stuck? We are here to help.</h3>
			<br>
		</div>
		
		<div class="col-lg-2">
			<h5>Amazing Results</h5>
			<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
			<img class="hidden-xs hidden-sm hidden-md" src="{{ asset("Pratt/assets/img/arrow1.png") }}">
		</div>
		<div class="col-lg-8">
			<!-- <img class="img-responsive" src="{{ asset("Pratt/assets/img/app-bg.png") }}" alt=""> -->
			<!-- <router-link to="/services"><h1>Our company</h1></router-link> -->
			<div id="roadassist"></div>
		</div>
		<div class="col-lg-2">
			<br>
			<img class="hidden-xs hidden-sm hidden-md" src="{{ asset("Pratt/assets/img/arrow2.png") }}">
			<h5>Awesome Design</h5>
			<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
		</div>
	</div>
@endsection