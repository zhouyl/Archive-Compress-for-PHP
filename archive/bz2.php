<?php
/**
 * bz2 文档压缩/解压
 *
 * @author  ZhouYL <aultoale@gmail.com>
 * @license http://apache.org/licenses/LICENSE-2.0.txt
 */
class Archive_Bz2 extends Archive
{

    /**
     * Compression Options
     * array(
     *     'blocksize' => Blocksize to use from 0-9
     *     'archive'   => Archive to use
     * )
     *
     * @var array
     */
    protected $_options = array(
        'blocksize' => 4,
        'archive'   => NULL,
    );

    /**
     * Class constructor
     *
     * @param array|NULL $options (Optional) Options to set
     */
    public function __construct($options = NULL)
    {
        if (! extension_loaded('bz2')) {
            throw new Archive_Exception('This filter needs the bz2 extension');
        }
        parent::__construct($options);
    }

    /**
     * Returns the set blocksize
     *
     * @return integer
     */
    public function getBlocksize()
    {
        return $this->_options['blocksize'];
    }

    /**
     * Sets a new blocksize
     *
     * @param  integer     $level
     * @return Archive_Bz2
     */
    public function setBlocksize($blocksize)
    {
        if (($blocksize < 0) || ($blocksize > 9)) {
            throw new Archive_Exception('Blocksize must be between 0 and 9');
        }

        $this->_options['blocksize'] = (int) $blocksize;

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
     * @param  string      $archive Archive to use
     * @return Archive_Bz2
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
            $file = bzopen($archive, 'w');
            if (! $file) {
                throw new Archive_Exception("Error opening the archive '{$archive}'");
            }

            bzwrite($file, $content);
            bzclose($file);
            $compressed = TRUE;
        } else {
            $compressed = bzcompress($content, $this->getBlocksize());
        }

        if (is_int($compressed)) {
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
        if (file_exists($content)) {
            $archive = $content;
        }

        if (file_exists($archive)) {
            $file = bzopen($archive, 'r');
            if (! $file) {
                throw new Archive_Exception("Error opening the archive '{$content}'");
            }

            $compressed = bzread($file);
            bzclose($file);
        } else {
            $compressed = bzdecompress($content);
        }

        if (is_int($compressed)) {
            throw new Archive_Exception('Error during decompression');
        }

        return $compressed;
    }

}
