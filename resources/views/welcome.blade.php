<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Cafeteria</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="text-center">
        <h1 class="text-3xl font-bold mb-6">Smart Cafeteria Management System</h1>
        
        <div class="flex justify-center gap-4">
            <a href="{{ route('login') }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Login
            </a>
            <a href="{{ route('register') }}" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Register
            </a>
        </div>
    </div>
</body>
</html>
