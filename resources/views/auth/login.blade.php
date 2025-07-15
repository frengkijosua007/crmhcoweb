<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Hansen Construction CRM</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --hansen-navy: #0a1155;
            --hansen-light-navy: #1a2165;
            --hansen-gold: #e3b04b;
            --hansen-light-gray: #f5f6fa;
            --hansen-medium-gray: #e0e0e0;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--hansen-navy) 0%, var(--hansen-light-navy) 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            position: relative;
            background-color: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
            padding: 0;
        }

        .login-card {
            padding: 2.5rem;
            border: none;
            border-radius: 0;
            background-color: white;
        }

        .login-sidebar {
            background-color: var(--hansen-light-gray);
            position: relative;
            height: 100%;
            padding: 2.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .login-sidebar::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('https://cdnjs.cloudflare.com/ajax/libs/simple-icons/8.15.0/simpleicons.svg');
            background-size: cover;
            opacity: 0.03;
            z-index: 0;
        }

        .login-sidebar .content {
            position: relative;
            z-index: 1;
        }

        .logo-container {
            margin-bottom: 2rem;
            text-align: center;
        }

        .logo {
            max-width: 220px;
            margin-bottom: 1.5rem;
        }

        .building-icon {
            position: absolute;
            bottom: 0;
            right: 0;
            font-size: 180px;
            color: rgba(10, 17, 85, 0.03);
            transform: translateY(30%);
        }

        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            border: 1px solid var(--hansen-medium-gray);
            background-color: var(--hansen-light-gray);
            transition: all 0.3s;
        }

        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(10, 17, 85, 0.1);
            border-color: var(--hansen-navy);
            background-color: white;
        }

        .input-group .btn {
            border-top-right-radius: 8px;
            border-bottom-right-radius: 8px;
            padding: 12px 15px;
            background-color: var(--hansen-light-gray);
            border: 1px solid var(--hansen-medium-gray);
            border-left: none;
        }

        .btn-primary {
            background-color: var(--hansen-navy);
            border-color: var(--hansen-navy);
            border-radius: 8px;
            padding: 12px 20px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background-color: var(--hansen-light-navy);
            border-color: var(--hansen-light-navy);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(10, 17, 85, 0.2);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .form-check-input:checked {
            background-color: var(--hansen-navy);
            border-color: var(--hansen-navy);
        }

        .form-label {
            font-weight: 500;
            color: #555;
        }

        .login-title {
            color: var(--hansen-navy);
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .login-subtitle {
            color: #666;
            margin-bottom: 2rem;
        }

        .blueprint-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: repeating-linear-gradient(0deg, rgba(10, 17, 85, 0.03) 0px, rgba(10, 17, 85, 0.03) 1px, transparent 1px, transparent 20px),
                              repeating-linear-gradient(90deg, rgba(10, 17, 85, 0.03) 0px, rgba(10, 17, 85, 0.03) 1px, transparent 1px, transparent 20px);
            z-index: 0;
        }

        .demo-accounts {
            background-color: var(--hansen-light-gray);
            border-radius: 8px;
            padding: 1.25rem;
            margin-top: 2rem;
            position: relative;
            overflow: hidden;
        }

        .demo-accounts::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--hansen-navy), var(--hansen-light-navy));
        }

        .demo-title {
            font-weight: 600;
            color: var(--hansen-navy);
            margin-bottom: 0.75rem;
        }

        .demo-account {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }

        .demo-account:last-child {
            margin-bottom: 0;
        }

        .demo-role {
            font-weight: 600;
            margin-right: 0.5rem;
            color: #555;
            min-width: 75px;
        }

        .demo-creds {
            color: #666;
        }

        .forgot-link {
            color: var(--hansen-navy);
            text-decoration: none;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .forgot-link:hover {
            color: var(--hansen-light-navy);
            text-decoration: underline;
        }

        /* Construction Elements */
        .construction-element {
            position: absolute;
            z-index: 0;
            opacity: 0.05;
            color: var(--hansen-navy);
        }

        .crane {
            top: 5%;
            right: 5%;
            font-size: 40px;
            transform: rotate(15deg);
        }

        .helmet {
            bottom: 10%;
            left: 10%;
            font-size: 35px;
        }

        .ruler {
            top: 15%;
            left: 5%;
            font-size: 30px;
            transform: rotate(-30deg);
        }

        .truck {
            bottom: 7%;
            right: 15%;
            font-size: 35px;
            transform: rotate(-10deg);
        }

        .alert-error {
            background-color: #fff1f1;
            color: #e53e3e;
            border-left: 4px solid #e53e3e;
            border-radius: 4px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 991.98px) {
            .login-sidebar {
                padding: 2rem 1.5rem;
            }

            .login-card {
                padding: 2rem 1.5rem;
            }
        }

        @media (max-width: 767.98px) {
            .login-container {
                margin: 1rem;
            }

            .login-sidebar {
                display: none;
            }
        }

        /* Animated elements */
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .blueprint-element {
            position: absolute;
            z-index: 0;
        }

        .building-sketch {
            bottom: -20px;
            right: 20px;
            width: 120px;
            height: 180px;
            border: 2px solid rgba(10, 17, 85, 0.1);
            border-radius: 5px;
            background: rgba(245, 246, 250, 0.5);
        }

        .building-sketch::before,
        .building-sketch::after {
            content: "";
            position: absolute;
            background: rgba(10, 17, 85, 0.1);
        }

        .building-sketch::before {
            top: 30px;
            left: 20px;
            width: 80px;
            height: 120px;
        }

        .building-sketch::after {
            top: 60px;
            left: 40px;
            width: 40px;
            height: 60px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100 py-5">
            <div class="col-lg-10 col-xl-9">
                <div class="login-container row m-0">
                    <!-- Login Form Side -->
                    <div class="col-md-7 p-0">
                        <div class="login-card h-100 d-flex flex-column">
                            <!-- Construction Elements -->
                            <i class="bi bi-truck construction-element crane"></i>
                            <i class="bi bi-hard-hat construction-element helmet"></i>
                            <i class="bi bi-rulers construction-element ruler"></i>
                            <i class="bi bi-building construction-element truck"></i>

                            <div class="position-relative flex-grow-1 d-flex flex-column">
                                <div class="text-center d-md-none mb-4">
                                    <img src="{{ asset('images/logo.png') }}" alt="Hansen Construction" class="logo" style="max-width: 200px;">
                                </div>

                                <h3 class="login-title">Welcome Back</h3>
                                <p class="login-subtitle">Silakan login untuk melanjutkan</p>

                                <div class="alert-error d-none">
                                    <i class="bi bi-exclamation-circle me-2"></i>
                                    <span class="error-message">Invalid email or password. Please try again.</span>
                                </div>

                                <form class="flex-grow-1 d-flex flex-column" method="POST" action="{{ route('login') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            id="email" name="email" placeholder="Enter your email" required
                                            value="{{ old('email') }}" autofocus>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label for="password" class="form-label">Password</label>
                                            @if (Route::has('password.request'))
                                                <a href="{{ route('password.request') }}" class="forgot-link">Forgot Password?</a>
                                            @endif
                                        </div>
                                        <div class="input-group">
                                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                                id="password" name="password" placeholder="Enter your password" required>
                                            <button class="btn" type="button" id="togglePassword">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-check mb-4">
                                        <input class="form-check-input" type="checkbox" id="remember"
                                            name="remember" {{ old('remember') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="remember">
                                            Remember me
                                        </label>
                                    </div>

                                    <div class="mt-auto">
                                        <button type="submit" class="btn btn-primary w-100 d-flex align-items-center justify-content-center">
                                            <i class="bi bi-box-arrow-in-right me-2"></i>
                                            <span>Log in</span>
                                        </button>
                                    </div>
                                </form>

                                <div class="demo-accounts">
                                    <h6 class="demo-title">Demo Accounts</h6>
                                    <div class="demo-account">
                                        <span class="demo-role">Admin:</span>
                                        <span class="demo-creds">admin@hansen.com / password123</span>
                                    </div>
                                    <div class="demo-account">
                                        <span class="demo-role">Manager:</span>
                                        <span class="demo-creds">manager@hansen.com / password123</span>
                                    </div>
                                    <div class="demo-account">
                                        <span class="demo-role">Marketing:</span>
                                        <span class="demo-creds">marketing@hansen.com / password123</span>
                                    </div>
                                    <div class="demo-account">
                                        <span class="demo-role">Surveyor:</span>
                                        <span class="demo-creds">surveyor@hansen.com / password123</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar with Logo -->
                    <div class="col-md-5 p-0 d-none d-md-block">
                        <div class="login-sidebar">
                            <div class="blueprint-bg"></div>
                            <div class="blueprint-element building-sketch"></div>

                            <div class="content">
                                <div class="logo-container">
                                    <img src="{{ asset('images/logo.png') }}" alt="Hansen Construction" class="logo">
                                    <p class="text-muted">CRM System</p>
                                </div>

                                <div class="d-none d-lg-block">
                                    <div class="card border-0 bg-white shadow-sm mb-3">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-shield-check text-primary me-3" style="font-size: 1.5rem;"></i>
                                                <div>
                                                    <h6 class="mb-1">Secure Access</h6>
                                                    <p class="text-muted mb-0 small">Employee portal for Hansen team</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card border-0 bg-white shadow-sm">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-building text-primary me-3" style="font-size: 1.5rem;"></i>
                                                <div>
                                                    <h6 class="mb-1">Project Management</h6>
                                                    <p class="text-muted mb-0 small">Monitor construction projects</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
    </script>
</body>
</html>
