
<!DOCTYPE html>
<html class="my-top-class">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    {{-- <link rel="icon" href="assets/img/basic/favicon.ico" type="image/x-icon"> --}}
    <title>Login</title>
    <!-- CSS -->
    @vite(['resources/sass/app.scss', 'resources/css/theme.css', 'resources/css/style.css', 'resources/js/app.js'])

</head>
<body class="light sidebar-mini sidebar-collapse">
<!-- Pre loader -->
<div id="app">
    <div id="primary" class="blue4 p-t-b-100 height-full responsive-phone">
        <div class="container">
            <div class="row">
                {{-- <div class="col-lg-6">
                    <img src="assets/img/icon/icon-plane.png" alt="">
                </div> --}}
                <div class="col-lg-6 p-t-100 offset-sm-3">
                    <div class="text-white">
                        <h1>Welcome Back</h1>
                        <p class="s-18 p-t-b-20 font-weight-lighter">Hey, welcome back signin to order management system</p>
                    </div>
                    <form  method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group has-icon"><i class="icon-envelope-o"></i>
                                    <input type="text" id="username" class="form-control form-control-lg no-b @error('username') is-invalid @enderror @error('email') is-invalid @enderror" name="username" value="{{ old('username') }}"
                                           placeholder="Email or Username" required autocomplete="username" autofocus>
                                </div>
                               

                            </div>
                            <div class="col-lg-6">
                                <div class="form-group has-icon"><i class="icon-user-secret"></i>
                                    <input id="password" type="password" class="form-control form-control-lg no-b @error('password') is-invalid @enderror"
                                    name="password" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Password">
                                </div>
                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                             @error('username')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            {{-- <div class="row mb-6 ">
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
    
                                        <label class="form-check-label" for="remember">
                                            {{ __('Remember Me') }}
                                        </label>
                                    </div>
                                </div>
                            </div>  --}}
                            
                            <div class="col-lg-12">
                                <input type="submit" class="btn btn-success btn-lg btn-block" value="Let me enter">
                                {{-- <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button> --}}
                                {{-- <p class="forget-pass text-white">Have you forgot your username or password ?</p> --}}
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- #primary -->
<!-- Right Sidebar -->
<!-- /.right-sidebar -->
<!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
</div>
<!--/#app -->
<script>
</script>

</body>
</html>


 