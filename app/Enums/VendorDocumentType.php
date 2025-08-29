<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum VendorDocumentType: string implements HasLabel
{
    case DeedInformation = 'deed_information';
    case PengesahanSkKemenkumham = 'pengesahan_sk_kemenkumham';
    case DirectorInformation = 'director_information';

    case TradingBusinessLicenseSiup = 'trading_business_license_siup';
    case CompanyRegistrationTdp = 'company_registration_tdp';
    case BusinessDomicileLetterSkdu = 'business_domicile_letter_skdu';
    case TaxableEntrepreneurSppkp = 'taxable_entrepreneur_confirmation_letter_sppkp';
    case BusinessIdentificationNumberNib = 'business_identification_number_nib';
    case HinderOrdonantieHo = 'hinder_ordonantie_ho';
    case BusinessEntityCertificateSbu = 'business_entity_certificate_sbu';

    /**
     * Get the label for the vendor status.
     * Returns a human-readable label for the enum case.
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::DeedInformation => 'Deed Information',
            self::PengesahanSkKemenkumham => 'Pengesahan (SK Kemenkumham)',
            self::DirectorInformation => 'Director Information',

            self::TradingBusinessLicenseSiup => 'Trading Business License (SIUP)',
            self::CompanyRegistrationTdp => 'Company Registration (TDP)',
            self::BusinessDomicileLetterSkdu => 'Business Domicile Letter (SKDU)',
            self::TaxableEntrepreneurSppkp => 'Taxable Entrepreneur Confirmation Letter (SPPKP)',
            self::BusinessIdentificationNumberNib => 'Business Identification Number (NIB)',
            self::HinderOrdonantieHo => 'Hinder Ordonantie (HO)',
            self::BusinessEntityCertificateSbu => 'Business Entity Certificate (SBU)',
        };
    }

    public function group(): string
    {
        return match ($this) {
            self::DeedInformation,
            self::PengesahanSkKemenkumham,
            self::DirectorInformation => 'legality',

            self::TradingBusinessLicenseSiup,
            self::CompanyRegistrationTdp,
            self::BusinessDomicileLetterSkdu,
            self::TaxableEntrepreneurSppkp,
            self::BusinessIdentificationNumberNib,
            self::HinderOrdonantieHo,
            self::BusinessEntityCertificateSbu => 'licensing',
        };
    }

    public static function options(?string $group = null): array
    {
        $cases = $group
            ? array_filter(self::cases(), fn (self $c) => $c->group() === $group)
            : self::cases();

        $out = [];
        foreach ($cases as $c) {
            $out[$c->value] = $c->getLabel();
        }
        return $out;
    }

    public static function values(?string $group = null): array
    {
        $cases = $group
            ? array_filter(self::cases(), fn (self $c) => $c->group() === $group)
            : self::cases();

        return array_map(fn (self $c) => $c->value, $cases);
    }
}
