<!DOCTYPE html>
<html lang="en" id="htmlTag" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Advanced POS') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Premium Gradient Navbar */
        .navbar-custom {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #7e22ce 100%);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
        }

        .navbar-brand {
            font-weight: 700;
            letter-spacing: 1.5px;
            font-size: 1.5rem;
            color: #fff !important;
            transition: all 0.3s ease;
        }

        .navbar-brand:hover {
            text-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
        }

        .nav-link {
            font-weight: 500;
            transition: all 0.2s;
            color: rgba(255, 255, 255, 0.85) !important;
        }

        .nav-link:hover {
            transform: translateY(-2px);
            color: #fff !important;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
        }

        .active-nav {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50px;
            padding-left: 15px !important;
            padding-right: 15px !important;
            backdrop-filter: blur(10px);
            color: #fff !important;
        }

        .active-nav:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        /* Language Dropdown Active Item */
        .dropdown-item.active-language {
            background-color: rgba(126, 34, 206, 0.1);
            color: #7e22ce;
            font-weight: 600;
        }

        .dropdown-item.active-language::before {
            content: "✓ ";
            margin-right: 5px;
            font-weight: bold;
            color: #7e22ce;
        }

        /* Dark Mode Toggle Button */
        .theme-toggle-btn {
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .theme-toggle-btn:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.5);
        }

        /* Footer Styling */
        footer {
            backdrop-filter: blur(10px);
        }

        /* Mobile Responsive Navbar */
        @media (max-width: 991px) {
            .navbar-collapse {
                background: rgba(0, 0, 0, 0.2);
                border-radius: 12px;
                padding: 15px;
                margin-top: 15px;
                backdrop-filter: blur(10px);
            }

            .navbar-nav {
                gap: 5px;
            }

            .nav-item {
                text-align: center;
            }

            .nav-link {
                padding: 10px 15px !important;
                border-radius: 8px;
                transition: all 0.3s ease;
            }

            .active-nav {
                background: rgba(255, 255, 255, 0.2);
                border-radius: 8px;
                padding: 10px 15px !important;
            }

            .vr {
                display: none !important;
            }

            .navbar-toggler {
                border-color: rgba(255, 255, 255, 0.5);
            }

            .navbar-toggler:focus {
                box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.25);
            }

            /* Mobile utility buttons grouping */
            .mobile-utils {
                display: flex;
                gap: 10px;
                justify-content: center;
                flex-wrap: wrap;
                margin-top: 15px;
                padding-top: 15px;
                border-top: 1px solid rgba(255, 255, 255, 0.2);
            }

            .mobile-utils .nav-item {
                flex: 1;
                min-width: 100px;
            }

            .dropdown-menu {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(126, 34, 206, 0.1);
            }
        }

        /* Dropdown Menu Styling */
        .dropdown-menu {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .dropdown-item {
            transition: all 0.2s ease;
            border-radius: 6px;
            margin: 5px 8px;
        }

        .dropdown-item:hover {
            background-color: rgba(126, 34, 206, 0.08);
            transform: translateX(2px);
        }

        .dropdown-divider {
            margin: 8px 0;
        }
    </style>

    <script>
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-bs-theme', savedTheme);
    </script>
</head>

<body class="bg-body-tertiary d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="bi bi-bag-check-fill me-2"></i>{{ config('app.name', 'My POS') }}
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">

                    @hasanyrole('Super Admin|Manager')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('dashboard') ? 'active-nav text-white' : 'text-white-50' }}"
                                href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2 me-1"></i> {{ __('Dashboard') }}
                            </a>
                        </li>
                    @endhasanyrole

                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('pos*') ? 'active-nav text-white' : 'text-warning' }}"
                            href="{{ route('pos.index') }}">
                            <i class="bi bi-calculator me-1"></i> {{ __('Go to POS') }}
                        </a>
                    </li>

                    @hasanyrole('Super Admin|Manager')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('products*') ? 'active-nav text-white' : 'text-white-50' }}"
                                href="{{ route('products.index') }}">
                                <i class="bi bi-box-seam me-1"></i> {{ __('Products') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('purchases*') ? 'active-nav text-white' : 'text-white-50' }}"
                                href="{{ route('purchases.create') }}">
                                <i class="bi bi-bag-plus me-1"></i> {{ __('Purchase Stock') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('customers*') ? 'active-nav text-white' : 'text-white-50' }}"
                                href="{{ route('customers.index') }}">
                                <i class="bi bi-people me-1"></i> {{ __('Customers') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('expenses*') ? 'active-nav text-white' : 'text-white-50' }}"
                                href="{{ route('expenses.index') }}">
                                <i class="bi bi-wallet2 me-1"></i> {{ __('Expenses') }}
                            </a>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-white-50 {{ request()->is('reports*') ? 'text-white active-nav' : '' }}"
                                href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-graph-up me-1"></i> {{ __('Reports') }}
                            </a>
                            <ul class="dropdown-menu shadow border-0">
                                <li><a class="dropdown-item"
                                        href="{{ route('reports.sales') }}">{{ __('Sales Report') }}</a></li>
                                <li><a class="dropdown-item"
                                        href="{{ route('reports.profit') }}">{{ __('Profit/Loss') }}</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="{{ route('returns.create') }}">{{ __('Returns') }}</a>
                                </li>
                            </ul>
                        </li>
                    @endhasanyrole

                    <div class="vr mx-3 d-none d-lg-block text-white opacity-25"></div>

                    <!-- Mobile Utilities Section -->
                    <div class="mobile-utils d-lg-none w-100">
                        <!-- Theme Toggle -->
                        <li class="nav-item">
                            <button class="btn nav-link theme-toggle-btn text-white ms-auto me-auto"
                                onclick="toggleTheme()" title="{{ __('Toggle Theme') }}"
                                aria-label="{{ __('Toggle Dark/Light Mode') }}">
                                <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
                            </button>
                        </li>

                        <!-- Language Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link text-white dropdown-toggle" href="#" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('Select Language') }}">
                                <i class="bi bi-translate"></i> <span
                                    class="ms-1 d-lg-none">{{ __('Language') }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                <li><a class="dropdown-item {{ app()->getLocale() === 'en' ? 'active-language' : '' }}"
                                        href="{{ route('lang.switch', 'en') }}">🇺🇸 English</a></li>
                                <li><a class="dropdown-item {{ app()->getLocale() === 'bn' ? 'active-language' : '' }}"
                                        href="{{ route('lang.switch', 'bn') }}">🇧🇩 বাংলা</a></li>
                                <li><a class="dropdown-item {{ app()->getLocale() === 'hi' ? 'active-language' : '' }}"
                                        href="{{ route('lang.switch', 'hi') }}">🇮🇳 Hindi</a></li>
                                <li><a class="dropdown-item {{ app()->getLocale() === 'ur' ? 'active-language' : '' }}"
                                        href="{{ route('lang.switch', 'ur') }}">🇵🇰 Urdu</a></li>
                                <li><a class="dropdown-item {{ app()->getLocale() === 'zh' ? 'active-language' : '' }}"
                                        href="{{ route('lang.switch', 'zh') }}">🇨🇳 Chinese</a></li>
                                <li><a class="dropdown-item {{ app()->getLocale() === 'es' ? 'active-language' : '' }}"
                                        href="{{ route('lang.switch', 'es') }}">🇪🇸 Spanish</a></li>
                            </ul>
                        </li>

                        <!-- User Profile -->
                        @auth
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center text-white" href="#"
                                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 32px; height: 32px; font-size: 0.9rem; font-weight: 600;">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                    <li class="px-3 py-2 text-center">
                                        <small class="d-block fw-bold">{{ Auth::user()->name }}</small>
                                        <small
                                            class="text-muted d-block">{{ Auth::user()->getRoleNames()->first() ?? 'User' }}</small>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger fw-bold">
                                                <i class="bi bi-box-arrow-right me-2"></i> {{ __('Logout') }}
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endauth
                    </div>

                    <!-- Desktop Utilities Section -->
                    <div class="d-none d-lg-flex align-items-center ms-lg-2 gap-3">
                        <!-- Theme Toggle -->
                        <li class="nav-item list-unstyled">
                            <button class="btn nav-link theme-toggle-btn text-white" onclick="toggleTheme()"
                                title="{{ __('Toggle Theme') }}" aria-label="{{ __('Toggle Dark/Light Mode') }}">
                                <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
                            </button>
                        </li>

                        <!-- Language Dropdown -->
                        <li class="nav-item dropdown list-unstyled">
                            <a class="nav-link text-white dropdown-toggle" href="#" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('Select Language') }}">
                                <i class="bi bi-translate"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                <li><a class="dropdown-item {{ app()->getLocale() === 'en' ? 'active-language' : '' }}"
                                        href="{{ route('lang.switch', 'en') }}">🇺🇸 English</a></li>
                                <li><a class="dropdown-item {{ app()->getLocale() === 'bn' ? 'active-language' : '' }}"
                                        href="{{ route('lang.switch', 'bn') }}">🇧🇩 বাংলা</a></li>
                                <li><a class="dropdown-item {{ app()->getLocale() === 'hi' ? 'active-language' : '' }}"
                                        href="{{ route('lang.switch', 'hi') }}">🇮🇳 Hindi</a></li>
                                <li><a class="dropdown-item {{ app()->getLocale() === 'ur' ? 'active-language' : '' }}"
                                        href="{{ route('lang.switch', 'ur') }}">🇵🇰 Urdu</a></li>
                                <li><a class="dropdown-item {{ app()->getLocale() === 'zh' ? 'active-language' : '' }}"
                                        href="{{ route('lang.switch', 'zh') }}">🇨🇳 Chinese</a></li>
                                <li><a class="dropdown-item {{ app()->getLocale() === 'es' ? 'active-language' : '' }}"
                                        href="{{ route('lang.switch', 'es') }}">🇪🇸 Spanish</a></li>
                            </ul>
                        </li>

                        <!-- User Profile -->
                        @auth
                            <li class="nav-item dropdown list-unstyled">
                                <a class="nav-link dropdown-toggle d-flex align-items-center text-white" href="#"
                                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-2"
                                        style="width: 35px; height: 35px;">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <span class="d-block lh-1"
                                            style="font-size: 0.9rem;">{{ Auth::user()->name }}</span>
                                        <span style="font-size: 0.7rem;"
                                            class="opacity-75">{{ Auth::user()->getRoleNames()->first() ?? 'User' }}</span>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger fw-bold">
                                                <i class="bi bi-box-arrow-right me-2"></i> {{ __('Logout') }}
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endauth
                    </div>

                </ul>
            </div>
        </div>
    </nav>

    <div class="container flex-grow-1 py-4">
        @yield('content')

        @isset($slot)
            {{ $slot }}
        @endisset
    </div>

    <footer class="text-center py-4 mt-auto border-top bg-body">
        <small class="text-muted">
            &copy; {{ date('Y') }} <strong>Advanced POS</strong>. {{ __('All rights reserved.') }} <br>
            Designed with <i class="bi bi-heart-fill text-danger"></i> by Abdur Rahim
        </small>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function toggleTheme() {
            const html = document.getElementById('htmlTag');
            const currentTheme = html.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';

            html.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateIcon(newTheme);
        }

        function updateIcon(theme) {
            const icon = document.getElementById('themeIcon');
            if (!icon) return;

            if (theme === 'dark') {
                icon.classList.remove('bi-moon-stars-fill');
                icon.classList.add('bi-sun-fill');
            } else {
                icon.classList.remove('bi-sun-fill');
                icon.classList.add('bi-moon-stars-fill');
            }
        }

        // Initialize theme icon on page load
        document.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('theme') || 'light';
            const html = document.getElementById('htmlTag');
            html.setAttribute('data-bs-theme', savedTheme);
            updateIcon(savedTheme);
        });

        // Ensure icon is updated on initial load before DOMContentLoaded
        const initialTheme = localStorage.getItem('theme') || 'light';
        updateIcon(initialTheme);
    </script>

</body>

</html>
