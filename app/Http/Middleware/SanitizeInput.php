<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeInput
{
    protected array $except = [
        'password',
        'password_confirmation',
        'current_password',
    ];

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
                // Null bytes + trim — that's it.
                // SQL injection is handled by parameterized queries.
                // XSS is handled by Blade {{ }} escaping.
                $input[$key] = trim(str_replace(chr(0), '', $value));
            }
        }
        return $input;
    }
}
