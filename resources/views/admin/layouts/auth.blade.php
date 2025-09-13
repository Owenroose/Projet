<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light-style customizer-hide" dir="ltr"
    data-theme="theme-default" data-assets-path="{{ asset('assets/') }}/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'Novatech Bénin') }}</title>

    <meta name="description" content="" />

    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}" />

    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>

    <style>
        body {
            font-family: 'Inter', 'Public Sans', sans-serif;
            background: linear-gradient(135deg, #f0f4f8 0%, #e2e8f0 100%);
            min-height: 100vh;
        }

        .authentication-wrapper {
            background: transparent;
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .auth-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }

        .logo-container {
            background: linear-gradient(135deg, #0077b6 0%, #00b4d8 100%);
            padding: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .logo-container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: repeating-linear-gradient(
                45deg,
                transparent,
                transparent 10px,
                rgba(255, 255, 255, 0.05) 10px,
                rgba(255, 255, 255, 0.05) 20px
            );
            animation: shimmer 20s linear infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%) translateY(-100%); }
            100% { transform: translateX(100%) translateY(100%); }
        }

        .logo-svg {
            width: 120px;
            height: 60px;
            margin: 0 auto 1rem;
            position: relative;
            z-index: 2;
        }

        .brand-text {
            font-family: 'Inter', sans-serif;
            font-weight: 700;
            font-size: 2rem;
            color: white;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 2;
        }

        .brand-subtitle {
            font-family: 'Inter', sans-serif;
            font-weight: 300;
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.9);
            position: relative;
            z-index: 2;
        }

        .flag-elements {
            position: absolute;
            bottom: -10px;
            right: -10px;
            opacity: 0.3;
            z-index: 1;
        }

        .flag-block {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            margin: 2px;
            display: inline-block;
        }

        .flag-green { background-color: #2d6a4f; }
        .flag-yellow { background-color: #fbb040; }
        .flag-red { background-color: #d9534f; }

        .form-container {
            padding: 2.5rem;
        }

        .form-title {
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            font-size: 1.5rem;
            color: #1a202c;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .form-subtitle {
            font-family: 'Inter', sans-serif;
            font-weight: 400;
            color: #6b7280;
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-floating {
            margin-bottom: 1.5rem;
        }

        .form-control {
            border-radius: 12px;
            border: 2px solid #e5e7eb;
            padding: 0.875rem 1rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #0077b6;
            box-shadow: 0 0 0 3px rgba(0, 119, 182, 0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, #0077b6 0%, #00b4d8 100%);
            border: none;
            border-radius: 12px;
            padding: 0.875rem 2rem;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 119, 182, 0.3);
        }

        .fade-in {
            animation: fadeIn 1s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .floating {
            animation: floating 6s ease-in-out infinite;
        }

        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        @media (max-width: 768px) {
            .auth-card {
                margin: 1rem;
                border-radius: 16px;
            }

            .logo-container {
                padding: 1.5rem;
            }

            .brand-text {
                font-size: 1.75rem;
            }

            .form-container {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <div class="card auth-card fade-in">
                    <!-- Header avec logo -->
                    <div class="logo-container">
                        <div class="flag-elements">
                            <div class="flag-block flag-green"></div>
                            <div class="flag-block flag-yellow"></div>
                            <div class="flag-block flag-red"></div>
                        </div>

                        <svg class="logo-svg floating" viewBox="0 0 200 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Main logo shape -->
                            <path d="M70 10 C65 20 60 40 70 50 C80 60 90 70 100 70 C110 70 120 60 130 50 C140 40 135 20 130 10 C125 0 115 0 100 0 C85 0 75 0 70 10 Z"
                                  fill="rgba(255, 255, 255, 0.9)"
                                  class="drop-shadow-lg"/>

                            <!-- "N" letter -->
                            <path d="M80 15 L90 15 L100 45 L110 15 L120 15 L100 65 L80 15 Z"
                                  fill="#0077b6"/>
                        </svg>

                        <div class="brand-text">NOVA TECH</div>
                        <div class="brand-subtitle">Bénin</div>
                    </div>

                    <!-- Contenu du formulaire -->
                    <div class="form-container">
                        @yield('form-title')
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <script>
        // Animation d'entrée retardée pour les éléments du formulaire
        document.addEventListener('DOMContentLoaded', function() {
            const formElements = document.querySelectorAll('.form-floating, .btn');
            formElements.forEach((element, index) => {
                element.style.opacity = '0';
                element.style.transform = 'translateY(20px)';

                setTimeout(() => {
                    element.style.transition = 'all 0.6s ease';
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }, 100 * (index + 1));
            });
        });

        // Effet de particules subtil au survol
        document.querySelector('.logo-container').addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            const particle = document.createElement('div');
            particle.style.position = 'absolute';
            particle.style.left = x + 'px';
            particle.style.top = y + 'px';
            particle.style.width = '4px';
            particle.style.height = '4px';
            particle.style.background = 'rgba(255, 255, 255, 0.6)';
            particle.style.borderRadius = '50%';
            particle.style.pointerEvents = 'none';
            particle.style.animation = 'particle-float 1s ease-out forwards';

            this.appendChild(particle);

            setTimeout(() => {
                if (particle.parentNode) {
                    particle.parentNode.removeChild(particle);
                }
            }, 1000);
        });
    </script>

    <style>
        @keyframes particle-float {
            to {
                transform: translateY(-30px);
                opacity: 0;
            }
        }
    </style>

    @stack('scripts')
</body>
</html>
