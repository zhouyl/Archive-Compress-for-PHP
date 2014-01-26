<?php
/**
 * lzf 文档压缩/解压
 *
 * @author  Yellow.Chow <aultoale@gmail.com>
 * @license http://apache.org/licenses/LICENSE-2.0.txt
 */
class Archive_Lzf extends Archive
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        if ( ! extension_loaded('lzf')) {
            throw new Archive_Exception('This filter needs the lzf extension');
        }
    }

    /**
     * Compresses the given content
     *
     * @param  string $content
     * @return string
     */
    public function compress($content)
    {
        $compressed = lzf_compress($content);
        if (! $compressed) {
            throw new Archive_Exception('Error during compression');
        }

        return $compressed;
    }

    /**
     * Decompresses the given content
     *
     * @param  string $content
     * @return string
     */
    public function decompress($content)
    {
        $compressed = lzf_decompress($content);
        if (! $compressed) {
            throw new Archive_Exception('Error during compression');
        }

        return $compressed;
    }
}
