<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tory Crown | API Backend</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                    },
                    colors: {
                        gold: {
                            400: '#D4AF37',
                            500: '#AA8C2C',
                        },
                        dark: {
                            900: '#0A0A0A',
                            800: '#141414',
                        }
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 1s ease-out forwards',
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    },
                    keyframes: {
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #0A0A0A;
            background-image: radial-gradient(circle at 50% 0%, #1a1a1a 0%, #0A0A0A 70%);
            color: #ffffff;
        }
        .glass-panel {
            background: rgba(20, 20, 20, 0.6);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(212, 175, 55, 0.1);
        }
        .gold-gradient-text {
            background: linear-gradient(135deg, #F3E5AB 0%, #D4AF37 50%, #AA8C2C 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hover-gold-glow:hover {
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.2);
            border-color: rgba(212, 175, 55, 0.4);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center relative overflow-hidden font-sans selection:bg-gold-500 selection:text-white">

    <!-- Decorative Background Elements -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[500px] bg-gold-500/5 blur-[120px] rounded-full pointer-events-none"></div>
    <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-gold-500/5 blur-[100px] rounded-full pointer-events-none animate-pulse-slow"></div>

    <main class="relative z-10 w-full max-w-4xl px-6 flex flex-col items-center text-center opacity-0 animate-fade-in-up">
        
        <!-- Logo Icon (Crown placeholder) -->
        <div class="mb-8 p-4 rounded-full glass-panel inline-flex hover:scale-105 transition-transform duration-500">
            <svg class="w-12 h-12 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 21h18M4 18l2-10 4 4 2-6 2 6 4-4 2 10H4z"></path>
            </svg>
        </div>

        <h1 class="font-serif text-5xl md:text-7xl tracking-tight mb-4">
            <span class="gold-gradient-text">Tory Crown</span> Engine
        </h1>
        
        <p class="text-gray-400 text-lg md:text-xl font-light tracking-wide max-w-2xl mb-12">
            The headless commerce architecture powering the next generation of luxury jewelry retail. API v1 is currently active and processing requests.
        </p>

        <!-- Action Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full max-w-2xl">
            <!-- Admin Panel Link -->
            <a href="/admin" class="group glass-panel rounded-2xl p-8 flex flex-col items-center text-center hover-gold-glow transition-all duration-300">
                <div class="w-12 h-12 rounded-full bg-gold-400/10 flex items-center justify-center mb-4 group-hover:bg-gold-400/20 transition-colors">
                    <svg class="w-6 h-6 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-medium text-white mb-2">Admin Control</h3>
                <p class="text-sm text-gray-400">Manage catalog, orders, and dynamic CMS layouts via Filament.</p>
            </a>

            <!-- API Docs Link -->
            <a href="/api/v1/products" target="_blank" class="group glass-panel rounded-2xl p-8 flex flex-col items-center text-center hover-gold-glow transition-all duration-300">
                <div class="w-12 h-12 rounded-full bg-gold-400/10 flex items-center justify-center mb-4 group-hover:bg-gold-400/20 transition-colors">
                    <svg class="w-6 h-6 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-medium text-white mb-2">API Endpoints</h3>
                <p class="text-sm text-gray-400">Headless REST API powering the React frontend storefront.</p>
            </a>
        </div>

    </main>

    <!-- Footer -->
    <footer class="absolute bottom-8 text-center w-full z-10 opacity-0 animate-fade-in-up" style="animation-delay: 0.5s;">
        <p class="text-xs text-gray-500 uppercase tracking-widest">
            Laravel v{{ Illuminate\Foundation\Application::VERSION }} · PHP v{{ PHP_VERSION }}
        </p>
    </footer>

</body>
</html>
