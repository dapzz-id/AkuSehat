<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Aku Sehat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
        }

        .card-shadow {
            box-shadow: 0 15px 35px rgba(50, 50, 93, 0.1), 0 5px 15px rgba(0, 0, 0, 0.07);
        }

        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.5);
        }

        .btn-hover {
            transition: all 0.3s ease;
        }

        .btn-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1), 0 3px 6px rgba(0, 0, 0, 0.08);
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        .floating-label {
            position: relative;
            margin-bottom: 20px;
        }

        .floating-input {
            border: 0;
            border-bottom: 2px solid #cbd5e0;
            outline: none;
            transition: all 0.3s ease;
        }

        .floating-input:focus {
            border-bottom: 2px solid #1B5E20;
        }

        .floating-label label {
            position: absolute;
            top: 12px;
            left: 40px;
            transition: all 0.3s ease;
            pointer-events: none;
            color: #a0aec0;
        }

        .floating-input:focus~label,
        .floating-input:not(:placeholder-shown)~label {
            top: -15px;
            left: 10px;
            font-size: 12px;
            color: #1B5E20;
            background: white;
            padding: 0 8px;
        }

        @media (max-width: 640px) {
            .mobile-padding {
                padding: 1.5rem;
            }

            .mobile-margin {
                margin-top: 1rem;
                margin-bottom: 1rem;
            }
        }
    </style>
</head>

<body
    class="bg-[url('/src/bg_pharmacy_lab.avif')] backdrop-blur-sm bg-no-repeat bg-cover min-h-screen flex items-center justify-center px-4 py-8">
    <div class="bg-black/10 fixed inset-0"></div>

    <div class="max-w-md w-full relative z-10">
        <div class="bg-white rounded-2xl card-shadow overflow-hidden transition-all duration-300 hover:shadow-xl">
            <div class="bg-gradient-to-r from-[#308a34] to-[#1B5E20] p-4 text-center">
                <div
                    class="w-max px-5 py-1.5 mt-3 h-30 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 pulse-animation">
                    <img src="{{ asset('src/aku_sehat_white_icon.png') }}" class="w-36 h-auto mt-1" alt="">
                </div>
                <p class="text-blue-100 mt-2">Masuk ke akun Anda</p>
            </div>

            <div class="p-8 mobile-padding">
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-md animate-fade-in">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-3"></i>
                            <div>
                                @foreach ($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="space-y-6 mobile-margin">
                    @csrf
                    <div class="floating-label">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input type="text" id="username" name="username" required
                                class="floating-input block w-full pl-10 pr-3 py-3 rounded-md leading-5 bg-white placeholder-transparent input-focus"
                                placeholder="Masukkan username" value="{{ old('username') }}">
                            <label for="username">Username</label>
                        </div>
                    </div>

                    <div class="floating-label">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input type="password" id="password" name="password" required
                                class="floating-input block w-full pl-10 pr-10 py-3 rounded-md leading-5 bg-white placeholder-transparent input-focus"
                                placeholder="Masukkan password">
                            <label for="password">Password</label>
                            <span class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer text-gray-400"
                                id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-2">
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox"
                                class="h-4 w-4 text-[#1B5E20] focus:ring-[#308a34] border-gray-300 rounded"
                                {{ old('remember') ? 'checked' : '' }}>
                            <label for="remember" class="ml-2 block text-sm text-gray-700">Ingat saya</label>
                        </div>

                        <div class="text-sm">
                            <a href="#"
                                class="font-medium text-[#1B5E20] hover:text-[#308a34] transition-colors">Lupa
                                password?</a>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-[#308a34] to-[#1B5E20] hover:from-[#308a34] hover:to-[#1B5E20] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#308a34] btn-hover transition-all duration-300">
                        <i class="fas fa-sign-in-alt mr-2 mt-0.5"></i>
                        Masuk
                    </button>
                </form>

                <div class="mt-8 pt-5 border-t border-gray-200">
                    <p class="text-xs text-center text-gray-500">
                        Aku Sehat © {{ date('Y') }} | raadeveloperz <br> All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const errorDiv = document.querySelector('.animate-fade-in');
            if (errorDiv) {
                errorDiv.classList.add('opacity-0');
                setTimeout(() => {
                    errorDiv.classList.remove('opacity-0');
                    errorDiv.classList.add('opacity-100');
                }, 100);
            }

            const inputs = document.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('focus', () => {
                    input.parentElement.classList.add('ring-2', 'ring-[#1B5E20]');
                });

                input.addEventListener('blur', () => {
                    input.parentElement.classList.remove('ring-2', 'ring-[#1B5E20]');
                });
            });

            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#password');

            togglePassword.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);

                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
        });
    </script>
</body>
</html>