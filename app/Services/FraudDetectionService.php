<?php

namespace App\Services;

use App\Models\FraudAttempt;
use App\Models\TestSession;

class FraudDetectionService
{
    protected const FUZZY_THRESHOLD = 85; // Fixed at 85% as per user requirement

    /**
     * Check for potential fraud/duplicate test attempts
     * Returns array with 'blocked' status and 'reason' if blocked
     */
    public function checkForFraud(
        string $assessmentId,
        string $email,
        string $firstName,
        string $lastName,
        ?string $ipAddress,
        ?string $deviceFingerprint
    ): array {
        // Check exact email match
        $emailMatch = $this->checkExactEmailMatch($assessmentId, $email);
        if ($emailMatch) {
            return $this->logAndBlock($assessmentId, 'email_exact', $emailMatch, [
                'email' => $email,
                'ip_address' => $ipAddress,
                'device_fingerprint' => $deviceFingerprint,
            ]);
        }

        // Check fuzzy email match
        $fuzzyEmailMatch = $this->checkFuzzyEmailMatch($assessmentId, $email);
        if ($fuzzyEmailMatch) {
            return $this->logAndBlock($assessmentId, 'email_fuzzy', $fuzzyEmailMatch['session'], [
                'email' => $email,
                'ip_address' => $ipAddress,
                'device_fingerprint' => $deviceFingerprint,
            ], $fuzzyEmailMatch['similarity']);
        }

        // Check name similarity
        $nameMatch = $this->checkNameSimilarity($assessmentId, $firstName, $lastName);
        if ($nameMatch) {
            return $this->logAndBlock($assessmentId, 'name_fuzzy', $nameMatch['session'], [
                'email' => $email,
                'first_name' => $firstName,
                'last_name' => $lastName,
            ], $nameMatch['similarity']);
        }

        // Check device fingerprint
        if ($deviceFingerprint) {
            $deviceMatch = $this->checkDeviceFingerprint($assessmentId, $deviceFingerprint);
            if ($deviceMatch) {
                return $this->logAndBlock($assessmentId, 'device', $deviceMatch, [
                    'email' => $email,
                    'device_fingerprint' => $deviceFingerprint,
                ]);
            }
        }

        // Check IP address (less strict - only flag, don't block)
        if ($ipAddress) {
            $ipMatch = $this->checkIpAddress($assessmentId, $ipAddress);
            if ($ipMatch) {
                // Log but don't block - just flag for review
                logger()->info('Same IP detected for assessment', [
                    'assessment_id' => $assessmentId,
                    'ip' => $ipAddress,
                    'previous_session' => $ipMatch->id,
                ]);
            }
        }

        return ['blocked' => false];
    }

    protected function checkExactEmailMatch(string $assessmentId, string $email): ?TestSession
    {
        return TestSession::where('assessment_id', $assessmentId)
            ->whereRaw('LOWER(email) = ?', [strtolower($email)])
            ->first();
    }

    protected function checkFuzzyEmailMatch(string $assessmentId, string $email): ?array
    {
        $normalizedEmail = strtolower(trim($email));
        $existingSessions = TestSession::where('assessment_id', $assessmentId)
            ->select('id', 'email')
            ->get();

        foreach ($existingSessions as $session) {
            $existingEmail = strtolower(trim($session->email));
            $similarity = $this->calculateSimilarity($normalizedEmail, $existingEmail);
            
            if ($similarity >= self::FUZZY_THRESHOLD && $normalizedEmail !== $existingEmail) {
                return [
                    'session' => $session,
                    'similarity' => $similarity,
                ];
            }
        }

        return null;
    }

    protected function checkNameSimilarity(string $assessmentId, string $firstName, string $lastName): ?array
    {
        $fullName = strtolower(trim($firstName . ' ' . $lastName));
        $existingSessions = TestSession::where('assessment_id', $assessmentId)
            ->select('id', 'first_name', 'last_name')
            ->get();

        foreach ($existingSessions as $session) {
            $existingName = strtolower(trim($session->first_name . ' ' . $session->last_name));
            $similarity = $this->calculateSimilarity($fullName, $existingName);
            
            if ($similarity >= self::FUZZY_THRESHOLD) {
                return [
                    'session' => $session,
                    'similarity' => $similarity,
                ];
            }
        }

        return null;
    }

    protected function checkDeviceFingerprint(string $assessmentId, string $fingerprint): ?TestSession
    {
        return TestSession::where('assessment_id', $assessmentId)
            ->where('device_fingerprint', $fingerprint)
            ->first();
    }

    protected function checkIpAddress(string $assessmentId, string $ipAddress): ?TestSession
    {
        return TestSession::where('assessment_id', $assessmentId)
            ->where('ip_address', $ipAddress)
            ->first();
    }

    protected function calculateSimilarity(string $str1, string $str2): float
    {
        if ($str1 === $str2) {
            return 100.0;
        }

        $levenshtein = levenshtein($str1, $str2);
        $maxLen = max(strlen($str1), strlen($str2));
        
        if ($maxLen === 0) {
            return 100.0;
        }

        return (1 - ($levenshtein / $maxLen)) * 100;
    }

    protected function logAndBlock(
        string $assessmentId,
        string $matchType,
        TestSession $matchedSession,
        array $data,
        ?float $similarity = null
    ): array {
        FraudAttempt::logAttempt(
            $assessmentId,
            $matchType,
            $data,
            $matchedSession->id,
            $similarity
        );

        return [
            'blocked' => true,
            'reason' => $this->getBlockReason($matchType),
            'match_type' => $matchType,
        ];
    }

    protected function getBlockReason(string $matchType): string
    {
        return match ($matchType) {
            'email_exact' => 'This email has already been used for this assessment.',
            'email_fuzzy' => 'A similar email has already been used for this assessment.',
            'name_fuzzy' => 'A similar name has already been used for this assessment.',
            'device' => 'This device has already been used for this assessment.',
            'ip' => 'This IP address has already been used for this assessment.',
            'combo' => 'Multiple matching criteria detected.',
            default => 'You are not allowed to take this assessment again.',
        };
    }
}
