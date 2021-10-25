<!DOCTYPE html>
<html>
<head>
	<title>nyota</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=1024">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" type="image/png" href="{{ asset('/public/css/noyta-app/images/favicon.png') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('/public/css/noyta-app/css/bootstrap.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('/public/css/noyta-app/css/style.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('/public/css/noyta-app/css/roses.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('/public/css/noyta-app/css/terms.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('/public/css/noyta-app/css/contact.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('/public/css/noyta-app/css/gallery.css') }}">
	<link rel="stylesheet" href="{{ asset('/public/css/noyta-app/css/owl.carousel.min.css') }}">
	<link rel="stylesheet" href="{{ asset('/public/css/noyta-app/css/aos.css') }}">
	<link rel="stylesheet" href="{{ asset('/public/css/noyta-app/css/all.min.css') }}">
</head>
<body>
	<!-- main body start -->
	<div class="wrapper" id="edumain">
		<!-- header  -->

		<!-- navbar start -->
			<nav class="navbar navbar-expand-md navbar-dark" id="navbar">
				<div class="container">
						<a class="navbar-brand" href="{{URL::to('')}}/privacypolicy" target="_blank"><p>Politique de confidentialité</p></a>
					  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
					    <span class="fb"><i class="fas fa-bars"></i></span>
					  </button>
					  <div class="collapse navbar-collapse justify-content-end" id="collapsibleNavbar">
					  	<div class="headsocial">
					  		<h3 class="head">Suivez-nous sur</h3>
					    <ul class="navbar-nav">
					      <li class="nav-item">
					        <a class="nav-link" href="#" ><i class="fab fa-facebook"></i></a>
					      </li>
					      <li class="nav-item">
					        <a class="nav-link" href="#" ><i class="fab fa-linkedin"></i></a>
					      </li>
					      <li class="nav-item">
					        <a class="nav-link" href="#" ><i class="fab fa-whatsapp"></i></a>
					      </li> 
					    </ul>
					  	</div>
					  	
					  </div> 
				</div>
			</nav>
		<!-- navbar end -->



		<div class="container">
		<!-- first section start -->
				<div class="cmn-cls">
					<div class="frst-section">
					<img src="{{ asset('/public/css/noyta-app/images/logo.png') }}" class="mobb3">
					<h1 class="text-center nyota"><span>NYOTA </span>L’APPLICATION MOBILE</h1>
					<h1 class="text-center nyota">QUI FERA VOS COURSES AUSSI BIEN QUE VOUS</h1>
					<h3 class="text-center bien mt-4 mb-5"><i>Bientôt disponible sur</i></h3>
					<div class="row">
						<div class="col-md-3"></div>
						<div class="col-md-3 ">
							<center>
								<a href="#">
									<img src="{{ asset('/public/css/noyta-app/images/button1.png') }}" class="img-wdth">
								</a>
							</center>
						</div>
						<div class="col-md-3 mobb2">
							<center>
								<a href="#">
								<img src="{{ asset('/public/css/noyta-app/images/button2.png') }}" class="img-wdth">
								</a>
							</center>
						</div>
						<div class="col-md-3"></div>
					</div>
					</div>
				</div>
		<!-- first section end -->

		<!-- second section start -->
			<div class="cmn-cls">
				<div class="row margn">
					<div class="col-md-6">
						<img src="{{ asset('/public/css/noyta-app/images/side1.png') }}" style="width: 100%;">
					</div>
					<div class="col-md-6 mobb">
						<div class="card">
							<div class="card-body my-4">
								<h5 class="soyez mb-4">Soyez les premiers  à en profiter en nous laissons vos coordonnés et <span >bénéficier de nombreuses avantages exclusifs!</span></h5>
								<h5></h5>
								<span class="success" id="success"></span>
								{!! Form::open(array('route' => 'sendmail', 'method' => 'POST', 'role' => 'form','id' => 'myform', 'class' => 'needs-validation')) !!}
								{!! csrf_field() !!}
								    <div class="form-group">
								      <label>Prénom</label>
								      <input type="text" class="form-control" id="first_name" name="first_name">
								      <strong class="invalid-feedback" id="error-first_name"></strong>
								    </div>
								    <div class="form-group">
								      <label>Numéro de portable</label>
								      <input type="text" class="form-control" id="pswd" name="pswd">
								      <strong class="invalid-feedback" id="error-pswd"></strong>
								    </div>
								    <div class="form-group">
								      <label>Email</label>
								      <input type="email" class="form-control" id="email" name="email">
								      <strong class="invalid-feedback" id="error-email"></strong>
								    </div>
								    <center><button type="submit" class="btn btn-bg mt-4 ">Envoyez</button></center>
								{!! Form::close() !!}
							</div>	
						</div>
					</div>
				</div>
			</div>

		<!-- second section end -->

		<!-- third image background start -->

			<div class="cmn-cls">
				<div class="row margn">
					<div class="col-md-4">
						<h3 class="dispon text-center">Disponibilités des produits</h3>
						<div class="card">
							<div class="card-body card-height">
								<p class="plus">Plus la peine de faire le tour dessupermarchés et autres commerces de la ville pour réunir les produits dont vous avez besoin. NYOTA secharge de vous les mettre à portée de téléphone.</p>
							</div>
						</div>
					</div>
					<div class="col-md-4 mobb">
						<h3 class="dispon text-center">Géolocalisation inApp</h3>
						<div class="card card-height">
							<div class="card-body">
								<p class="plus">Grace à la fonction de géolocalisation intégrée à l’application NYOTA, en un clic vous pouvez indiquer l’endroit souhaité pour votre livraison. Notre système d’optimisation des tournées de livraisons vous proposera par la suite des créneaux horaires à valider.</p>
							</div>
						</div>
					</div>
					<div class="col-md-4 mobb">
						<h3 class="dispon text-center">Option de paiement</h3>
						<div class="card">
							<div class="card-body card-height">
								<p class="plus">Vous aurez le choix entre le mobile money (AIRTEL et MTN), le mobile banking (BGFIMobile, ECOBANKPay et LCBPay) ou encore le traditionnel paiement en cash à la livraison </p>
							</div>
						</div>
					</div>
				</div>
			</div>

		<!-- third image background start -->

		</div>
	<!-- footer -->

		<footer class="cmn-cls">
			<div class="footer margn"></div>
		</footer>
	</div>
	
	<script src="{{ asset('/public/css/noyta-app/js/jquery.min.js') }}"></script>
	<script src="{{ asset('/public/css/noyta-app/js/bootstrap.min.js') }}"></script>
	<script src="{{ asset('/public/css/noyta-app/js/aos.js') }}"></script>
	<script src="{{ asset('/public/css/noyta-app/js/owl.carousel.min.js') }}"></script>
	
	<script type="text/javascript">
		$(document).ready(function() {

			if ($(window).width() > 1024) 
			{
				$(".dashboard").css("height",($(window).height())+'px');			}
			else{
				$('.dashboard').css('height','auto');
			}
			
		});
	</script>
	<script>
		$(window).on("scroll", function() {
		    if($(window).scrollTop() > 50) {
		        $("#navbar").addClass("sticky");
		    } else {
		       $("#navbar").removeClass("sticky");
		    }
		});
	</script>
	<script>
      AOS.init({
        easing: 'ease-in-out-sine'
      });
    </script>

    <script>
        jQuery(document).ready(function()  {
            $('.loop').owlCarousel({
                items: 2,
                loop: true,
                nav: false,
                autoplay: true,
                autoplayTimeout: 5000,
                autoplayHoverPause: false,
                margin: 10,
                responsive: {
                    0: {
                        items: 1
                    },
                    600: {
                        items: 2
                    },
                    1000: {
                        items: 4
                    }
                }
            });
            })
          </script>
          <script type="text/javascript">
		$(document).ready(function() {
			$( "#myform" ).submit(function( event ) {
		        event.preventDefault();
		        var form = $(this);
		        var data = new FormData($(this)[0]);
		        var url = form.attr("action");
		        $.ajax({
		            type: form.attr('method'),
		            url: url,
		            data: data,
		            cache: false,
		            contentType: false,
		            processData: false,
		            success: function (data) {
		                $('.is-invalid').removeClass('is-invalid');
		                if (data.fail) {
		                    for (control in data.errors) {
		                       	$('#'+control).addClass('is-invalid');
	                            $('#error-' + control).html(data.errors[control]);
		                    }
		                }
		                else {
		                    $('#success').html('<p class="alert alert-success">Votre courrier a été envoyé avec succès.</p>');
		                    $('#first_name').val('');
		                    $('#pswd').val('');
		                    $('#email').val('');
		                }
		            },
		            error: function (xhr, textStatus, errorThrown) {
		              alert("Error: " + errorThrown);
		            }
		        });
		        return false;
		    });
		});
	</script>
</body>
</html>