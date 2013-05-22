<?php
class PHPSass extends Frontend
{
    public function __construct()
    {
        $this->import('Database');
    }

    public function parseFrontendTemplate($strContent, $strTemplate)
    {
        $this->compileSassFolders();

        return $strContent;
    }

    public function compileSassFolders()
    {
        $version = floatval(VERSION);

        if (!isset($GLOBALS['phpsass_compiled'])) {
            $library = __DIR__ . '/lib/phpsass/SassParser.php';

            if (file_exists($library)) {
                try {
                    require_once($library);

                    $q = $this->Database->prepare('SELECT * FROM tl_phpsass WHERE disable != "1"');
                    $folders = $q->execute()->fetchAllAssoc();

                    foreach($folders as $folder) {
                        if ($version < 3.0) {
                            $css_dir = TL_ROOT . '/' . $folder['css_dir'];
                            $sass_dir = TL_ROOT . '/' . $folder['sass_dir'];
                            $extensions_dir = (!empty($folder['extensions_dir'])) ? TL_ROOT . '/' . $folder['extensions_dir'] : NULL;
                            $images_dir = (!empty($folder['images_dir'])) ? TL_ROOT . '/' . $folder['images_dir'] : NULL;
                            $javascripts_dir = (!empty($folder['javascripts_dir'])) ? TL_ROOT . '/' . $folder['javascripts_dir'] : NULL;
                        } else {
                            $css_dir = TL_ROOT . '/' . FilesModel::findOneById($folder['css_dir'])->path;
                            $sass_dir = TL_ROOT . '/' . FilesModel::findOneById($folder['sass_dir'])->path;
                            $extensions_dir = (!empty($folder['extensions_dir'])) ? TL_ROOT . '/' . FilesModel::findOneById($folder['extensions_dir'])->path : NULL;
                            $images_dir = (!empty($folder['images_dir'])) ? TL_ROOT . '/' . FilesModel::findOneById($folder['images_dir']) : NULL;
                            $javascripts_dir = (!empty($folder['javascripts_dir'])) ? TL_ROOT . '/' . FilesModel::findOneById($folder['javascripts_dir'])->path : NULL;
                        }

                        $output_style = $folder['output_style'];

                        $config = array(
                            'style' => $folder['output_style'],
                            'syntax' => SassFile::SCSS,
                            'load_paths' => (!empty($extensions_dir))?array($extensions_dir):array(),
                            'cache' => FALSE,
                            'debug' => $GLOBALS['TL_CONFIG']['displayErrors'],
                        );

                        $this->compileSassFolder($sass_dir,$css_dir,$config);
                    }
                } catch (Exception $e) {
                    $this->log('Error while loading SassParser "'.$library.'"', 'PHPSass compileSassFolders', TL_ERROR);
                }
            }

            $GLOBALS['phpsass_compiled'] = true;
        }
    }

    public function compileSassFolder($sass_dir,$css_dir,$config)
    {
        if (is_dir($sass_dir)) {
            $config['filename'] = array(
                'dirname' => dirname($sass_dir)
            );

            $files = scandir($sass_dir);

            foreach($files as $file) {
                if (!in_array($file,array('.','..'))) {
                    $file_path = $sass_dir.'/'.$file;

                    if (is_file($file_path)) {
                        $sass_ext = substr($file,-5);

                        if (in_array($sass_ext,array('.scss','.sass'))) {
                            // ignore partials
                            if (substr($file,0,1) != '_') {
                                $config['filename']['basename'] = basename($file);

                                if ($sass_ext == '.sass') {
                                    $config['syntax'] = SassFile::SASS;
                                }

                                $css = $this->getCompiledSassFile($file_path,$config);

                                $css_path = $css_dir.'/'.substr($file,0,-5).'.css';

                                // create css directory if not existing yet
                                if (!file_exists($css_dir)) {
                                    mkdir($css_dir,0755, TRUE);
                                }

                                if (is_dir($css_dir)) {
                                    file_put_contents($css_path,$css);
                                }
                            }
                        }
                    } elseif (is_dir($file_path)) {
                        // recursively process files in subfolders
                        $this->compileSassFolder($file_path,$css_dir.'/'.$file,$config);
                    }
                }
            }
        }
    }

    public function getCompiledSassFile($file_path,$options)
    {
        $sass_parser = new SassParser($options);
        return $sass_parser->toCss($file_path,TRUE);
    }
}
