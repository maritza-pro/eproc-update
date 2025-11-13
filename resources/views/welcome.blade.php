<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Procurement</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
            html {
            scroll-behavior: smooth;
        }
    
    </style>
</head>
<body class="font-sans antialiased">
@include('header')
 
     <section id="beranda" class="pt-32 pb-20 bg-gradient-to-br from-white to-blue-50">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-10">

            <div class="md:w-1/2 space-y-6">
                <h1 class="text-4xl md:text-5xl font-extrabold text-blue-900 leading-tight">
                   Welcome to <br>
                   <span class="text-sky-500">E-Procurement </span>Application
                </h1>
                <p class="text-gray-600 text-lg">
                    Transformasikan cara kamu mengelola tender, vendor, dan produk dengan solusi digital cerdas yang menghemat waktu dan meningkatkan produktivitas.
                </p>

                <div class="flex flex-wrap gap-4 pt-2">
                    <a href="#tender" class="bg-blue-900 text-white px-6 py-3 rounded-lg hover:bg-blue-800 transition font-semibold">
                        Lihat Tender Aktif →
                    </a>
                    <a href="#vendor" class="border border-blue-900 text-blue-900 px-6 py-3 rounded-lg hover:bg-blue-900 hover:text-white transition font-semibold">
                        Daftar Vendor
                    </a>
                </div>
            </div>
            <div class="md:w-1/2 flex justify-center relative">
                <div class="bg-gradient-to-br from-blue-100 to-white rounded-2xl w-160 h-160 flex items-center justify-center">
                        <img src="https://www.pngarts.com/files/4/Skyscraper-PNG-Background-Image.png" alt="Clipboard Icon" class="w-160 h-160">
                </div>
            </div>

        </div>
    </section>

    <section id="fitur" class="h-screen flex items-center justify-center bg-blue-50">
        <h2 class="text-3xl font-semibold text-blue-900">About </h2>
    </section>
    <section id="tender" class="h-screen flex items-center justify-center bg-white">
        <h2 class="text-3xl font-semibold text-blue-900">Vender</h2>
    </section>
    <section id="hubungi" class="h-screen flex items-center justify-center bg-blue-50">
        <h2 class="text-3xl font-semibold text-blue-900">Hubungi Kami</h2>
    </section>
@include('footer')
</body>
</html>