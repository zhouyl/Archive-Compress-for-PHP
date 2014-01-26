<?php
/**
 * rar 文档压缩/解压
 *
 * @author  ZhouYL <aultoale@gmail.com>
 * @license http://apache.org/licenses/LICENSE-2.0.txt
 */
class Archive_Rar extends Archive
{
    /**
     * Compression Options
     * array(
     *     'callback' => Callback for compression
     *     'archive'  => Archive to use
     *     'password' => Password to use
     *     'target'   => Target to write the files to
     * )
     *
     * @var array
     */
    protected $_options = array(
        'callback' => NULL,
        'archive'  => NULL,
        'password' => NULL,
        'target'   => '.',
    );

    /**
     * Class constructor
     *
     * @param array|NULL $options (Optional) Options to set
     */
    public function __construct($options = NULL)
    {
        if (! extension_loaded('rar')) {
            throw new Archive_Exception('This filter needs the rar extension');
        }
        parent::__construct($options);
    }

    /**
     * Returns the set callback for compression
     *
     * @return string
     */
    public function getCallback()
    {
        return $this->_options['callback'];
    }

    /**
     * Sets the callback to use
     *
     * @param  string      $callback
     * @return Archive_Rar
     */
    public function setCallback($callback)
    {
        if (! is_callable($callback)) {
            throw new Archive_Exception('Callback can not be accessed');
        }

        $this->_options['callback'] = $callback;

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
     * @return Archive_Rar
     */
    public function setArchive($archive)
    {
        $archive = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $archive);
        $this->_options['archive'] = (string) $archive;

        return $this;
    }

    /**
     * Returns the set password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->_options['password'];
    }

    /**
     * Sets the password to use
     *
     * @param  string      $password
     * @return Archive_Rar
     */
    public function setPassword($password)
    {
        $this->_options['password'] = (string) $password;

        return $this;
    }

    /**
     * Returns the set targetpath
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->_options['target'];
    }

    /**
     * Sets the targetpath to use
     *
     * @param  string      $target
     * @return Archive_Rar
     */
    public function setTarget($target)
    {
        if (! file_exists(dirname($target))) {
            throw new Archive_Exception("The directory '{$target}' does not exist");
        }

        $target = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $target);
        $this->_options['target'] = (string) $target;

        return $this;
    }

    /**
     * Compresses the given content
     *
     * @param  string|array $content
     * @return string
     */
    public function compress($content)
    {
        $callback = $this->getCallback();
        if (is_null($callback)) {
            throw new Archive_Exception('No compression callback available');
        }

        $options = $this->getOptions();
        unset($options['callback']);

        $result = call_user_func($callback, $options, $content);
        if ($result !== TRUE) {
            throw new Archive_Exception('Error compressing the RAR Archive');
        }

        return $this->getArchive();
    }

    /**
     * Decompresses the given content
     *
     * @param  string  $content
     * @return boolean
     */
    public function decompress($content)
    {
        $archive = $this->getArchive();
        if (file_exists($content)) {
            $archive = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, realpath($content));
        } elseif (empty($archive) || ! file_exists($archive)) {
            throw new Archive_Exception('RAR Archive not found');
        }

        $password = $this->getPassword();
        if (! is_null($password)) {
            $archive = rar_open($archive, $password);
        } else {
            $archive = rar_open($archive);
        }

        if (! $archive) {
            throw new Archive_Exception("Error opening the RAR Archive");
        }

        $target = $this->getTarget();
        if (! is_dir($target)) {
            $target = dirname($target);
        }

        $filelist = rar_list($archive);
        if (! $filelist) {
            throw new Archive_Exception("Error reading the RAR Archive");
        }

        foreach ($filelist as $file) {
            $file->extract($target);
        }

        rar_close($archive);

        return TRUE;
    }
}
