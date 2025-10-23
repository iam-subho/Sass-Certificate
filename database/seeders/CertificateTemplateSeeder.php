<?php

namespace Database\Seeders;

use App\Models\CertificateTemplate;
use Illuminate\Database\Seeder;

class CertificateTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CertificateTemplate::create([
            'name' => 'Modern Professional Certificate',
            'description' => 'A stunning modern certificate design with elegant gradients and professional typography',
            'html_content' => $this->getTemplateHtml(),
            'is_active' => true,
        ]);

        if ($this->command) {
            $this->command->info('Certificate template created.');
        }
    }

    /**
     * Get the default template HTML
     */
    private function getTemplateHtml(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Achievement</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        .playfair {
            font-family: 'Playfair Display', serif;
        }
        .certificate-page {
            width: 297mm;
            height: 210mm;
        }
        /* Hide images with empty or no src */
        img[src=""], img:not([src]) {
            display: none !important;
        }
        /* Hide signature containers if image is empty */
        .signature-container:has(img[src=""]),
        .signature-container:has(img:not([src])) {
            display: none !important;
        }
        /* Hide signature line if title is empty */
        .signature-title:empty {
            display: none !important;
        }
        .signature-line:has(.signature-title:empty) {
            border-top: none !important;
        }
    </style>
</head>
<body class="bg-white">
    <!-- Certificate Container - Single Page -->
    <div class="certificate-page relative overflow-hidden bg-gradient-to-br from-amber-50 via-white to-blue-50">

        <!-- Decorative Background Elements -->
        <div class="absolute top-0 left-0 w-80 h-80 bg-gradient-to-br from-amber-200/20 to-transparent rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-0 w-80 h-80 bg-gradient-to-tl from-blue-200/20 to-transparent rounded-full blur-3xl"></div>

        <!-- Main Border Frame -->
        <div class="absolute inset-3 border-4 border-double border-amber-600/40 rounded-lg"></div>
        <div class="absolute inset-5 border-2 border-amber-500/30 rounded-lg"></div>

        <!-- Main Content Area -->
        <div class="relative h-full flex flex-col justify-between p-10">

            <!-- Header Section -->
            <div class="text-center relative z-10">
                <div class="flex items-center justify-center gap-6 mb-3">
                    <!-- Left Logo -->
                    <div class="w-16 h-16 flex-shrink-0">
                        <img src="{{certificate_left_logo}}" alt="" class="w-full h-full object-contain drop-shadow-md">
                    </div>

                    <!-- School Information -->
                    <div class="flex-1 max-w-2xl">
                        <h1 class="text-3xl font-bold text-gray-800 playfair tracking-wide uppercase mb-1">
                            {{school_name}}
                        </h1>
                        <div class="flex items-center justify-center gap-3 text-xs text-gray-600">
                            <span class="flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                </svg>
                                {{school_email}}
                            </span>
                            <span class="text-amber-600">•</span>
                            <span class="flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                                </svg>
                                {{school_phone}}
                            </span>
                        </div>
                    </div>

                    <!-- Right Logo -->
                    <div class="w-16 h-16 flex-shrink-0">
                        <img src="{{certificate_right_logo}}" alt="" class="w-full h-full object-contain drop-shadow-md">
                    </div>
                </div>
            </div>

            <!-- Certificate Body - Centered -->
            <div class="flex-1 flex flex-col justify-center items-center relative z-10 -mt-4">
                <!-- Certificate Badge -->
                <div class="mb-4">
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-r from-amber-400 to-amber-600 blur-lg opacity-50"></div>
                        <div class="relative bg-gradient-to-r from-amber-500 to-amber-600 text-white px-6 py-1.5 rounded-full shadow-lg">
                            <span class="text-xs font-semibold tracking-widest uppercase">Certificate of Achievement</span>
                        </div>
                    </div>
                </div>

                <!-- Title -->
                <h2 class="text-5xl font-black playfair text-transparent bg-clip-text bg-gradient-to-r from-amber-700 via-amber-600 to-amber-700 mb-5 tracking-tight">
                    Certificate
                </h2>

                <!-- Decorative Line -->
                <div class="w-24 h-1 bg-gradient-to-r from-transparent via-amber-500 to-transparent mb-5"></div>

                <!-- Certify Text -->
                <p class="text-base text-gray-600 italic mb-4 montserrat font-light">
                    This is proudly presented to
                </p>

                <!-- Student Name with QR Code -->
                <div class="mb-5 relative flex items-center justify-center gap-6">
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-200 to-purple-200 blur-xl opacity-30"></div>
                        <h3 class="relative text-5xl font-bold playfair text-gray-900 border-b-4 border-amber-500 pb-1.5 px-8">
                            {{full_name}}
                        </h3>
                    </div>
                    <!-- QR Code beside name -->
                    <div class="flex-shrink-0">
                        <div class="bg-white rounded-lg p-2 shadow-xl border-2 border-amber-200">
                            <img src="{{qr_code}}" alt="QR Code" class="w-20 h-20">
                        </div>
                        <p class="text-xs text-center text-gray-600 mt-1 font-medium">Scan to Verify</p>
                    </div>
                </div>

                <!-- Student Details Grid -->
                <div class="grid grid-cols-3 gap-6 mb-5 text-center max-w-3xl w-full">
                    <div class="bg-white/80 backdrop-blur-sm rounded-lg p-3 shadow-md border border-amber-100">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1 font-semibold">Date of Birth</p>
                        <p class="text-sm font-semibold text-gray-800">{{dob}}</p>
                    </div>
                    <div class="bg-white/80 backdrop-blur-sm rounded-lg p-3 shadow-md border border-amber-100">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1 font-semibold">Father's Name</p>
                        <p class="text-sm font-semibold text-gray-800">{{father_name}}</p>
                    </div>
                    <div class="bg-white/80 backdrop-blur-sm rounded-lg p-3 shadow-md border border-amber-100">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1 font-semibold">Mother's Name</p>
                        <p class="text-sm font-semibold text-gray-800">{{mother_name}}</p>
                    </div>
                </div>

                <!-- Event Information -->
                <div class="text-center max-w-2xl mb-4">
                    <div class="bg-gradient-to-r from-purple-50 to-blue-50 rounded-lg px-6 py-3 shadow-md border border-purple-100 inline-block">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1 font-semibold">Event / Competition</p>
                        <p class="text-base font-bold text-purple-800 montserrat">{{event_name}}</p>
                        <p class="text-xs text-gray-600 mt-1">
                            <span class="font-semibold">Rank / Achievement:</span>
                            <span class="ml-1 px-2 py-0.5 bg-amber-100 text-amber-800 rounded font-semibold">{{rank}}</span>
                        </p>
                    </div>
                </div>

                <!-- Achievement Text -->
                <div class="text-center max-w-2xl mb-4">
                    <p class="text-sm text-gray-700 leading-relaxed montserrat">
                        For successfully completing the required course of study and demonstrating
                        <span class="font-semibold text-amber-700">excellence in academic achievement</span>.
                    </p>
                </div>

                <!-- Certificate ID & Date - Centered -->
                <div class="text-center">
                    <div class="bg-gradient-to-r from-amber-50 to-blue-50 backdrop-blur-sm rounded-lg px-6 py-2 shadow-md border border-amber-100 inline-block">
                        <p class="text-xs text-gray-600">
                            <span class="font-semibold text-gray-800">Certificate ID:</span>
                            <span class="font-mono text-amber-700 ml-1">{{certificate_id}}</span>
                            <span class="mx-2 text-amber-500">|</span>
                            <span class="font-semibold text-gray-800">Issue Date:</span>
                            <span class="ml-1">{{issued_date}}</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Footer - Signatures -->
            <div class="relative z-10 -mb-2">
                <div class="flex justify-center gap-6">
                    <!-- Signature 1 -->
                    <div class="text-center signature-container" data-signature="left">
                        <div class="h-12 mb-1 flex items-end justify-center">
                            <img src="{{signature_left}}" alt="" class="max-h-full max-w-full object-contain signature-image">
                        </div>
                        <div class="border-t-2 border-gray-800 pt-1.5 px-6 signature-line">
                            <p class="text-xs font-semibold text-gray-800 montserrat signature-title">{{signature_left_title}}</p>
                        </div>
                    </div>

                    <!-- Signature 2 -->
                    <div class="text-center signature-container" data-signature="middle">
                        <div class="h-12 mb-1 flex items-end justify-center">
                            <img src="{{signature_middle}}" alt="" class="max-h-full max-w-full object-contain signature-image">
                        </div>
                        <div class="border-t-2 border-gray-800 pt-1.5 px-6 signature-line">
                            <p class="text-xs font-semibold text-gray-800 montserrat signature-title">{{signature_middle_title}}</p>
                        </div>
                    </div>

                    <!-- Signature 3 -->
                    <div class="text-center signature-container" data-signature="right">
                        <div class="h-12 mb-1 flex items-end justify-center">
                            <img src="{{signature_right}}" alt="" class="max-h-full max-w-full object-contain signature-image">
                        </div>
                        <div class="border-t-2 border-gray-800 pt-1.5 px-6 signature-line">
                            <p class="text-xs font-semibold text-gray-800 montserrat signature-title">{{signature_right_title}}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Corner Decorations -->
        <div class="absolute top-6 left-6 w-12 h-12 border-l-4 border-t-4 border-amber-500/50 rounded-tl-lg"></div>
        <div class="absolute top-6 right-6 w-12 h-12 border-r-4 border-t-4 border-amber-500/50 rounded-tr-lg"></div>
        <div class="absolute bottom-6 left-6 w-12 h-12 border-l-4 border-b-4 border-amber-500/50 rounded-bl-lg"></div>
        <div class="absolute bottom-6 right-6 w-12 h-12 border-r-4 border-b-4 border-amber-500/50 rounded-br-lg"></div>

    </div>
</body>
</html>
HTML;
    }
}
