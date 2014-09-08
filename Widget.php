<?php
/**
 * Basic class for widgets with assets.
 *
 * All widgets with assets should extend this class.
 * Assets folder should be at the same dir as the widget class.
 * Assets folder should be named 'assets'
 * Every static content in assets folder will be automatically published.
 * Links to JS and CSS files will also be embedded in DOM.
 *
 * @author Alex Akr <opexus@gmail.com>
 */
class Widget extends CWidget
{
    private $_assetsUrl;
    private $_assetsFolder;

    public function init()
    {
        $this->publishAssets();
        $this->registerAssets();
        parent::init();
    }

    /**
     * Assets publishing
     */
    protected function publishAssets()
    {
        $this->_assetsUrl = Yii::app()->getAssetManager()->publish($this->getAssetsFolder());
    }

    /**
     * Recurisive registration of assets
     */
    protected function registerAssets()
    {
        $iterator = new RecursiveDirectoryIterator($this->getAssetsFolder());
        foreach (new RecursiveIteratorIterator($iterator) as $fullFileName => $file) {
            $fileUrl = $this->_assetsUrl.DIRECTORY_SEPARATOR.str_replace($this->getAssetsFolder(), '', $fullFileName);
            if ('css' === $file->getExtension()) {
                Yii::app()->getClientScript()->registerCssFile($fileUrl);
            } elseif ('js' === $file->getExtension()) {
                Yii::app()->getClientScript()->registerScriptFile($fileUrl);
            }
        }
    }

    /**
     * Assets folder
     *
     * @todo check if assets dir really exists
     * @return string
     */
    protected function getAssetsFolder()
    {
        if (null === $this->_assetsFolder) {
            $reflector = new ReflectionClass(get_called_class());
            $fn = $reflector->getFileName();
            $this->_assetsFolder = dirname($fn).DIRECTORY_SEPARATOR.'assets';
        }
        return $this->_assetsFolder;
    }
}
