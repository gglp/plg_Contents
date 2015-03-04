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
        if ($context == 'com_content.article') {

            /* Порядок действий:
             * 1. Найти все теги <h...>
             * 2. Определить уровень тега
             * 3. Найти закрывающий тег </h...> соответствующего уровня
             * 4. Найти границы сворачиваемого блока: или следующий немладший тег или конец текста
             * 5. Вставки текста надо делать с конца, т.к. они смещают последующий текст
             */

            $offset = 0; // Смещение от начала текста
            $headerPosition = JString::strpos($article->text, "<h", $offset);

            $i = 1; // Счётчик блоков
            $existsTextBlocks = FALSE; // Флаг по наличию текстовых блоков

            while ($headerPosition !== FALSE) {
                $headerLevel = JString::substr($article->text, $headerPosition + 2, 1);         // Определили уровень тега
                $headerClose = (int) JString::strpos($article->text, "</h" . $headerLevel, $headerPosition);  // Определили позицию закрывающего тега
                // Найдём подходящий граничный тег, чтобы закрыть блок
                $nextHeaderFinded = FALSE; // Показывает, что следующий блок неменьшего уровня найден
                $blockIsText = TRUE; // Показывает, что блок текстовый без вложенных заголовков
                $nextHeaderPosition = $headerPosition + 1;
                while ($nextHeaderFinded === FALSE) {
                    $endOfBlock = JString::strpos($article->text, "<h", $nextHeaderPosition);      // Нашли следующий тег
                    if ($endOfBlock === FALSE) {
                        // Добавим закрывающий див в конце материала
                        $article->text .= "</div>";
                        $nextHeaderFinded = TRUE;
                    } else {
                        if ((int) JString::substr($article->text, $endOfBlock + 2, 1) <= $headerLevel) {
                            // Добавить закрывающий див перед найденным тегом заголовка
                            $article->text = JString::substr_replace($article->text, "</div>", $endOfBlock, 0);
                            $nextHeaderFinded = TRUE;
                        } else {
                            $nextHeaderPosition++;
                            $blockIsText = FALSE;
                        }
                    }
                }

                // Добавим начало блока после заголовка
                if ($blockIsText) {
                    $article->text = JString::substr_replace($article->text, "<div id=\"livetoc_block$i\" class=\"livetoc_text\">", $headerClose + 5, 0);
                    $existsTextBlocks = TRUE;
                } else {
                    $article->text = JString::substr_replace($article->text, "<div id=\"livetoc_block$i\">", $headerClose + 5, 0);
                }
                // Добавим скрипт в заголовок
                $article->text = JString::substr_replace($article->text, " onclick=\"jQuery('#livetoc_block$i').slideToggle();\"", $headerPosition + 3, 0);

                // Найдём следующий заголовок
                $offset = $headerPosition + 1;
                $i++;
                $headerPosition = JString::strpos($article->text, "<h", $offset);
            }
            
            // Если существуют текстовые блоки, добавить div с ссылками свернуть / развернуть
            if ($existsTextBlocks) {
                $article->text =
                        "<div style=\"float:right\" class=\"well\">"
                        ."<a href=\"#\" onclick=\"jQuery('.livetoc_text').show();\">Развернуть</a><br/>"
                        ."<a href=\"#\" onclick=\"jQuery('.livetoc_text').hide();\">Свернуть</a>"
                        ."</div>"
                        .$article->text
                    ;
            }
        }
        return true;
    }

}
