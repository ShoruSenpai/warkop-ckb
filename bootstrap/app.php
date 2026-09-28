<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

//
use App\Http\Middleware\CheckWebRole;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . "/../routes/web.php",
        api: __DIR__ . "/../routes/api.php",
        commands: __DIR__ . "/../routes/console.php",
        health: "/up",
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();

        $middleware->alias([
            "abilities" => CheckAbilities::class,
            "ability" => CheckForAnyAbility::class,
            "role" => CheckWebRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn(Request $request) => $request->is("api/*") ||
                $request->expectsJson(),
        );

        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is("api/*") || $request->expectsJson()) {
                if (
                    $e instanceof QueryException &&
                    str_starts_with((string) $e->getCode(), "08")
                ) {
                    return response()->json(
                        [
                            "error" => "DATABASE_CONNECTION_FAILED",
                            "message" =>
                                "Tidak dapat terhubung ke server. Periksa koneksi internet dan coba lagi.",
                        ],
                        503,
                    );
                }
                if ($e instanceof HttpExceptionInterface) {
                    $status = $e->getStatusCode();

                    return response()->json(
                        [
                            "error" => "HTTP_ERROR",
                            "message" => match ($status) {
                                403
                                    => "Kamu tidak memiliki izin untuk melakukan tindakan ini.",

                                404
                                    => "Data atau halaman yang kamu cari tidak ditemukan.",

                                405 => "Metode permintaan tidak diizinkan.",

                                419
                                    => "Sesi kamu telah kedaluwarsa. Silakan muat ulang halaman.",

                                429
                                    => "Terlalu banyak permintaan. Silakan coba lagi sebentar.",

                                500 => "Terjadi kesalahan pada server.",

                                503 => "Layanan sedang tidak tersedia.",

                                default => "Permintaan tidak dapat diproses.",
                            },
                        ],
                        $status,
                    );
                }
                return response()->json(
                    [
                        "error" => "SERVER_ERROR",
                        "message" =>
                            "Terjadi kesalahan pada server. Silakan coba lagi.",
                    ],
                    500,
                );
            }

            if ($e instanceof HttpExceptionInterface) {
                $status = $e->getStatusCode();

                return response()->view(
                    "errors.error",
                    [
                        "code" => $status,

                        "title" => match ($status) {
                            403 => "Akses Ditolak",
                            404 => "Halaman Tidak Ditemukan",
                            405 => "Metode Tidak Diizinkan",
                            419 => "Sesi Kedaluwarsa",
                            429 => "Terlalu Banyak Permintaan",
                            503 => "Layanan Tidak Tersedia",

                            default => "Terjadi Kesalahan",
                        },

                        "message" => match ($status) {
                            403
                                => "Kamu tidak memiliki izin untuk mengakses halaman ini.",

                            404 => "Halaman yang kamu cari tidak tersedia.",

                            405
                                => "Permintaan tidak dapat dilakukan dengan metode tersebut.",

                            419
                                => "Sesi kamu telah kedaluwarsa. Silakan muat ulang halaman.",

                            429
                                => "Mohon tunggu sebentar sebelum mencoba lagi.",

                            503 => "Layanan sedang tidak dapat diakses.",

                            default
                                => "Terjadi kesalahan saat memproses permintaan.",
                        },
                    ],
                    $status,
                );
            }

            return null;
        });
    })
    ->create();
