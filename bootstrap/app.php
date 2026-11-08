<?php

use App\Http\Middleware\CheckWebRole;
use App\Http\Middleware\CheckApiRole;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . "/../routes/web.php",
        api: __DIR__ . "/../routes/api.php",
        commands: __DIR__ . "/../routes/console.php",
        health: "/up",
    )

    /*
    |--------------------------------------------------------------------------
    | Middleware Configuration
    |--------------------------------------------------------------------------
    */

    ->withMiddleware(function (Middleware $middleware): void {
        // Enable Sanctum stateful API authentication.
        $middleware->statefulApi();

        // Register middleware aliases.
        $middleware->alias([
            "abilities" => CheckAbilities::class,
            "ability" => CheckForAnyAbility::class,
            "role" => CheckWebRole::class,
            "api-role" => CheckApiRole::class,
        ]);
    })

    /*
    |--------------------------------------------------------------------------
    | Exception Handling Configuration
    |--------------------------------------------------------------------------
    */

    ->withExceptions(function (Exceptions $exceptions): void {
        /*
        |--------------------------------------------------------------------------
        | Request Detection
        |--------------------------------------------------------------------------
        */

        $isApiRequest = static fn(Request $request): bool => $request->is(
            "api",
            "api/*",
        );

        /*
        |--------------------------------------------------------------------------
        | Database Connection Error Detection
        |--------------------------------------------------------------------------
        */

        $isDatabaseConnectionError = static function (
            Throwable $exception,
        ): bool {
            if (!($exception instanceof \PDOException)) {
                return false;
            }

            // SQLSTATE class 08 represents connection exceptions.
            $sqlState = (string) $exception->getCode();

            if (str_starts_with($sqlState, "08")) {
                return true;
            }

            // Some PDO drivers store SQLSTATE inside errorInfo.
            $errorInfo = $exception->errorInfo ?? null;

            return is_array($errorInfo) &&
                isset($errorInfo[0]) &&
                str_starts_with((string) $errorInfo[0], "08");
        };

        /*
        |--------------------------------------------------------------------------
        | HTTP Error Messages
        |--------------------------------------------------------------------------
        */

        $httpErrorTitle = static fn(int $status): string => match ($status) {
            400 => "Permintaan Tidak Valid",
            401 => "Autentikasi Diperlukan",
            403 => "Akses Ditolak",
            404 => "Halaman Tidak Ditemukan",
            405 => "Metode Tidak Diizinkan",
            408 => "Permintaan Kedaluwarsa",
            409 => "Konflik Data",
            419 => "Sesi Kedaluwarsa",
            422 => "Data Tidak Valid",
            429 => "Terlalu Banyak Permintaan",
            500 => "Kesalahan Server",
            502 => "Gateway Error",
            503 => "Layanan Tidak Tersedia",
            504 => "Gateway Timeout",
            default => "Terjadi Kesalahan",
        };

        $httpErrorMessage = static fn(int $status): string => match ($status) {
            400 => "Permintaan tidak dapat diproses.",
            401 => "Autentikasi diperlukan. Silakan masuk kembali.",
            403 => "Kamu tidak memiliki izin untuk melakukan tindakan ini.",
            404 => "Data atau halaman yang kamu cari tidak ditemukan.",
            405 => "Metode permintaan tidak diizinkan.",
            408 => "Waktu permintaan telah habis. Silakan coba lagi.",
            409 => "Permintaan bertentangan dengan kondisi data saat ini.",
            419 => "Sesi kamu telah kedaluwarsa. Silakan muat ulang halaman.",
            422 => "Data yang dikirim tidak dapat diproses.",
            429 => "Terlalu banyak permintaan. Silakan coba lagi sebentar.",
            500 => "Terjadi kesalahan pada server.",
            502 => "Server sementara tidak dapat memberikan respons.",
            503 => "Layanan sedang tidak tersedia.",
            504 => "Server tidak merespons dalam waktu yang ditentukan.",
            default => "Permintaan tidak dapat diproses.",
        };

        /*
        |--------------------------------------------------------------------------
        | JSON Response Detection
        |--------------------------------------------------------------------------
        */

        $exceptions->shouldRenderJsonWhen(
            fn(Request $request, Throwable $exception): bool => $isApiRequest(
                $request,
            ) || $request->expectsJson(),
        );

        /*
        |--------------------------------------------------------------------------
        | Exception Rendering
        |--------------------------------------------------------------------------
        */

        $exceptions->render(function (
            Throwable $exception,
            Request $request,
        ) use (
            $isApiRequest,
            $isDatabaseConnectionError,
            $httpErrorTitle,
            $httpErrorMessage,
        ) {
            /*
                |--------------------------------------------------------------------------
                | Determine Response Type
                |--------------------------------------------------------------------------
                */

            $wantsJson = $isApiRequest($request) || $request->expectsJson();

            /*
                |--------------------------------------------------------------------------
                | Preserve Explicit HTTP Responses
                |--------------------------------------------------------------------------
                */

            if ($exception instanceof HttpResponseException) {
                return $exception->getResponse();
            }

            /*
                |--------------------------------------------------------------------------
                | JSON Exception Handling
                |--------------------------------------------------------------------------
                */

            if ($wantsJson) {
                // Validation error.
                if ($exception instanceof ValidationException) {
                    return response()->json(
                        [
                            "error" => "VALIDATION_ERROR",
                            "message" => "Data yang dikirim tidak valid.",
                            "errors" => $exception->errors(),
                        ],
                        $exception->status,
                    );
                }

                // Authentication error.
                if ($exception instanceof AuthenticationException) {
                    return response()->json(
                        [
                            "error" => "UNAUTHENTICATED",
                            "message" =>
                                "Autentikasi diperlukan. Silakan masuk kembali.",
                        ],
                        401,
                    );
                }

                // Database connection error.
                if ($isDatabaseConnectionError($exception)) {
                    return response()->json(
                        [
                            "error" => "DATABASE_CONNECTION_FAILED",
                            "message" =>
                                "Tidak dapat terhubung ke server. Periksa koneksi internet dan coba lagi.",
                        ],
                        503,
                    );
                }

                // HTTP exception.
                if ($exception instanceof HttpExceptionInterface) {
                    $status = $exception->getStatusCode();

                    return response()->json(
                        [
                            "error" => "HTTP_ERROR",
                            "message" => $httpErrorMessage($status),
                        ],
                        $status,
                        $exception->getHeaders(),
                    );
                }

                // Unexpected server error.
                return response()->json(
                    [
                        "error" => "SERVER_ERROR",
                        "message" =>
                            "Terjadi kesalahan pada server. Silakan coba lagi.",
                    ],
                    500,
                );
            }

            /*
                |--------------------------------------------------------------------------
                | Preserve Laravel's Default Form Handling
                |--------------------------------------------------------------------------
                */

            if (
                $exception instanceof ValidationException ||
                $exception instanceof AuthenticationException
            ) {
                return null;
            }

            /*
                |--------------------------------------------------------------------------
                | Web HTTP Exception Handling
                |--------------------------------------------------------------------------
                */

            if ($exception instanceof HttpExceptionInterface) {
                $status = $exception->getStatusCode();

                return response()->view(
                    "errors.error",
                    [
                        "code" => $status,
                        "title" => $httpErrorTitle($status),
                        "message" => $httpErrorMessage($status),
                    ],
                    $status,
                    $exception->getHeaders(),
                );
            }

            /*
                |--------------------------------------------------------------------------
                | Preserve Detailed Development Errors
                |--------------------------------------------------------------------------
                */

            if (config("app.debug")) {
                return null;
            }

            /*
                |--------------------------------------------------------------------------
                | Production Database Connection Error
                |--------------------------------------------------------------------------
                */

            if ($isDatabaseConnectionError($exception)) {
                return response()->view(
                    "errors.error",
                    [
                        "code" => 503,
                        "title" => "Layanan Tidak Tersedia",
                        "message" =>
                            "Tidak dapat terhubung ke server. Silakan coba lagi nanti.",
                    ],
                    503,
                );
            }

            /*
                |--------------------------------------------------------------------------
                | Production Unexpected Error
                |--------------------------------------------------------------------------
                */

            return response()->view(
                "errors.error",
                [
                    "code" => 500,
                    "title" => "Kesalahan Server",
                    "message" =>
                        "Terjadi kesalahan saat memproses permintaan. Silakan coba lagi.",
                ],
                500,
            );
        });
    })

    ->create();
