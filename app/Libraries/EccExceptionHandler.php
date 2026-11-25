<?php

declare(strict_types=1);

namespace App\Libraries;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Debug\BaseExceptionHandler;
use CodeIgniter\Debug\ExceptionHandlerInterface;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\Exceptions\HTTPException;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Paths;
use Throwable;

/**
 * Penangan exception terpusat untuk Evidence Command Center (ECC).
 * - Menghasilkan Kode Rujukan unik (ECC-YYYYMMDD-XXXX) pada error 5xx agar mudah dilacak Admin di log.
 * - Mencegah kebocoran pesan teknis pada halaman error maupun respons AJAX di production.
 */
class EccExceptionHandler extends BaseExceptionHandler implements ExceptionHandlerInterface
{
    use ResponseTrait;

    private ?RequestInterface $request = null;
    private ?ResponseInterface $response = null;
    private ?string $refId = null;

    public function handle(
        Throwable $exception,
        RequestInterface $request,
        ResponseInterface $response,
        int $statusCode,
        int $exitCode
    ): void {
        $this->request  = $request;
        $this->response = $response;
        $this->refId    = null;

        if ($request instanceof IncomingRequest) {
            try {
                $response->setStatusCode($statusCode);
            } catch (HTTPException) {
                $statusCode = 500;
                $response->setStatusCode($statusCode);
            }

            if ($statusCode >= 500 && !in_array($statusCode, $this->config->ignoreCodes, true)) {
                $this->refId = self::generateReferenceId();
                $this->logWithReference($this->refId, $exception, $request, $statusCode);
            }

            if (!headers_sent()) {
                header(
                    sprintf(
                        'HTTP/%s %s %s',
                        $request->getProtocolVersion(),
                        $response->getStatusCode(),
                        $response->getReasonPhrase()
                    ),
                    true,
                    $statusCode
                );
            }

            $accept = strtolower($request->getHeaderLine('accept'));
            $isNonHtml = ($accept !== '' && !str_contains($accept, 'text/html') && !str_contains($accept, '*/*'))
                || $request->isAJAX();

            if ($isNonHtml) {
                if ($this->isDisplayErrorsEnabled()) {
                    $data = $this->collectVars($exception, $statusCode);
                } else {
                    $data = [
                        'status'  => 'error',
                        'message' => self::friendlyMessageForStatus($statusCode),
                    ];
                    if ($this->refId !== null) {
                        $data['ref_id'] = $this->refId;
                    }
                }

                $this->respond($data, $statusCode)->send();

                if (ENVIRONMENT !== 'testing') {
                    exit($exitCode);
                }

                return;
            }
        }

        $addPath = ($request instanceof IncomingRequest ? 'html' : 'cli') . DIRECTORY_SEPARATOR;
        $path    = $this->viewPath . $addPath;
        $altPath = rtrim((new Paths())->viewDirectory, '\\/ ')
            . DIRECTORY_SEPARATOR . 'errors' . DIRECTORY_SEPARATOR . $addPath;

        $view    = $this->determineView($exception, $path, $statusCode);
        $altView = $this->determineView($exception, $altPath, $statusCode);

        $viewFile = null;
        if (is_file($path . $view)) {
            $viewFile = $path . $view;
        } elseif (is_file($altPath . $altView)) {
            $viewFile = $altPath . $altView;
        }

        $this->render($exception, $statusCode, $viewFile);

        if (ENVIRONMENT !== 'testing') {
            exit($exitCode);
        }
    }

    /**
     * Membuat kode rujukan pendek untuk dicocokkan dengan log aplikasi.
     */
    public static function generateReferenceId(): string
    {
        try {
            $suffix = strtoupper(bin2hex(random_bytes(2)));
        } catch (Throwable) {
            $suffix = strtoupper(substr(md5(uniqid('', true)), 0, 4));
        }

        return 'ECC-' . date('Ymd') . '-' . $suffix;
    }

    /**
     * Pesan singkat non-teknis untuk respons API/AJAX berdasarkan kode HTTP.
     */
    public static function friendlyMessageForStatus(int $statusCode): string
    {
        return match ($statusCode) {
            400 => 'Permintaan tidak dapat diproses. Silakan muat ulang dan coba lagi.',
            403 => 'Akses ditolak atau sesi halaman telah berakhir.',
            404 => 'Data atau halaman yang diminta tidak ditemukan.',
            429 => 'Aktivitas terlalu cepat. Tunggu sebentar lalu coba lagi.',
            default => 'Terjadi kendala saat memproses permintaan. Silakan coba lagi.',
        };
    }

    protected function collectVars(Throwable $exception, int $statusCode): array
    {
        $vars = parent::collectVars($exception, $statusCode);
        $vars['refId'] = $this->refId;

        return $vars;
    }

    protected function determineView(
        Throwable $exception,
        string $templatePath,
        int $statusCode = 500
    ): string {
        $view = 'production.php';

        if ($this->isDisplayErrorsEnabled()) {
            $view = 'error_exception.php';
        }

        if ($exception instanceof PageNotFoundException) {
            return 'error_404.php';
        }

        $templatePath = rtrim($templatePath, '\\/ ') . DIRECTORY_SEPARATOR;

        if (is_file($templatePath . 'error_' . $statusCode . '.php')) {
            return 'error_' . $statusCode . '.php';
        }

        return $view;
    }

    private function logWithReference(
        string $refId,
        Throwable $exception,
        IncomingRequest $request,
        int $statusCode
    ): void {
        try {
            $method = strtoupper($request->getMethod());
            $path   = '/' . ltrim($request->getUri()->getPath(), '/');
            $ip     = $request->getIPAddress();
            $userId = '-';

            if (function_exists('session')) {
                try {
                    $uid = session()->get('id') ?? session()->get('user_id');
                    if ($uid !== null && $uid !== '') {
                        $userId = (string) $uid;
                    }
                } catch (Throwable) {
                    // Sesi belum terinisialisasi; biarkan '-'
                }
            }

            $file = $exception->getFile();
            if (defined('ROOTPATH') && str_starts_with($file, ROOTPATH)) {
                $file = ltrim(substr($file, strlen(ROOTPATH)), '\\/');
            }

            log_message(
                'critical',
                sprintf(
                    '[%s] HTTP %d %s %s | user=%s | ip=%s | %s: %s (%s:%d)',
                    $refId,
                    $statusCode,
                    $method,
                    $path,
                    $userId,
                    $ip,
                    $exception::class,
                    $exception->getMessage(),
                    $file,
                    $exception->getLine()
                )
            );
        } catch (Throwable) {
            // Jangan pernah membiarkan kegagalan pencatatan log memicu crash sekunder
        }
    }

    private function isDisplayErrorsEnabled(): bool
    {
        return in_array(
            strtolower((string) ini_get('display_errors')),
            ['1', 'true', 'on', 'yes'],
            true
        );
    }
}
