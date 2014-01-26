<?php
/**
 * gzip 文档压缩/解压
 *
 * @author  ZhouYL <aultoale@gmail.com>
 * @license http://apache.org/licenses/LICENSE-2.0.txt
 */
class Archive_Gz extends Archive
{

    /**
     * Compression Options
     * array(
     *     'level'    => Compression level 0-9
     *     'mode'     => Compression mode, can be 'compress', 'deflate'
     *     'archive'  => Archive to use
     * )
     *
     * @var array
     */
    protected $_options = array(
        'level'   => 9,
        'mode'    => 'compress',
        'archive' => NULL,
    );

    /**
     * Class constructor
     *
     * @param array|NULL $options (Optional) Options to set
     */
    public function __construct($options = NULL)
    {
        if (! extension_loaded('zlib')) {
            throw new Archive_Exception('This filter needs the zlib extension');
        }

        parent::__construct($options);
    }

    /**
     * Returns the set compression level
     *
     * @return integer
     */
    public function getLevel()
    {
        return $this->_options['level'];
    }

    /**
     * Sets a new compression level
     *
     * @param  integer    $level
     * @return Archive_Gz
     */
    public function setLevel($level)
    {
        if (($level < 0) || ($level > 9)) {
            throw new Archive_Exception('Level must be between 0 and 9');
        }

        $this->_options['level'] = (int) $level;

        return $this;
    }

    /**
     * Returns the set compression mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->_options['mode'];
    }

    /**
     * Sets a new compression mode
     *
     * @param string $mode Supported are 'compress', 'deflate' and 'file'
     */
    public function setMode($mode)
    {
        if (($mode != 'compress') && ($mode != 'deflate')) {
            throw new Archive_Exception('Given compression mode not supported');
        }

        $this->_options['mode'] = $mode;

        return $this;
    }

    /**
     * Returns the set archive
     *
     * @return string
     */
    public function getArchive()
    {
        return $this->_options['archive'];
    }

    /**
     * Sets the archive to use for de-/compression
     *
     * @param  string     $archive Archive to use
     * @return Archive_Gz
     */
    public function setArchive($archive)
    {
        $this->_options['archive'] = (string) $archive;

        return $this;
    }

    /**
     * Compresses the given content
     *
     * @param  string $content
     * @return string
     */
    public function compress($content)
    {
        $archive = $this->getArchive();
        if (! empty($archive)) {
            $file = gzopen($archive, 'w'.$this->getLevel());
            if (! $file) {
                throw new Archive_Exception("Error opening the archive '{$this->_options['archive']}'");
            }

            gzwrite($file, $content);
            gzclose($file);
            $compressed = TRUE;
        } elseif ($this->_options['mode'] == 'deflate') {
            $compressed = gzdeflate($content, $this->getLevel());
        } else {
            $compressed = gzcompress($content, $this->getLevel());
        }

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
        $archive = $this->getArchive();
        $mode    = $this->getMode();
        if (file_exists($content)) {
            $archive = $content;
        }

        if (file_exists($archive)) {
            $handler = fopen($archive, 'rb');
            if (! $handler) {
                throw new Archive_Exception("Error opening the archive '{$archive}'");
            }

            fseek($handler, -4, SEEK_END);
            $packet = fread($handler, 4);
            $bytes  = unpack("V", $packet);
            $size   = end($bytes);
            fclose($handler);

            $file       = gzopen($archive, 'r');
            $compressed = gzread($file, $size);
            gzclose($file);
        } elseif ($mode == 'deflate') {
            $compressed = gzinflate($content);
        } else {
            $compressed = gzuncompress($content);
        }

        if (! $compressed) {
            throw new Archive_Exception('Error during compression');
        }

        return $compressed;
    }

}
