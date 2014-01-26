#Archive Compress for PHP

###文档压缩/解压抽象类

```
// 创建 ZIP 压缩文件
$zip = new Archive_Zip();
$zip->setArchive('zip_compress.zip') // 指定输出的文件名
    ->compress('/var/compress_path'); // 压缩指定的目录到 ZIP

// 创建解压缩目录
mkdir("/var/decompress_path", 7770);

// 解压缩 ZIP 文件
$zip = new Archive_Zip();
$zip->setTarget('/var/decompress_path') // 解压到指定目录
    ->decompress('zip_compress.zip'); // 解压指定文件
```