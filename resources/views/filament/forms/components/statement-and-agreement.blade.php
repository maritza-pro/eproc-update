@php
    $contents = [
        'statement' => [
            [
                'title' => 'Accuracy of Information',
                'desc' => 'All information provided, including but not limited to company details, qualifications, certifications, financial data, and supporting documents, is true, accurate, and complete to the best of our knowledge.',
            ],
            [
                'title' => 'Compliance with Regulations',
                'desc' => 'The vendor complies with all applicable laws, regulations, and industry standards relevant to the procurement process and the goods/services offered.',
            ],
            [
                'title' => 'Authorization',
                'desc' => 'The individual submitting this data is duly authorized to act on behalf of the vendor and bind the vendor to the terms of this agreement.',
            ],
            [
                'title' => 'Data Updates',
                'desc' => 'The vendor agrees to promptly update any changes to the submitted data, including contact information, certifications, or other relevant details, to ensure ongoing accuracy.',
            ],
        ],

        'agreement' => [
            [
                'title' => 'Adherence to Procurement Policies',
                'desc' => 'The vendor shall adhere to all policies, procedures, and guidelines outlined by our company and the procuring entity during the procurement process.',
            ],
            [
                'title' => 'Confidentiality',
                'desc' => 'The vendor shall treat all information related to the procurement process, including bidding details and proprietary data, as confidential and shall not disclose it to unauthorized parties without prior written consent.',
            ],
            [
                'title' => 'Fair Competition',
                'desc' => 'The vendor commits to engaging in fair and ethical practices, refraining from any form of collusion, bid rigging, or other anti-competitive behavior.',
            ],
            [
                'title' => 'Liability for Errors',
                'desc' => 'The vendor assumes full responsibility for any errors, omissions, or misrepresentations in the submitted data and agrees to indemnify and the procuring entity against any resulting claims or losses.',
            ],
            [
                'title' => 'Acceptance of Terms',
                'desc' => 'Submission of data constitutes acceptance of the terms and conditions of this platform, including any additional terms specified in the procurement documentation.',
            ],
            [
                'title' => 'Data Privacy',
                'desc' => 'The vendor consents to the collection, processing, and storage of submitted data in accordance with our privacy policy and applicable data protection laws.',
            ],
        ],
    ];
@endphp

<div class="space-y-8">
    <h2 class="mb-4 text-lg font-bold text-title-color">
        Statement & Agreement
    </h2>
    @foreach ($contents as $key => $items)
        <section>
            <h3 class="mb-4 text-lg font-bold text-title-color">
                {{ Illuminate\Support\Str::title($key) }}
            </h3>
            <div class="mb-4 space-y-4">
                @foreach ($items as $item)
                    <div class="flex items-start">
                        <span class="w-6 font-semibold text-title-color">{{ $loop->iteration }}.</span>
                        <div class="flex-1 text-justify">
                            <span class="block font-semibold text-title-color">{{ $item['title'] }}</span>
                            <span>{{ $item['desc'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
            @unless ($loop->last)
                <hr class="mb-4 border-gray-300">
            @endunless
        </section>
    @endforeach
</div>

<style scoped>
    .text-title-color {
        color: #d97706;
    }
</style>