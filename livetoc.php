<?php

/*
 * Live TOC. Плагин
 * 
 * @package     LiveTOC
 * @subpackage  Plugin
 * @link        TODO
 * @license     GNU/GPL v2
 */

// убираем прямой доступ
defined('_JEXEC') or die('Restricted access');

class PlgContentLiveTOC extends JPlugin {

    public function onContentPrepare($context, &$article, &$params, $page = 0) {
        if ($context == 'com_content.article' || $context == 'com_content.featured' || $context == 'com_content.category') {
            $article->text = $article->text
                    . '<h2 onclick="$(\'#block01\').slideToggle();">Заголовок статьи</h2>'
                    . '<div id="block01">'
                    . '<p><b>Правило.</b> Ё должна использоваться: в случаях возможных разночтений; в словарях; в книгах для изучающих русский язык (т. е. детей и иностранцев); для правильного прочтения редких топонимов, названий или фамилий. Во всех остальных случаях наличие буквы ё только затрудняет чтение. Она плохо выглядит, зато хорошо звучит.</p>'
                    . '<h3 onclick="$(\'#block0101\').slideToggle();">Подзаголовок статьи</h3>'
                    . '<div id="block0101">Текст блока</div>'
                    . '<h3 onclick="$(\'#block0102\').slideToggle();">Подзаголовок 2 статьи</h3>'
                    . '<div id="block0102">Текст ещё одного блока.</div>'
                    . '</div>'
            ;
        }
        return true;
    }

}
