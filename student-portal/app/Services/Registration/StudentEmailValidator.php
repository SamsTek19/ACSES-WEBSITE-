<?php

namespace App\Services\Registration;

class StudentEmailValidator
{
    /**
     * Valid school email domain.
     */
    private const SCHOOL_DOMAIN = 'st.umat.edu.gh';

    /**
     * Email prefixes mapped to program classes.
     */
    private const CLASS_PREFIXES = [
        'cy' => 'Cyber Security',
        'is' => 'Information System',
        'ce' => 'Computer Science',
        'ro' => 'Robotics',
    ];

    /**
     * Check if the email is a valid school email.
     */
    public function isSchoolEmail(string $email): bool
    {
        $domain = $this->extractDomain($email);

        return strtolower($domain) === self::SCHOOL_DOMAIN;
    }

    /**
     * Check if the email prefix matches the selected class.
     */
    public function emailMatchesClass(string $email, string $class): bool
    {
        $prefix = $this->extractPrefix($email);

        if ($prefix === null) {
            return false;
        }

        $expectedClass = self::CLASS_PREFIXES[strtolower($prefix)] ?? null;

        return $expectedClass === $class;
    }

    /**
     * Get the expected class based on email prefix.
     */
    public function getExpectedClass(string $email): ?string
    {
        $prefix = $this->extractPrefix($email);

        if ($prefix === null) {
            return null;
        }

        return self::CLASS_PREFIXES[strtolower($prefix)] ?? null;
    }

    /**
     * Check if this email qualifies for auto-verification.
     * Auto-verification requires:
     * 1. School email domain (st.umat.edu.gh)
     * 2. Email prefix matches the selected class
     */
    public function canAutoVerify(string $email, string $class): bool
    {
        return $this->isSchoolEmail($email) && $this->emailMatchesClass($email, $class);
    }

    /**
     * Get validation error message for email/class mismatch.
     */
    public function getMismatchMessage(string $email, string $selectedClass): ?string
    {
        if (! $this->isSchoolEmail($email)) {
            return null; // No mismatch if not a school email
        }

        $expectedClass = $this->getExpectedClass($email);

        if ($expectedClass === null) {
            return 'Your school email prefix is not recognized. Expected prefixes: CY (Cyber Security), IS (Information System), CE (Computer Science), RO (Robotics).';
        }

        if ($expectedClass !== $selectedClass) {
            return "Your school email indicates you belong to {$expectedClass}, but you selected {$selectedClass}. Please select the correct program.";
        }

        return null;
    }

    /**
     * Extract the domain from an email address.
     */
    private function extractDomain(string $email): ?string
    {
        $parts = explode('@', $email);

        return $parts[1] ?? null;
    }

    /**
     * Extract the class prefix from a school email (first 2 characters before the hyphen).
     * e.g., cy-alfredob0123@st.umat.edu.gh -> cy
     */
    private function extractPrefix(string $email): ?string
    {
        $localPart = explode('@', $email)[0] ?? '';

        // Check if email follows the pattern: XX-name@domain
        if (preg_match('/^([a-zA-Z]{2})-/i', $localPart, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Get all valid class prefixes for documentation.
     */
    public static function getValidPrefixes(): array
    {
        return self::CLASS_PREFIXES;
    }

    /**
     * Get the school domain.
     */
    public static function getSchoolDomain(): string
    {
        return self::SCHOOL_DOMAIN;
    }
}
