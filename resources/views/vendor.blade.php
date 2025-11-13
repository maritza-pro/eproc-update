<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Procurement</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body class="font-sans antialiased">
    @include('header')
    <!-- Section Cara Menjadi Vendor & Syarat-Syarat (Side by Side) -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gray-50">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- Kolom Kiri: Cara Menjadi Vendor -->
                <div class="bg-gradient-to-br from-blue-100 to-white rounded-2xl p-8 shadow-2xl">
                    <div class="space-y-6">
                        <h2 class="text-center text-3xl font-bold text-gray-800 mb-8">Cara Menjadi Vendor</h2>
                        
                        <div class="space-y-6">
                            <!-- Step 1 -->
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0 text-white font-bold text-xl">
                                    1
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold mb-2 text-gray-800">Registrasi</h3>
                                    <p class="text-gray-700">Akses halaman Pendaftaran Vendor lalu isi formulir pendaftaran dengan data dasar perusahaan, meliputi:</p>
                                    <ul class="list-disc list-inside mt-2 space-y-1 text-gray-600">
                                        <li>Logo Perusahaan</li>
                                        <li>Nama Perusahaan</li>
                                        <li>Jenis Entitas Bisnis</li>
                                        <li>Bidang Bisnis</li>
                                        <li>Nomor Telepon</li>
                                        <li>Email Perusahaan</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Step 2 -->
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0 text-white font-bold text-xl">
                                    2
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold mb-2 text-gray-800">Lengkapi Dokumen Perusahaan</h3>
                                    <p class="font-semibold text-gray-700 mt-2">A. Legalitas</p>
                                    <ul class="list-disc list-inside space-y-1 text-gray-600">
                                        <li>Akta Pendirian Perusahaan</li>
                                        <li>Pengesahan (SK Kemenkumham)</li>
                                        <li>Bidang Bisnis</li>
                                        <li>Nomor Telepon</li>
                                    </ul>
                                    <p class="font-semibold text-gray-700 mt-3">B. Lisensi</p>
                                    <ul class="list-disc list-inside space-y-1 text-gray-600">
                                        <li>Surat Keterangan Domisili Usaha (SKDU)</li>
                                        <li>Sertifikat Badan Usaha (SBU)</li>
                                        <li>Nomor Induk Berusaha (NIB)</li>
                                        <li>Tanda Daftar Perusahaan (TDP)</li>
                                        <li>Hinder Ordonantie (HO)</li>
                                        <li>Surat Pengukuhan Pengusaha Kena Pajak (SPPKP)</li>
                                        <li>Surat Izin Usaha Perdagangan (SIUP)</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Step 3 -->
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0 text-white font-bold text-xl">
                                    3
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold mb-2 text-gray-800">Lengkapi Informasi Finansial</h3>
                                    <p class="text-gray-700">Isi data keuangan perusahaan dengan benar, meliputi:</p>
                                    <ul class="list-disc list-inside mt-2 space-y-1 text-gray-600">
                                        <li>Nama Bank</li>
                                        <li>Nama Akun</li>
                                        <li>Nomor Rekening</li>
                                    </ul>
                                    <p class="text-sm text-gray-600 mt-2 italic">Note: Pastikan akun yang tertera adalah akun aktif atas nama perusahaan.</p>
                                </div>
                            </div>

                            <!-- Step 4 -->
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0 text-white font-bold text-xl">
                                    4
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold mb-2 text-gray-800">Isi Data Penanggung Jawab Tender</h3>
                                    <p class="text-gray-700">Masukkan informasi lengkap penanggung jawab tender:</p>
                                    <ul class="list-disc list-inside mt-2 space-y-1 text-gray-600">
                                        <li>Nama Lengkap</li>
                                        <li>Nomor Telepon</li>
                                        <li>Posisi Pekerjaan</li>
                                        <li>Alamat Email</li>
                                        <li>Nomor ID Nasional (KTP)</li>
                                        <li>Lampiran (format: JPEG, PNG, atau PDF)</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Step 5 -->
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0 text-white font-bold text-xl">
                                    5
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold mb-2 text-gray-800">Isi Data Pengalaman Vendor</h3>
                                    <p class="text-gray-700">Lengkapi riwayat proyek atau pengalaman kerja sebelumnya:</p>
                                    <ul class="list-disc list-inside mt-2 space-y-1 text-gray-600">
                                        <li>Nama Proyek</li>
                                        <li>Lokasi Proyek</li>
                                        <li>Nomor Kontrak</li>
                                        <li>Tanggal Mulai</li>
                                        <li>Tanggal Akhir</li>
                                        <li>Bidang Bisnis</li>
                                        <li>Pemangku Kepentingan</li>
                                        <li>Nilai Proyek</li>
                                        <li>Keterangan Tambahan</li>
                                        <li>Lampiran Pengalaman (PDF)</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Step 6 -->
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0 text-white font-bold text-xl">
                                    6
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold mb-2 text-gray-800">Proses Verifikasi oleh Admin</h3>
                                    <p class="text-gray-700 text-justify">Tim pengadaan akan memeriksa kelengkapan dan validitas dokumen. Jika ada kekurangan, sistem akan memberikan notifikasi untuk perbaikan. Setelah disetujui, status vendor akan berubah menjadi "Terverifikasi" dan jika ditolak, maka tim akan melampirkan alasan penolakan.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Syarat-Syarat Umum -->
                <div class="space-y-8">
                    <!-- Syarat-Syarat Umum -->
                    <div class="bg-gradient-to-br from-green-100 to-white rounded-2xl p-8 shadow-2xl">
                        <div class="space-y-6">
                            <h2 class="text-center text-3xl font-bold text-gray-800 mb-8">Syarat-Syarat Umum Pendaftaran Vendor</h2>
                            
                            <div class="space-y-4">
                                <div class="flex items-start space-x-4">
                                    <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="bi bi-check-circle-fill text-2xl text-white"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-800">Berbadan hukum yang sah (PT, CV, Koperasi, atau perorangan sesuai ketentuan).</h3>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-4">
                                    <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="bi bi-check-circle-fill text-2xl text-white"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-800">Memiliki izin usaha sesuai bidang yang akan diikuti.</h3>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-4">
                                    <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="bi bi-check-circle-fill text-2xl text-white"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-800">Memiliki NPWP dan dokumen legalitas lengkap.</h3>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-4">
                                    <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="bi bi-check-circle-fill text-2xl text-white"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-800">Tidak termasuk dalam daftar hitam pengadaan.</h3>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-4">
                                    <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="bi bi-check-circle-fill text-2xl text-white"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-800">Memiliki rekening bank aktif atas nama perusahaan.</h3>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-4">
                                    <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="bi bi-check-circle-fill text-2xl text-white"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-800">Bersedia mengikuti seluruh aturan dan ketentuan pengadaan yang berlaku.</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panduan Teknis Upload Dokumen -->
                    <div class="bg-gradient-to-br from-purple-100 to-white rounded-2xl p-8 shadow-2xl">
                        <div class="space-y-6">
                            <h2 class="text-center text-3xl font-bold text-gray-800 mb-8">Panduan Teknis Upload Dokumen</h2>
                            
                            <div class="space-y-4">
                                <div class="flex items-start space-x-4">
                                    <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="bi bi-file-earmark-arrow-up text-2xl text-white"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-800">Dokumen diunggah dalam format .pdf, .jpg, .png, dan .zip.</h3>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-4">
                                    <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="bi bi-file-earmark-arrow-up text-2xl text-white"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-800">Sistem harus menetapkan batas ukuran file per dokumen (maksimal 10 MB).</h3>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-4">
                                    <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="bi bi-file-earmark-arrow-up text-2xl text-white"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-800">Daftar dokumen yang wajib diunggah adalah: NPWP, Surat Izin Usaha Perdagangan (SIUP), dan Akta Pendirian Perusahaan.</h3>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-4">
                                    <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="bi bi-file-earmark-arrow-up text-2xl text-white"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-800">Gunakan drag & drop atau tombol "Upload Dokumen".</h3>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-4">
                                    <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="bi bi-file-earmark-arrow-up text-2xl text-white"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-800">Pastikan koneksi internet stabil saat upload.</h3>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-4">
                                    <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="bi bi-file-earmark-arrow-up text-2xl text-white"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-800">Setelah semua dokumen lengkap, klik tombol Submit.</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
    @include('footer')
</body>
</html>