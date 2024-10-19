<?php

/**
 * 图片上方添加一层蒙版显示水印内容，直接复制图片地址无水印效果。（划重点“伪”）
 * @package 伪水印插件
 * @author C4rpeDime
 * @version 1.0.0
 * @link https://www.1042.net
 */
class Watermark_Plugin implements Typecho_Plugin_Interface
{
    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Archive')->footer = array('Watermark_Plugin', 'render');
    }

    public static function deactivate()
    {
        // 插件停用时操作
    }

    public static function config(Typecho_Widget_Helper_Form $form)
    {
        $textContent = new Typecho_Widget_Helper_Form_Element_Text('watermarkText', NULL, '水印', _t('水印文本内容'));
        $form->addInput($textContent);

        $textColor = new Typecho_Widget_Helper_Form_Element_Text('watermarkTextColor', NULL, '#ffffff', _t('水印文本颜色'));
        $form->addInput($textColor);

        $textSize = new Typecho_Widget_Helper_Form_Element_Select('watermarkTextSize', 
            array(10 => '10px', 12 => '12px', 14 => '14px', 16 => '16px', 18 => '18px'), 
            12, _t('水印文本大小'));
        $form->addInput($textSize);

        $textOpacity = new Typecho_Widget_Helper_Form_Element_Select('watermarkTextOpacity', 
            array(0 => '0%', 50 => '50%', 100 => '100%'), 
            100, _t('水印文本透明度'));
        $form->addInput($textOpacity);

        $position = new Typecho_Widget_Helper_Form_Element_Select('watermarkPosition', 
            array('top-left' => '左上', 'top-right' => '右上', 'bottom-left' => '左下', 'bottom-right' => '右下', 'center' => '中间'), 
            'bottom-right', _t('水印文本位置'));
        $form->addInput($position);
    }

    public static function personalConfig(Typecho_Widget_Helper_Form $form)
    {
        // 用户个人配置
    }

    public static function render()
    {
        $options = Helper::options();
        $pluginOptions = $options->plugin('Watermark');

        $text = isset($pluginOptions->watermarkText) ? htmlspecialchars($pluginOptions->watermarkText, ENT_QUOTES, 'UTF-8') : '水印';
        $textColor = isset($pluginOptions->watermarkTextColor) ? htmlspecialchars($pluginOptions->watermarkTextColor, ENT_QUOTES, 'UTF-8') : '#ffffff';
        $textSize = isset($pluginOptions->watermarkTextSize) ? htmlspecialchars($pluginOptions->watermarkTextSize, ENT_QUOTES, 'UTF-8') : '12px';
        $textOpacity = isset($pluginOptions->watermarkTextOpacity) ? $pluginOptions->watermarkTextOpacity : 100;
        $position = isset($pluginOptions->watermarkPosition) ? $pluginOptions->watermarkPosition : 'bottom-right';

        // 根据选择的位置设置 CSS
        $positionStyles = '';
        switch ($position) {
            case 'top-left':
                $positionStyles = 'top: 10px; left: 10px;';
                break;
            case 'top-right':
                $positionStyles = 'top: 10px; right: 10px;';
                break;
            case 'bottom-left':
                $positionStyles = 'bottom: 10px; left: 10px;';
                break;
            case 'bottom-right':
                $positionStyles = 'bottom: 10px; right: 10px;';
                break;
            case 'center':
                $positionStyles = 'top: 50%; left: 50%; transform: translate(-50%, -50%);';
                break;
        }

        echo <<<HTML
<style>
.watermark {
    position: relative;
    display: inline-block;
}
.watermark-text {
    position: absolute;
    color: $textColor;
    font-size: {$textSize};
    opacity: {$textOpacity}%;
    pointer-events: none; /* 防止水印文本影响点击操作 */
    $positionStyles
    z-index: 1; /* 确保文本在上层 */
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    let contentElements = document.querySelectorAll('.post-content img');
    contentElements.forEach(function (element) {
        let wrapper = document.createElement('div');
        wrapper.className = 'watermark';
        element.parentNode.insertBefore(wrapper, element);
        wrapper.appendChild(element);

        let textElement = document.createElement('div');
        textElement.className = 'watermark-text';
        textElement.innerText = '$text';
        wrapper.appendChild(textElement);
    });
});
</script>
HTML;
    }
}
