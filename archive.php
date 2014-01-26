<?php
/**
 * 文档压缩/解压抽象类
 *
 * <code>
 *     // 创建 ZIP 压缩文件
 *     $zip = new Archive_Zip();
 *     $zip->setArchive('zip_compress.zip') // 指定输出的文件名
 *         ->compress('/var/compress_path'); // 压缩指定的目录到 ZIP
 *
 *     // 创建解压缩目录
 *     mkdir("/var/decompress_path", 7770);
 *
 *     // 解压缩 ZIP 文件
 *     $zip = new Archive_Zip();
 *     $zip->setTarget('/var/decompress_path') // 解压到指定目录
 *         ->decompress('zip_compress.zip'); // 解压指定文件
 * </code>
 *
 * @author  ZhouYL <aultoale@gmail.com>
 * @license http://apache.org/licenses/LICENSE-2.0.txt
 */
abstract class Archive
{

    /**
     * Class constructor
     *
     * @param array $options (Optional) Options to set
     */
    public function __construct(array $options = NULL)
    {

        if (is_array($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * Returns one or all set options
     *
     * @param  string $option (Optional) Option to return
     * @return mixed
     */
    public function getOptions($option = NULL)
    {
        if ($option === NULL) {
            return $this->_options;
        }

        if ( ! array_key_exists($option, $this->_options)) {
            return NULL;
        }

        return $this->_options[$option];
    }

    /**
     * Sets all or one option
     *
     * @param  array   $options
     * @return Archive
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $option) {
            $method = 'set'.$key;
            if (method_exists($this, $method)) {
                $this->$method($option);
            }
        }

        return $this;
    }

    /**
     * Compresses the given content
     *
     * @param  string $content
     * @return string
     */
    abstract public function compress($content);

    /**
     * Decompresses the given content
     *
     * @param  string $content
     * @return string
     */
    abstract public function decompress($content);

}
