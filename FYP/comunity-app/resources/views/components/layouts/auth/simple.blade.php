<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
    <title>{{ $title ?? 'Community Connect' }}</title>
    <style>
        /* Force dark theme for auth pages */
        body {
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e) !important;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            color: #ffffff;
            margin: 0;
            padding: 0;
        }

        .auth-container {
            width: 100%;
            max-width: 480px;
            /* narrowed for better focus */
            padding: 2rem;
            position: relative;
            z-index: 10;
        }

        .auth-logo-section {
            text-align: center;
            margin-bottom: 2.5rem;
            animation: fadeInDown 0.8s ease-out;
        }

        .auth-logo-link {
            text-decoration: none;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }

        .auth-logo-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #8b5cf6, #ec4899);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: white;
            font-size: 1.5rem;
            box-shadow: 0 10px 25px rgba(236, 72, 153, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.3s ease;
        }

        .auth-logo-link:hover .auth-logo-icon {
            transform: scale(1.05) rotate(5deg);
        }

        .auth-logo-text {
            font-family: 'Outfit', sans-serif;
            font-size: 1.75rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            background: linear-gradient(to right, #ffffff, #c4b5fd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        /* Glassmorphism Card */
        .glass-card {
            background: rgba(30, 30, 50, 0.6);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4), inset 0 0 0 1px rgba(255, 255, 255, 0.05);
            animation: fadeInUp 0.8s ease-out;
        }

        /* Typography */
        h2.auth-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        p.auth-subtitle {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.95rem;
            text-align: center;
            margin-bottom: 2rem;
            line-height: 1.5;
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 1.25rem;
            position: relative;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.9rem;
            font-weight: 500;
            margin-left: 0.25rem;
        }

        .form-input {
            width: 100%;
            padding: 0.875rem 1rem;
            border-radius: 14px;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 0.95rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-family: 'Inter', sans-serif;
        }

        .form-input:focus {
            outline: none;
            background: rgba(0, 0, 0, 0.3);
            border-color: #8b5cf6;
            box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.15);
            transform: translateY(-1px);
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        /* Button */
        .primary-button {
            width: 100%;
            padding: 0.875rem;
            border-radius: 14px;
            background: linear-gradient(135deg, #8b5cf6, #ec4899);
            border: none;
            color: white;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
            margin-top: 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .primary-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.5);
            filter: brightness(1.1);
        }

        .primary-button:active {
            transform: translateY(0);
        }

        .primary-button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        /* Links */
        .auth-link {
            color: #a78bfa;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.2s;
        }

        .auth-link:hover {
            color: #c4b5fd;
            text-decoration: underline;
        }

        /* Checkbox */
        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
        }

        .custom-checkbox {
            appearance: none;
            width: 20px;
            height: 20px;
            border-radius: 6px;
            border: 1.5px solid rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.05);
            cursor: pointer;
            position: relative;
            transition: all 0.2s ease;
        }

        .custom-checkbox:checked {
            background: #8b5cf6;
            border-color: #8b5cf6;
        }

        .custom-checkbox:checked::after {
            content: '✓';
            position: absolute;
            color: white;
            font-size: 14px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-weight: bold;
        }

        .checkbox-label {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            user-select: none;
        }

        /* Helper Utilities */
        .flex-between {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .text-center {
            text-align: center;
        }

        .mt-4 {
            margin-top: 1.5rem;
        }

        .mt-6 {
            margin-top: 2rem;
        }

        .divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
            margin: 2rem 0;
            position: relative;
        }

        .error-message {
            color: #ff8a8a;
            font-size: 0.85rem;
            margin-top: 0.4rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        /* Animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Override any potential light theme styles */
        * {
            color-scheme: dark;
            box-sizing: border-box;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body>
    <div class="auth-container">
        <div class="auth-logo-section">
            <a href="{{ route('home') }}" class="auth-logo-link">
                <div class="auth-logo-icon">CC</div>
                <span class="auth-logo-text">Community Connect</span>
            </a>
        </div>

        {{ $slot }}
    </div>

    @livewireScripts
    @fluxScripts
</body>

</html>