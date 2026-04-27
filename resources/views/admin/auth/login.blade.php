<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style> 
        body { font-family: 'Inter', sans-serif; } 
        
        input::-ms-reveal,
        input::-ms-clear { 
            display: none;
        }
    </style>
</head>
<body class="bg-slate-50 h-screen flex items-center justify-center p-4 md:p-10">

    <div class="bg-white rounded-[24px] shadow-[0_20px_50px_rgba(0,0,0,0.08)] w-full max-w-[1000px] min-h-[600px] flex p-4">
        
        <div class="hidden md:flex w-1/2 bg-[#253294] rounded-[24px] text-white relative overflow-hidden items-center justify-center">
            
            <svg class="absolute top-0 left-0 -translate-x-1/3 -translate-y-1/3 w-72 h-72 opacity-20 pointer-events-none" viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="0.5">
                <circle cx="50" cy="50" r="10"/>
                <circle cx="50" cy="50" r="15"/>
                <circle cx="50" cy="50" r="20"/>
                <circle cx="50" cy="50" r="25"/>
                <circle cx="50" cy="50" r="30"/>
                <circle cx="50" cy="50" r="35"/>
                <circle cx="50" cy="50" r="40"/>
                <circle cx="50" cy="50" r="45"/>
                <circle cx="50" cy="50" r="50"/>
            </svg>

            <svg class="absolute bottom-0 right-0 translate-x-1/4 translate-y-1/4 w-96 h-96 opacity-15 pointer-events-none" viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="0.5">
                <circle cx="50" cy="50" r="10"/>
                <circle cx="50" cy="50" r="15"/>
                <circle cx="50" cy="50" r="20"/>
                <circle cx="50" cy="50" r="25"/>
                <circle cx="50" cy="50" r="30"/>
                <circle cx="50" cy="50" r="35"/>
                <circle cx="50" cy="50" r="40"/>
                <circle cx="50" cy="50" r="45"/>
                <circle cx="50" cy="50" r="50"/>
            </svg>

            <div class="relative z-10 text-center px-10">
                <h1 class="text-4xl font-bold mb-4 tracking-tight">Selamat Datang</h1>
                <p class="text-[15px] font-medium text-blue-100 leading-relaxed">di Website Visualisasi Spasial Cuaca<br>Jawa Timur</p>
            </div>
        </div>

        <div class="w-full md:w-1/2 p-10 md:px-16 md:py-12 flex flex-col justify-center">
            <div class="max-w-md w-full mx-auto">
                <h2 class="text-3xl font-bold text-slate-800 mb-2">Masuk</h2>
                <p class="text-slate-500 text-sm mb-8 font-medium">Masukkan email anda di bawah ini</p>

                @if($errors->any())
                    <div class="bg-red-50 text-red-600 p-4 rounded-xl text-sm mb-6 border border-red-200 flex items-start gap-3">
                        <svg class="w-5 h-5 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form action="{{ route('login.process') }}" method="POST" class="space-y-5">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2 tracking-wide">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required 
                            class="w-full bg-slate-50 hover:bg-slate-100 px-4 py-3.5 rounded-xl border border-slate-200 focus:bg-white focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none transition-all text-sm font-medium text-slate-800">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2 tracking-wide">Password</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required 
                                class="w-full bg-slate-50 hover:bg-slate-100 px-4 py-3.5 pr-12 rounded-xl border border-slate-200 focus:bg-white focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none transition-all text-sm font-medium text-slate-800">
                            
                            <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-blue-600 transition-colors">
                                <svg id="eyeIcon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" 
                        class="w-full bg-[#253294] hover:bg-[#1b2579] text-white font-bold py-3.5 rounded-xl transition-all mt-4 text-[15px] shadow-[0_8px_20px_rgba(37,50,148,0.25)] hover:shadow-[0_4px_10px_rgba(37,50,148,0.2)] hover:-translate-y-0.5">
                        Masuk
                    </button>
                </form>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            const eyeOpenSvg = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>`;
            
            const eyeClosedSvg = `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>`;

            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                if (type === 'text') {
                    eyeIcon.innerHTML = eyeClosedSvg;
                    eyeIcon.classList.add('text-blue-600');
                } else {
                    eyeIcon.innerHTML = eyeOpenSvg;
                    eyeIcon.classList.remove('text-blue-600');
                }
            });
        });
    </script>
</body>
</html>