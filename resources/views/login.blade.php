<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <link rel="icon" href="{{ asset('images/logo_icon.png') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center font-sans">

    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md border-t-4 border-blue-500">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Login</h2>

        <!-- Tampilkan Pesan Error Jika Login Gagal -->
        @error('username')
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 text-sm" role="alert">
                <span class="block sm:inline">{{ $message }}</span>
            </div>
        @enderror

        <form action="/login" method="POST">
            @csrf <!-- Wajib ada di Laravel untuk keamanan form (CSRF Token) -->

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                    Username
                </label>
                <input class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       id="username" name="username" type="text" placeholder="Masukkan username..." value="{{ old('username') }}" required autofocus>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                    Password
                </label>
                <input class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       id="password" name="password" type="password" placeholder="••••••••" required>
            </div>

            <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300" type="submit">
                Masuk
            </button>
        </form>

        <div class="text-center mt-6">
            <a href="/" class="text-sm text-gray-500 hover:text-blue-600 hover:underline">&larr; Kembali ke Halaman Utama</a>
        </div>
    </div>

</body>
</html>