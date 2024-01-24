<!-- Bootstrap core JavaScript-->
<script src="<?= base_url() ?>_vendor/jquery/jquery.min.js?v=<?= time(); ?>"></script>
    <script src="<?= base_url() ?>_vendor/bootstrap/js/bootstrap.bundle.min.js?v=<?= time(); ?>"></script>

    <!-- Core plugin JavaScript-->
    <script src="<?= base_url() ?>_vendor/jquery-easing/jquery.easing.min.js?v=<?= time(); ?>"></script>

    <!-- Custom scripts for all pages-->
    <script src="<?= base_url() ?>js/sb-admin-2.min.js?v=<?= time(); ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11?v=<?= time(); ?>"></script>
    <script>
        $(document).ready(function () {
            var validator = $(".login-form").validate({
                rules: {
                    email: {
                        email: true,
                        required: true
                    },
                    password: {
                        required: true
                    }
                },
                messages: {
                    email: {
                        email: "Email Tidak Valid",
                        required: "Email Harus Diisi"
                    },
                    password: {
                        required: "Password Harus Diisi"
                    }
                },
                errorElement: 'label',
                errorClass: 'text-danger',
                errorPlacement: function (error, element) {
                    if (element.parent('.input-group').length) {
                        error.insertAfter(element.parent());
                    } else {
                        error.insertAfter(element);
                    }
                }
            });

            $('.email, .password').keypress(function (e) {
                var key = e.which;
                if(key == 13)  // the enter key code
                {
                    if ($(".login-form").valid()) {
                        setLoadingLogin()
                        $.ajax({
                            url : "<?= base_url("login"); ?>",
                            type: "POST",
                            dataType: "json",
                            cache: false,
                            data: $(".login-form").serialize(),
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    }).then((responseSuccess) => {
                                        if (responseSuccess.isConfirmed) {
                                            window.location.href = "/home";
                                        }
                                    })
                                } else {
                                    stopLoadingLogin()
                                    Swal.fire({
                                        icon: 'error',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    })
                                }
                            },
                            onError: function(err) {
                                stopLoadingLogin()
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Data Gagal Disimpan',
                                    confirmButtonColor: '#4e73df',
                                })
                            }
                        })
                    }
                }
            }); 

            $(".btn-login-register").click(function() {
                if ($(".login-form").valid()) {
                    setLoadingLogin()
                    $.ajax({
                        url : "<?= base_url("login"); ?>",
                        type: "POST",
                        dataType: "json",
                        cache: false,
                        data: $(".login-form").serialize(),
                        success: function(response) {
                            if (response.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                }).then((responseSuccess) => {
                                    if (responseSuccess.isConfirmed) {
                                        window.location.href = "/home";
                                    }
                                })
                            } else {
                                stopLoadingLogin()
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                            }
                        },
                        onError: function(err) {
                            stopLoadingLogin()
                            Swal.fire({
                                icon: 'error',
                                title: 'Data Gagal Disimpan',
                                confirmButtonColor: '#4e73df',
                            })
                        }
                    })
                }
            })
        })
    </script>
</body>

</html>