<?php

declare(strict_types = 1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum VendorDocumentType: string implements HasLabel
{
    // TODO : Make sure aja nanti bisa multi bahasa
    case BusinessDomicileLetterSKDU = 'business_domicile_letter_skdu';
    case BusinessEntityCertificateSBU = 'business_entity_certificate_sbu';
    case BusinessIdentificationNumberNIB = 'business_identification_number_nib';
    case CompanyRegistrationTDP = 'company_registration_tdp';
    case DeedInformation = 'deed_information';
    case HinderOrdonantieHO = 'hinder_ordonantie_ho';
    case PengesahanKemenkumham = 'pengesahan_sk_kemenkumham';
    case TaxableEntrepreneurSPPKP = 'taxable_entrepreneur_confirmation_letter_sppkp';

    case TradingBusinessLicenseSIUP = 'trading_business_license_siup';

    public function category(): string
    {
        return match ($this) {
            self::DeedInformation,
            self::PengesahanKemenkumham => 'legality',

            self::TradingBusinessLicenseSIUP,
            self::CompanyRegistrationTDP,
            self::BusinessDomicileLetterSKDU,
            self::TaxableEntrepreneurSPPKP,
            self::BusinessIdentificationNumberNIB,
            self::HinderOrdonantieHO,
            self::BusinessEntityCertificateSBU => 'licensing',
        };
    }

    /**
     * Get the label for the vendor status.
     * Returns a human-readable label for the enum case.
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::DeedInformation => 'Deed Information',
            self::PengesahanKemenkumham => 'Pengesahan (SK Kemenkumham)',

            self::TradingBusinessLicenseSIUP => 'Trading Business License (SIUP)',
            self::CompanyRegistrationTDP => 'Company Registration (TDP)',
            self::BusinessDomicileLetterSKDU => 'Business Domicile Letter (SKDU)',
            self::TaxableEntrepreneurSPPKP => 'Taxable Entrepreneur Confirmation Letter (SPPKP)',
            self::BusinessIdentificationNumberNIB => 'Business Identification Number (NIB)',
            self::HinderOrdonantieHO => 'Hinder Ordonantie (HO)',
            self::BusinessEntityCertificateSBU => 'Business Entity Certificate (SBU)',
        };
    }

    public static function options(?string $group = null): array
    {
        $cases = $group
            ? array_filter(self::cases(), fn (self $case): bool => $case->category() === $group)
            : self::cases();

        $filtered = [];

        foreach ($cases as $case) {
            $filtered[$case->value] = $case->getLabel();
        }

        return $filtered;
    }
}
