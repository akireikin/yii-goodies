<?php
/**
 * Базовый класс для виджетов с ресурсами (assets).
 *
 * От него должны наследоваться все виджеты, имеющие ресурсы.
 * Папка с ресурсами для виджета должна распологаться на том же уровне,
 * что и класс виджета и иметь название 'assets'.
 * Все ресурсы расположенные в этой папке будут автоматически опубликованы.
 * Элементы, указывающие на JS и CSS файлы будут так-же добавлены в DOM.
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
     * регистрация ресурсов
     */
    protected function publishAssets()
    {
        $this->_assetsUrl = Yii::app()->getAssetManager()->publish($this->getAssetsFolder());
    }

    /**
     * рекурсивная регистрация всех ресурсов
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
     * Физическая папка с ресурсами на сервере
     *
     * @todo проверять существует ли директория assets
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
