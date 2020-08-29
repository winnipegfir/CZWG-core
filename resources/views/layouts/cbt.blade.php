<html lang="en">
<body>
  <!-- JavaScripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script type="text/javascript" src="{{asset('js/flipclock.min.js')}}"></script>
{{-- <script src="{{ elixir('js/app.js') }}"></script> --}}
<script src="{{asset('js/sweetalert.min.js')}}"></script>
<script>
    $('div.alert-success').delay(3000).slideUp(400);
    $(function(){
    $('a#btn-delete').on('click', function(e){
        e.preventDefault();
        e.stopPropagation();
        var $a = this;
        swal({
                    title: "Are you sure?",
                    text: "You will not be able to recover this category!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: '#DD6B55',
                    confirmButtonText: 'Yes, delete it!',
                    closeOnConfirm: false
                },
                function(){
                    //console.log($($a).attr('href'));
                    document.location.href=$($a).attr('href');
                });
    });
    });
    $('#add-new-question').hide();
    $('#btn-add-new-question').on('click', function(){
        $('#add-new-question').slideDown();
    });
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        }
    });
    @yield('script_clock')
    @yield('script_form')
</script>
</body>
</html>
