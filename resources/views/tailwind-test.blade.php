<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tailwind Test</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold text-blue-600 mb-4">Tailwind CSS Test</h1>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            This is a red alert box
        </div>
        <div class="flex gap-4">
            <div class="bg-blue-500 text-white p-4 rounded">Blue Box</div>
            <div class="bg-green-500 text-white p-4 rounded">Green Box</div>
            <div class="bg-yellow-500 text-black p-4 rounded">Yellow Box</div>
        </div>
        <div class="grid grid-cols-2 gap-4 mt-4">
            <div class="bg-purple-500 text-white p-4 rounded">Grid Item 1</div>
            <div class="bg-pink-500 text-white p-4 rounded">Grid Item 2</div>
        </div>
    </div>
</body>
</html>
