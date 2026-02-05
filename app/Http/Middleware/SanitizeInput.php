<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeInput
{
    /**
     * Fields that should not be sanitized
     */
    protected array $except = [
        'password',
        'password_confirmation',
        'current_password',
    ];

    /**
     * Sanitize all input to prevent XSS attacks
     */
    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->all();
        
        $request->merge($this->sanitize($input));
        
        return $next($request);
    }

    protected function sanitize(array $input): array
    {
        foreach ($input as $key => $value) {
            if (in_array($key, $this->except, true)) {
                continue;
            }

            if (is_array($value)) {
                $input[$key] = $this->sanitize($value);
            } elseif (is_string($value)) {
                $input[$key] = $this->sanitizeString($value);
            }
        }

        return $input;
    }

    protected function sanitizeString(string $value): string
    {
        // Remove null bytes
        $value = str_replace(chr(0), '', $value);
        
        // HTML encode special characters
        $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8', false);
        
        // Remove potential SQL injection patterns
        $sqlPatterns = [
            '/\bUNION\b/i',
            '/\bSELECT\b.*\bFROM\b/i',
            '/\bDROP\b.*\bTABLE\b/i',
            '/\bINSERT\b.*\bINTO\b/i',
            '/\bDELETE\b.*\bFROM\b/i',
            '/--/',
            '/;.*\bDROP\b/i',
        ];
        
        foreach ($sqlPatterns as $pattern) {
            if (preg_match($pattern, $value)) {
                logger()->warning('Potential SQL injection blocked', [
                    'value' => substr($value, 0, 100),
                ]);
                return '';
            }
        }
        
        return trim($value);
    }
}
