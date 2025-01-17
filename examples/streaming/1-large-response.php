<?php declare(strict_types=1);

use Amp\Http\Client\HttpClientBuilder;
use Amp\Http\Client\HttpException;
use Amp\Http\Client\Request;
use function Amp\now;

require __DIR__ . '/../.helper/functions.php';

// https://stackoverflow.com/a/2510540/2373138
function formatBytes(int $size, int $precision = 2): string
{
    $base = log($size, 1024);
    $suffixes = ['bytes', 'kB', 'MB', 'GB', 'TB'];

    return round(1024 ** ($base - floor($base)), $precision) . ' ' . $suffixes[(int) $base];
}

try {
    $start = now();

    // Instantiate the HTTP client
    $client = HttpClientBuilder::buildDefault();

    $request = new Request('http://speed.hetzner.de/100MB.bin');
    $request->setBodySizeLimit(128 * 1024 * 1024); // 128 MB
    $request->setTransferTimeout(120); // 120 seconds

    // Make an asynchronous HTTP request
    $response = $client->request($request);

    // Output the results
    printf(
        "HTTP/%s %d %s\r\n%s\r\n\r\n",
        $response->getProtocolVersion(),
        $response->getStatus(),
        $response->getReason(),
        (string) $response->getRequest()->getUri()
    );

    foreach ($response->getHeaders() as $field => $values) {
        foreach ($values as $value) {
            print "$field: $value\r\n";
        }
    }

    print "\n";

    $path = tempnam(sys_get_temp_dir(), "artax-streaming-");

    $file = Amp\File\openFile($path, "w");

    $bytes = 0;

    // The response body is an instance of Payload, which allows buffering or streaming by the consumers choice.
    // We could also use Amp\ByteStream\pipe() here, but we want to show some progress.
    while (null !== $chunk = $response->getBody()->read()) {
        $file->write($chunk);
        $bytes += strlen($chunk);

        print "\r" . formatBytes($bytes) . '    '; // blanks to remove previous output
    }

    $file->close();

    print sprintf(
        "\rDone in %.2f seconds with peak memory usage of %.2fMB.\n",
        now() - $start,
        (float) memory_get_peak_usage(true) / 1024 / 1024
    );

    $size = Amp\File\getSize($path);

    print sprintf("%s has a size of %.2fMB\r\n", $path, (float) $size / 1024 / 1024);
} catch (HttpException $error) {
    // If something goes wrong Amp will throw the exception where the promise was yielded.
    // The HttpClient::request() method itself will never throw directly, but returns a promise.
    echo $error;
}
