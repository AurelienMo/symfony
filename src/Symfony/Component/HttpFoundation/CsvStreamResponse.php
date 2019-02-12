<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation;

/**
 * CsvStreamResponse represents a csv file streamed HTTP response based on basic StreamedResponse.
 *
 * Callback is define to transform array datas and write transform result into csv file.
 *
 * @author Aurelien Morvan <morvan.aurelien@gmail.com>
 */
final class CsvStreamResponse extends StreamedResponse
{
    /**
     * Override StreamedResponse to return CSV file to attachment.
     *
     * @param array  $rows
     * @param string $fileName
     * @param int    $statusCode
     * @param array  $additionnalHeaders
     */
    public function __construct(
        array $rows,
        string $fileName,
        int $statusCode = self::HTTP_OK,
        array $additionnalHeaders = []
    ) {
        parent::__construct(
            function () use ($rows) {
                $this->transformArrayAndWriteCsv($rows);
            },
            $statusCode,
            array_merge(
                $this->getCsvHeaders($fileName),
                $additionnalHeaders
            )
        );
    }

    /**
     * Convert array datas and put datas to csv file
     *
     * @param array $rows
     */
    private function transformArrayAndWriteCsv(array $rows)
    {
        $tempfile = fopen('php://output', 'rb+');
        foreach ($rows as $row) {
            fputcsv($tempfile, $row);
        }
        fclose($tempfile);
    }

    /**
     * Build headers for specific csv format
     *
     * @param string $fileName
     *
     * @return array
     */
    private function getCsvHeaders(string $fileName)
    {
        return [
            'Content-Disposition' => sprintf('attachment; filename="%s"', $fileName),
            'Content-Type' => 'text/csv',
        ];
    }
}
