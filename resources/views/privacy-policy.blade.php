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
  <style type="text/css">
    p {
        margin-top: 0;
        margin-bottom: 0.5rem;
    }
  </style>
</head>
<body>
  <!-- main body start -->
  <div class="wrapper" id="edumain">
    <!-- header  -->

    <!-- navbar start -->
      <nav class="navbar navbar-expand-md navbar-dark" id="navbar">
        <div class="container">
            
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
              <span class="fb"><i class="fas fa-bars"></i></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="collapsibleNavbar">
             <div class="headsocial">
                <h3 class="" style="text-align: center;">Politique de confidentialité</h3>
              
              </div>
              
            </div> 
        </div>
      </nav>
    <!-- navbar end -->



    <div class="container">
    <!-- first section start -->
        
    <!-- first section end -->

    <!-- second section start -->
      

    <!-- second section end -->

    <!-- third image background start -->

        <div style="padding-top: 100px;">
          {!! $pages->contain !!}
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
        $(".dashboard").css("height",($(window).height())+'px');      }
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
