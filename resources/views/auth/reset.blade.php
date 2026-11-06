
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white shadow-lg rounded-xl flex overflow-hidden w-full max-w-2xl">
        <!-- Left: Reset Password Form -->
        <div class="w-full lg:w-1/2 p-5">
            <h2 class="text-2xl font-bold mb-4">Reset Password</h2>

            {{-- Pesan Error / Sukses --}}
            @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
                {{ session('error') }}
            </div>
            @endif

            @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
                {{ session('success') }}
            </div>
            @endif

            {{-- Form Reset Password --}}
            <form action="{{ route('password.reset.post') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="mt-1 w-full border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                        placeholder="user@gmail.com">
                    @error('email')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Username -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Username</label>
                    <input type="text" name="username" value="{{ old('username') }}" required
                        class="mt-1 w-full border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                        placeholder="username">
                    @error('username')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror

                </div>
                <!-- No HP -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">No HP</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp') }}" required
                        class="mt-1 w-full border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                        placeholder="08xxxxxxxxxx">
                    @error('no_hp')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit"
                    class="w-full bg-blue-400 text-white font-bold py-2 rounded hover:bg-blue-500 transition">
                    RESET PASSWORD
                </button>
            </form>
        </div>
    </div>
</body>
</html>


