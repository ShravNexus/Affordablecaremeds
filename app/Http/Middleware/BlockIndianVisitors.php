<?php

namespace App\Http\Middleware;

use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockIndianVisitors
{
    private const MESSAGE = 'This website is not available in your country.';

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->isIndianIp($request->ip())) {
            return $next($request);
        }

        $headers = ['Cache-Control' => 'private, no-store'];

        if ($request->is('api/*') || $request->expectsJson()) {
            return ApiResponse::error(self::MESSAGE, Response::HTTP_FORBIDDEN, headers: $headers);
        }

        return response()->view('errors.country-restricted', ['message' => self::MESSAGE], Response::HTTP_FORBIDDEN, $headers);
    }

    private function isIndianIp(?string $ip): bool
    {
        if (! filter_var($ip, FILTER_VALIDATE_IP)) {
            return false;
        }

        $address = inet_pton($ip);
        if (str_starts_with($address, "\0\0\0\0\0\0\0\0\0\0\xff\xff")) {
            $address = substr($address, 12);
        }

        if (! filter_var(inet_ntop($address), FILTER_VALIDATE_IP, FILTER_FLAG_GLOBAL_RANGE)) {
            return false;
        }

        $ranges = (require resource_path('data/india-ip-ranges.php'))[strlen($address)];
        $address = bin2hex($address);
        $low = 0;
        $high = count($ranges) - 1;

        while ($low <= $high) {
            $middle = intdiv($low + $high, 2);
            [$first, $last] = $ranges[$middle];

            if (strcmp($address, $first) < 0) {
                $high = $middle - 1;
            } elseif (strcmp($address, $last) > 0) {
                $low = $middle + 1;
            } else {
                return true;
            }
        }

        return false;
    }
}
