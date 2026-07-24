<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layar Terkunci - Ijin Keluar</title>
    <link rel="icon" href="{{ asset('images/logo_icon.png') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    
    <div class="w-full max-w-md bg-white rounded-xl shadow-2xl border-t-8 border-yellow-500 overflow-hidden text-center p-8">
         
        <div class="mb-6 flex justify-center">
            <svg class="w-24 h-24 text-yellow-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
        </div>
        
        <h2 class="text-3xl font-bold text-gray-800 mb-3">Layar Terkunci</h2>
        
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-8">
            <p class="text-gray-700 font-medium text-sm leading-relaxed">
                Anda sedang dalam status <span class="text-yellow-700 font-bold">Ijin Keluar</span>. <br><br>
                Akses sistem absensi ditangguhkan sementara. Anda tidak dapat melakukan Absen Pulang sebelum mengonfirmasi kepulangan Anda ke kantor.
            </p>
        </div>
        
        <form action="{{ route('absen.kembali') }}" method="POST">
            @csrf
            <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 px-4 rounded-lg shadow-md transition-all duration-300 transform hover:-translate-y-1 cursor-pointer">
                Sudah Kembali ke Kantor
            </button>
        </form>
    </div>
</body>
</html>